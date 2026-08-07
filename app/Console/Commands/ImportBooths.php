<?php

namespace App\Console\Commands;

use App\Imports\BoothsImport;
use App\Services\Import\BoothImportService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

#[Signature('booth:import {file} {constituency}')]
#[Description('Import Booth Master Excel')]
class ImportBooths extends Command
{
    public function handle(): int
    {
        $file = $this->argument('file');
        $constituencyId = (int) $this->argument('constituency');

        if (! file_exists($file)) {
            $this->error("File not found: {$file}");

            return self::FAILURE;
        }

        $this->info('=================================');
        $this->info('Booth Import Started');
        $this->info('=================================');
        $this->line("File : {$file}");
        $this->line("Constituency ID : {$constituencyId}");
        $this->newLine();

        try {

            Excel::import(
                new BoothsImport(
                    app(BoothImportService::class),
                    $constituencyId
                ),
                $file
            );

            $this->newLine();
            $this->info('✅ Booths Imported Successfully.');

            return self::SUCCESS;

        } catch (\Throwable $e) {

            $this->newLine();
            $this->error('❌ Import Failed!');
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}