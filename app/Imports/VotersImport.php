<?php

namespace App\Imports;

use App\Services\Import\ImportResult;
use App\Services\Import\VoterImportService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VotersImport implements ToCollection, WithHeadingRow
{
    public ImportResult $result;

    public function __construct(
        protected VoterImportService $service,
    ) {
        $this->result = new ImportResult();
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {

            $this->result->totalRows++;

            $this->service->import(
                $row->toArray(),
                $this->result
            );
        }
    }

    public function result(): ImportResult
    {
        return $this->result;
    }
}