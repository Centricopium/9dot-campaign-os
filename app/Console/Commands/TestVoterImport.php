<?php

namespace App\Console\Commands;

use App\Imports\VotersImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class TestVoterImport extends Command
{
    protected $signature = 'import:test {file}';

    protected $description = 'Test Election Commission Excel Import';

    public function handle(): int
    {
        $file = $this->argument('file');

        if (! file_exists($file)) {
            $this->error("File not found: {$file}");

            return self::FAILURE;
        }

        $import = app(VotersImport::class);

        Excel::import($import, $file);

        $result = $import->result();

        $this->newLine();

        $this->info('========== IMPORT SUMMARY ==========');

        $this->line('Total Rows      : ' . $result->totalRows);
        $this->line('Imported        : ' . $result->imported);
        $this->line('Updated         : ' . $result->updated);
        $this->line('Skipped         : ' . $result->skipped);
        $this->line('Duplicate EPIC  : ' . $result->duplicateEpic);
        $this->line('New Houses      : ' . $result->newHouses);
        $this->line('Errors          : ' . $result->errors);

        if (! empty($result->messages)) {
            $this->newLine();
            $this->warn('Errors');

            foreach ($result->messages as $message) {
                $this->line('- ' . $message);
            }
        }

        return self::SUCCESS;
    }
}