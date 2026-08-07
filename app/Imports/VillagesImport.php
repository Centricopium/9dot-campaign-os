<?php

namespace App\Imports;

use App\Services\Import\VillageImportService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class VillagesImport implements ToCollection
{
    public function __construct(
        protected VillageImportService $service,
        protected int $constituencyId,
    ) {}

    public function collection(Collection $rows): void
    {
        $rows->skip(1)->each(function ($row) {

            $this->service->import(
                $row->toArray(),
                $this->constituencyId
            );

        });
    }
}