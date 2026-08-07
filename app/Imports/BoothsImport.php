<?php

namespace App\Imports;

use App\Services\Import\BoothImportService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class BoothsImport implements ToCollection
{
    public function __construct(
        protected BoothImportService $service,
        protected int $constituencyId,
    ) {}

    public function collection(Collection $rows): void
    {
        // Header row skip
        $rows->skip(1)->each(function ($row) {

            $this->service->import(
                $row->toArray(),
                $this->constituencyId
            );

        });
    }
}