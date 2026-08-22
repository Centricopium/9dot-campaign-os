<?php

namespace App\Console\Commands;

use App\Models\SystemBackup;
use App\Services\Security\SystemBackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class RestoreSystemBackupCommand extends Command
{
    protected $signature = 'backup:restore {backup : Backup record ID} {--confirm= : Exact RESTORE-ID confirmation}';

    protected $description = 'Verify and restore a private database backup with a pre-restore safety backup';

    public function handle(SystemBackupService $service): int
    {
        $id = (int) $this->argument('backup');
        if ($this->option('confirm') !== "RESTORE-{$id}") {
            $this->error("Confirmation required: --confirm=RESTORE-{$id}");

            return self::FAILURE;
        }$backup = SystemBackup::query()->where('status', 'Completed')->findOrFail($id);
        $this->warn('Creating a safety backup before restore...');
        $service->create();
        Artisan::call('down');
        try {
            $service->restore($backup);
            $this->info('Database restore completed.');

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        } finally {
            Artisan::call('up');
        }
    }
}
