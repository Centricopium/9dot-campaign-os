<?php

namespace App\Jobs;

use App\Services\Security\SystemBackupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateSystemBackup implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;

    public int $timeout = 7200;

    public int $tries = 1;

    public function __construct(public readonly ?int $userId = null) {}

    public function handle(SystemBackupService $service): void
    {
        $service->create($this->userId);
    }
}
