<?php

namespace App\Imports;

use App\Models\Booth;
use App\Models\House;
use App\Models\Voter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VotersImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;

    public int $skipped = 0;

    public array $errors = [];

    public function __construct(
        protected int $constituencyId
    ) {
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {

            $excelRow = $index + 2;

            try {

                DB::transaction(function () use ($row, $excelRow) {

                    $partNo = trim(
                        (string) ($row['part_no'] ?? '')
                    );

                    $houseNo = trim(
                        (string) ($row['house_no'] ?? '')
                    );

                    $epicNo = trim(
                        (string) ($row['idcard_no'] ?? '')
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Basic Validation
                    |--------------------------------------------------------------------------
                    */

                    if ($partNo === '' || $houseNo === '') {

                        $this->skipped++;

                        $this->errors[] = [
                            'row' => $excelRow,
                            'reason' => 'PART_NO or HOUSE_NO is missing.',
                        ];

                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | 1. Find Booth using PART_NO
                    |--------------------------------------------------------------------------
                    */

                    $booth = Booth::where('part_no', $partNo)
                        ->first();

                    if (! $booth) {

                        $this->skipped++;

                        $this->errors[] = [
                            'row' => $excelRow,
                            'reason' => "Booth not found for PART_NO: {$partNo}",
                        ];

                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | 2. Validate Booth belongs to selected Constituency
                    |--------------------------------------------------------------------------
                    |
                    | Voter Excel:
                    |
                    | Selected Constituency
                    |        ↓
                    | PART_NO
                    |        ↓
                    | Booth
                    |        ↓
                    | Village
                    |        ↓
                    | Constituency
                    |
                    */

                    $booth->loadMissing(
                        'village.constituency'
                    );

                    $boothConstituencyId =
                        $booth->village?->constituency?->id;

                    if (
                        ! $boothConstituencyId ||
                        (int) $boothConstituencyId !== (int) $this->constituencyId
                    ) {

                        $this->skipped++;

                        $this->errors[] = [
                            'row' => $excelRow,
                            'reason' =>
                                "PART_NO {$partNo} belongs to a different constituency.",
                        ];

                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | 3. Prevent Duplicate EPIC
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $epicNo !== '' &&
                        Voter::where('epic_no', strtoupper($epicNo))->exists()
                    ) {

                        $this->skipped++;

                        $this->errors[] = [
                            'row' => $excelRow,
                            'reason' =>
                                "Duplicate EPIC: {$epicNo}",
                        ];

                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | 4. Find or Create House
                    |--------------------------------------------------------------------------
                    |
                    | Same Booth + House No = Same House
                    |
                    */

                    $house = House::firstOrCreate(
                        [
                            'booth_id' => $booth->id,
                            'house_no' => $houseNo,
                        ],
                        [
                            'is_active' => true,
                            'is_verified' => false,
                        ]
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | 5. Create Voter
                    |--------------------------------------------------------------------------
                    */

                    Voter::create([
                        'house_id' => $house->id,

                        'serial_no' => $this->nullableString(
                            $row['slnoinpart'] ?? null
                        ),

                        'part_no' => $partNo,

                        'epic_no' => $epicNo !== ''
                            ? strtoupper($epicNo)
                            : null,

                        'name' => $this->buildName($row),

                        'father_husband_name' => $this->nullableString(
                            $row['eng_m_name'] ?? null
                        ),

                        'gender' => $this->mapGender(
                            $row['sex'] ?? null
                        ),

                        'age' => $this->nullableInt(
                            $row['age'] ?? null
                        ),

                        'mobile' => $this->nullableString(
                            $row['contactno'] ?? null
                        ),

                        'caste' => $this->nullableString(
                            $row['ecast'] ?? null
                        ),

                        'is_active' => true,
                    ]);

                    $this->imported++;
                });

            } catch (\Throwable $e) {

                $this->skipped++;

                $this->errors[] = [
                    'row' => $excelRow,
                    'reason' => $e->getMessage(),
                ];
            }
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
        return is_numeric($value)
            ? (int) $value
            : null;
    }
}