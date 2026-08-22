<?php

namespace App\Filament\Pages;

use App\Jobs\CreateSystemBackup;
use App\Models\SystemBackup;
use App\Services\Security\SystemBackupService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class BackupRestoreCentre extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 21;

    protected static ?string $navigationLabel = 'Backup & Restore';

    protected static ?string $title = 'Automated Backup & Restore Centre';

    protected string $view = 'filament.pages.backup-restore-centre';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('backup.view') ?? false;
    }

    public function getBackupsProperty()
    {
        return SystemBackup::query()->with('initiator')->latest()->limit(50)->get();
    }

    public function getStatsProperty(): array
    {
        $q = SystemBackup::query();
        $latest = (clone $q)->where('status', 'Completed')->latest()->first();

        return ['total' => (clone $q)->where('status', 'Completed')->count(), 'failed' => (clone $q)->where('status', 'Failed')->count(), 'running' => (clone $q)->whereIn('status', ['Queued', 'Running'])->count(), 'size' => (int) (clone $q)->where('status', 'Completed')->sum('file_size'), 'latest' => $latest?->completed_at?->diffForHumans() ?? 'Never', 'verified' => $latest?->verified_at ? 'Verified' : 'Not available'];
    }

    public function createBackup(): void
    {
        abort_unless(auth()->user()?->can('backup.create'), 403);
        CreateSystemBackup::dispatch(auth()->id());
        Notification::make()->title('Private backup queued')->body('Keep the queue worker running.')->success()->send();
    }

    public function verifyBackup(int $id, SystemBackupService $service): void
    {
        abort_unless(auth()->user()?->can('backup.view'), 403);
        $ok = $service->verify(SystemBackup::query()->findOrFail($id));
        Notification::make()->title($ok ? 'Backup verified' : 'Verification failed')->color($ok ? 'success' : 'danger')->send();
    }
}
