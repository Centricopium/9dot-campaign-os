<?php

namespace App\Console\Commands;

use App\Imports\VillagesImport;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

#[Signature('village:import {file} {constituency}')]
#[Description('Import Village Master')]
class ImportVillages extends Command
{
    public function handle(): int
    {
        $file = $this->argument('file');
        $constituencyId = (int) $this->argument('constituency');

        if (! file_exists($file)) {
            $this->error("File not found: {$file}");
            return self::FAILURE;
        }

        Excel::import(
            new VillagesImport(
                app(\App\Services\Import\VillageImportService::class),
                $constituencyId
            ),
            $file
        );

        $this->info('✅ Village Import Completed.');

        return self::SUCCESS;
    }
}