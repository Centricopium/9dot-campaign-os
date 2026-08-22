<?php

namespace App\Imports;

use App\Models\Booth;
use App\Models\House;
use App\Models\Voter;
use App\Models\VoterImportBatch;
use App\Services\House\HouseHeadService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\ImportFailed;

class VotersImport implements ShouldQueue, ToCollection, WithChunkReading, WithEvents, WithHeadingRow
{
    use RegistersEventListeners;

    private const MAX_RECORDED_ERRORS = 1_000;

    public int $imported = 0;

    public int $skipped = 0;

    public array $errors = [];

    private array $houseIds = [];

    private int $processedRows = 0;

    public int $timeout = 900;

    public int $tries = 2;

    public function __construct(
        protected int $constituencyId,
        protected ?string $sourcePath = null,
        protected ?int $batchId = null,
    ) {}

    public function collection(Collection $rows): void
    {
        $beforeImported = $this->imported;
        $beforeSkipped = $this->skipped;
        $beforeErrors = count($this->errors);
        $chunkStart = $this->processedRows + 2;
        $this->processedRows += $rows->count();

        $partNumbers = $rows->pluck('part_no')->map(fn ($value) => trim((string) $value))->filter()->unique();
        $booths = Booth::query()
            ->whereIn('part_no', $partNumbers)
            ->whereHas('village', fn ($query) => $query->where('constituency_id', $this->constituencyId))
            ->get()
            ->keyBy(fn (Booth $booth) => (string) $booth->part_no);

        $epicNumbers = $rows->pluck('idcard_no')->map(fn ($value) => strtoupper(trim((string) $value)))->filter()->unique();
        $existingEpics = Voter::query()->whereIn('epic_no', $epicNumbers)->pluck('epic_no')->flip()->all();
        $seenEpics = $existingEpics;
        $serialNumbers = $rows->pluck('slnoinpart')->map(fn ($value) => trim((string) $value))->filter()->unique();
        $seenSerials = Voter::query()
            ->whereIn('part_no', $partNumbers)
            ->whereIn('serial_no', $serialNumbers)
            ->get(['part_no', 'serial_no'])
            ->mapWithKeys(fn (Voter $voter) => [$voter->part_no.'|'.$voter->serial_no => true])
            ->all();
        $now = now();
        $inserts = [];

        DB::transaction(function () use ($rows, $chunkStart, $booths, &$seenEpics, &$seenSerials, &$inserts, $now): void {
            foreach ($rows as $index => $row) {
                $excelRow = $chunkStart + $index;
                $partNo = trim((string) ($row['part_no'] ?? ''));
                $houseNo = trim((string) ($row['house_no'] ?? ''));
                $epicNo = strtoupper(trim((string) ($row['idcard_no'] ?? '')));
                $serialNo = trim((string) ($row['slnoinpart'] ?? ''));

                if ($partNo === '' || $houseNo === '') {
                    $this->skip($excelRow, 'PART_NO or HOUSE_NO is missing.');

                    continue;
                }

                $booth = $booths->get($partNo);
                if (! $booth) {
                    $this->skip($excelRow, "Booth not found in selected constituency for PART_NO: {$partNo}");

                    continue;
                }

                if ($epicNo !== '' && isset($seenEpics[$epicNo])) {
                    $this->skip($excelRow, "Duplicate EPIC: {$epicNo}");

                    continue;
                }

                $serialKey = $partNo.'|'.$serialNo;
                if ($serialNo === '' && $epicNo === '') {
                    $this->skip($excelRow, 'EPIC and serial number are both missing.');

                    continue;
                }

                if ($serialNo !== '' && isset($seenSerials[$serialKey])) {
                    $this->skip($excelRow, "Duplicate PART_NO and serial number: {$serialKey}");

                    continue;
                }

                $houseKey = $booth->id.'|'.$houseNo;
                if (! isset($this->houseIds[$houseKey])) {
                    $this->houseIds[$houseKey] = House::query()->firstOrCreate(
                        ['booth_id' => $booth->id, 'house_no' => $houseNo],
                        ['is_active' => true, 'is_verified' => false],
                    )->id;
                }

                $inserts[] = [
                    'house_id' => $this->houseIds[$houseKey],
                    'serial_no' => $serialNo !== '' ? $serialNo : null,
                    'part_no' => $partNo,
                    'epic_no' => $epicNo !== '' ? $epicNo : null,
                    'name' => $this->buildName($row),
                    'father_husband_name' => $this->nullableString($row['eng_m_name'] ?? null),
                    'gender' => $this->mapGender($row['sex'] ?? null),
                    'age' => $this->nullableInt($row['age'] ?? null),
                    'mobile' => $this->nullableString($row['contactno'] ?? null),
                    'caste' => $this->nullableString($row['ecast'] ?? null),
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if ($epicNo !== '') {
                    $seenEpics[$epicNo] = true;
                }

                if ($serialNo !== '') {
                    $seenSerials[$serialKey] = true;
                }
            }

            foreach (array_chunk($inserts, 500) as $batch) {
                Voter::query()->insert($batch);
                $this->imported += count($batch);
            }
        });

        if ($this->batchId) {
            $newErrors = array_slice($this->errors, $beforeErrors);
            DB::transaction(function () use ($rows, $beforeImported, $beforeSkipped, $newErrors): void {
                $batch = VoterImportBatch::query()->lockForUpdate()->find($this->batchId);
                if (! $batch) {
                    return;
                }
                $errors = array_slice(array_merge($batch->errors ?? [], $newErrors), 0, self::MAX_RECORDED_ERRORS);
                $batch->update(['processed_rows' => $batch->processed_rows + $rows->count(), 'imported_rows' => $batch->imported_rows + ($this->imported - $beforeImported), 'skipped_rows' => $batch->skipped_rows + ($this->skipped - $beforeSkipped), 'errors' => $errors]);
            });
        }
    }

    protected function skip(int $row, string $reason): void
    {
        $this->skipped++;
        $this->recordError(compact('row', 'reason'));
    }

    /**
     * Keep PhpSpreadsheet's memory use bounded for large electoral rolls.
     */
    public function chunkSize(): int
    {
        return 1_000;
    }

    public function afterImport(AfterImport $event): void
    {
        $updatedHouseHeads = app(HouseHeadService::class)
            ->backfill($this->constituencyId);

        if ($this->sourcePath && Storage::disk('local')->exists($this->sourcePath)) {
            Storage::disk('local')->delete($this->sourcePath);
        }

        Log::info('Queued voter import completed.', [
            'constituency_id' => $this->constituencyId,
            'file' => $this->sourcePath,
            'updated_house_heads' => $updatedHouseHeads,
        ]);
        if ($this->batchId) {
            VoterImportBatch::query()->whereKey($this->batchId)->update(['status' => 'Completed', 'completed_at' => now()]);
        }
    }

    public function importFailed(ImportFailed $event): void
    {
        if ($this->batchId) {
            VoterImportBatch::query()->whereKey($this->batchId)->update(['status' => 'Failed', 'failure_message' => $event->getException()->getMessage(), 'completed_at' => now()]);
        }
        Log::error('Queued voter import chunk failed.', [
            'constituency_id' => $this->constituencyId,
            'file' => $this->sourcePath,
            'message' => $event->getException()->getMessage(),
        ]);
    }

    protected function recordError(array $error): void
    {
        if (count($this->errors) < self::MAX_RECORDED_ERRORS) {
            $this->errors[] = $error;
        }
    }

    protected function buildName(Collection|array $row): string
    {
        $first = $this->nullableString(
            $row['eng_f_name'] ?? null
        );

        $surname = $this->nullableString(
            $row['eng_surname'] ?? null
        );

        return trim("{$first} {$surname}");
    }

    protected function mapGender(mixed $value): ?string
    {
        $value = strtoupper(
            trim((string) $value)
        );

        return match ($value) {
            'M', 'MALE' => 'Male',
            'F', 'FEMALE' => 'Female',
            default => $value !== '' ? 'Other' : null,
        };
    }

    protected function nullableString(mixed $value): ?string
    {
        $value = trim(
            (string) $value
        );

        return $value !== ''
            ? $value
            : null;
    }

    protected function nullableInt(mixed $value): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }

        $value = (int) $value;

        return $value >= 0 && $value <= 255 ? $value : null;
    }
}
