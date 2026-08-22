<?php

namespace App\Console\Commands;

use App\Services\Security\SystemBackupService;
use Illuminate\Console\Command;

class CreateSystemBackupCommand extends Command
{
    protected $signature = 'backup:create';

    protected $description = 'Create a verified private database backup';

    public function handle(SystemBackupService $service): int
    {
        $backup = $service->create();
        $this->info("Backup completed: {$backup->file_name} ({$backup->human_size})");

        return self::SUCCESS;
    }
}
