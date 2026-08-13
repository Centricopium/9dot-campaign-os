<?php

namespace App\Imports;

use App\Services\Import\BoothImportService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class BoothsImport implements ToCollection
{
    public int $processedRows = 0;

    public function __construct(
        protected BoothImportService $service,
        protected int $constituencyId,
    ) {
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $data = $row->toArray();
            $boothNo = $data[0] ?? null;

            // Skip blank rows and the header row safely.
            if (blank($boothNo) || ! is_numeric($boothNo)) {
                continue;
            }

            $this->service->import($data, $this->constituencyId);

            $this->processedRows++;
        }
    }
}