<?php

namespace App\Jobs;

use App\Imports\VotersImport;
use App\Models\VoterImportBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Throwable;

class ProcessVoterImport implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 7_200;

    public int $tries = 1;

    public function __construct(
        public readonly string $path,
        public readonly int $constituencyId,
        public readonly ?int $batchId = null,
    ) {}

    public function handle(): void
    {
        $batch = $this->batchId ? VoterImportBatch::query()->find($this->batchId) : null;
        if (! Storage::disk('local')->exists($this->path)) {
            throw new \RuntimeException('The uploaded voter import file is no longer available.');
        }

        $sourcePath = $this->prepareQueueSource($batch);

        if ($batch) {
            $batch->update(['status' => 'Processing', 'started_at' => $batch->started_at ?: now(), 'failure_message' => null]);
            if (! $batch->total_rows) {
                $sourceFile = Storage::disk('local')->path($sourcePath);
                $info = IOFactory::createReaderForFile($sourceFile)->listWorksheetInfo($sourceFile);
                $batch->update(['total_rows' => max(0, (int) ($info[0]['totalRows'] ?? 1) - 1)]);
            }
        }

        $import = new VotersImport($this->constituencyId, $sourcePath, $this->batchId);

        Excel::queueImport($import, Storage::disk('local')->path($sourcePath));

        Log::info('Voter import completed.', [
            'constituency_id' => $this->constituencyId,
            'file' => $sourcePath,
            'mode' => 'queued_chunks',
            'chunk_size' => $import->chunkSize(),
        ]);
    }

    /**
     * CSV files are streamed by the reader and are substantially more reliable
     * than repeatedly reading a large XLSX workbook on shared hosting.
     */
    private function prepareQueueSource(?VoterImportBatch $batch): string
    {
        if (strtolower(pathinfo($this->path, PATHINFO_EXTENSION)) !== 'xlsx') {
            return $this->path;
        }

        $csvPath = preg_replace('/\\.xlsx$/i', '.csv', $this->path);
        $disk = Storage::disk('local');

        if (! $disk->exists($csvPath)) {
            $sourceFile = $disk->path($this->path);
            $csvFile = $disk->path($csvPath);
            $reader = IOFactory::createReaderForFile($sourceFile);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($sourceFile);

            $writer = new Csv($spreadsheet);
            $writer->setDelimiter(',');
            $writer->setEnclosure('"');
            $writer->setUseBOM(true);
            $writer->save($csvFile);
            $spreadsheet->disconnectWorksheets();
        }

        if ($batch) {
            $batch->update([
                'file_name' => basename($csvPath),
                'file_path' => $csvPath,
            ]);
        }

        return $csvPath;
    }

    public function failed(Throwable $exception): void
    {
        if ($this->batchId) {
            VoterImportBatch::query()->whereKey($this->batchId)->update(['status' => 'Failed', 'failure_message' => $exception->getMessage(), 'completed_at' => now()]);
        }
        Log::error('Voter import failed.', [
            'constituency_id' => $this->constituencyId,
            'file' => $this->path,
            'message' => $exception->getMessage(),
        ]);

        // Keep the source file so a failed import can be inspected or resumed safely.
    }
}
