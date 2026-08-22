<?php

namespace App\Filament\Pages;

use App\Jobs\ProcessVoterImport;
use App\Models\Constituency;
use App\Models\VoterImportBatch;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class ImportProgressCentre extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPathRoundedSquare;

    protected static string|UnitEnum|null $navigationGroup = 'Data Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Import Progress';

    protected static ?string $title = 'Voter Import Progress Centre';

    protected string $view = 'filament.pages.import-progress-centre';

    public string $constituencyId = '';

    public function mount(): void
    {
        $u = auth()->user();
        if ($u?->isAssemblyAdmin() && ! $u->isSuperAdmin()) {
            $this->constituencyId = (string) $u->constituency_id;
        }
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('import.voter') ?? false;
    }

    public function getConstituenciesProperty(): Collection
    {
        return Constituency::query()->orderBy('name')->get(['id', 'name']);
    }

    public function getBatchesProperty(): Collection
    {
        return VoterImportBatch::query()->with(['constituency', 'user'])->when($this->constituencyId, fn ($q, $id) => $q->where('constituency_id', $id))->latest()->limit(50)->get();
    }

    public function getStatsProperty(): array
    {
        $q = VoterImportBatch::query()->when($this->constituencyId, fn ($q, $id) => $q->where('constituency_id', $id));

        return ['total' => (clone $q)->count(), 'running' => (clone $q)->whereIn('status', ['Queued', 'Processing'])->count(), 'completed' => (clone $q)->where('status', 'Completed')->count(), 'failed' => (clone $q)->where('status', 'Failed')->count(), 'imported' => (int) (clone $q)->sum('imported_rows'), 'skipped' => (int) (clone $q)->sum('skipped_rows')];
    }

    public function retryImport(int $id): void
    {
        $b = VoterImportBatch::query()->findOrFail($id);
        abort_unless($b->status === 'Failed' && Storage::disk('local')->exists($b->file_path), 422);
        $retry = VoterImportBatch::create([
            'constituency_id' => $b->constituency_id,
            'user_id' => auth()->id(),
            'file_name' => $b->file_name,
            'file_path' => $b->file_path,
            'status' => 'Queued',
            'total_rows' => $b->total_rows,
        ]);
        ProcessVoterImport::dispatch($retry->file_path, $retry->constituency_id, $retry->id);
        Notification::make()->title('Import retry queued')->success()->send();
    }

    public function downloadErrors(int $id): StreamedResponse
    {
        $b = VoterImportBatch::query()->findOrFail($id);
        $rows = $b->errors ?? [];

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Excel Row', 'Reason']);
            foreach ($rows as $row) {
                fputcsv($out, [$row['row'] ?? '', $row['reason'] ?? '']);
            }fclose($out);
        }, 'voter-import-'.$b->id.'-errors.csv', ['Content-Type' => 'text/csv']);
    }
}
