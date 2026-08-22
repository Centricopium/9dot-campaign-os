<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Filament\Resources\CampaignEvents\CampaignEventResource;
use App\Models\CampaignEvent;
use App\Models\Constituency;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use UnitEnum;

class EventTourPlanner extends Page
{
    use AuthorizesPagePermission;

    protected static string $requiredPermission = 'campaign_event.view';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|UnitEnum|null $navigationGroup = 'Campaign Operations';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Event Planner';

    protected static ?string $title = 'Event, Rally & Candidate Tour Planner';

    protected string $view = 'filament.pages.event-tour-planner';

    public string $constituencyId = '';

    public string $type = '';

    public function mount(): void
    {
        $u = auth()->user();
        if ($u?->isAssemblyAdmin() && ! $u->isSuperAdmin()) {
            $this->constituencyId = (string) $u->constituency_id;
        }
    }

    public function getConstituenciesProperty(): Collection
    {
        return Constituency::query()->orderBy('name')->get(['id', 'name']);
    }

    private function query(): Builder
    {
        return CampaignEvent::query()->when($this->constituencyId, fn (Builder $q, $id) => $q->where('constituency_id', $id))->when($this->type, fn (Builder $q, $type) => $q->where('event_type', $type));
    }

    public function getEventsProperty(): Collection
    {
        return $this->query()->with(['constituency', 'village', 'candidate', 'coordinator'])->where('starts_at', '>=', now()->subDay())->orderBy('starts_at')->limit(50)->get();
    }

    public function getStatsProperty(): array
    {
        $q = $this->query();

        return ['upcoming' => (clone $q)->where('starts_at', '>=', now())->whereNotIn('status', ['Completed', 'Cancelled'])->count(), 'today' => (clone $q)->whereDate('starts_at', today())->count(), 'tours' => (clone $q)->where('event_type', 'Candidate Tour')->where('starts_at', '>=', now())->count(), 'permissions' => (clone $q)->where('permission_status', 'Pending')->count(), 'expected' => (int) (clone $q)->whereDate('starts_at', '>=', today())->sum('expected_attendance'), 'completed' => (clone $q)->where('status', 'Completed')->count()];
    }

    public function createUrl(): string
    {
        return CampaignEventResource::getUrl('create');
    }

    public function editUrl(CampaignEvent $e): string
    {
        return CampaignEventResource::getUrl('edit', ['record' => $e]);
    }

    public function exportPdf()
    {
        abort_unless(auth()->user()?->can('campaign_event.export'), 403);

        $events = $this->query()
            ->with(['constituency', 'village', 'booth', 'candidate', 'coordinator'])
            ->withCount('teamMembers')
            ->orderBy('starts_at')
            ->get();

        $stats = [
            'total' => $events->count(),
            'upcoming' => $events->where('starts_at', '>=', now())->whereNotIn('status', ['Completed', 'Cancelled'])->count(),
            'completed' => $events->where('status', 'Completed')->count(),
            'permissions_pending' => $events->where('permission_status', 'Pending')->count(),
            'expected_attendance' => (int) $events->sum('expected_attendance'),
            'actual_attendance' => (int) $events->sum('actual_attendance'),
            'estimated_budget' => (float) $events->sum('estimated_budget'),
            'actual_expense' => (float) $events->sum('actual_expense'),
        ];

        $constituency = $this->constituencyId
            ? $this->constituencies->firstWhere('id', (int) $this->constituencyId)
            : null;

        $pdf = Pdf::loadView('reports.event-planner-pdf', [
            'events' => $events,
            'stats' => $stats,
            'assemblyLabel' => $constituency?->name ?? 'All Assemblies',
            'typeLabel' => $this->type ?: 'All Event Types',
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'event-planner-'.now()->format('Y-m-d-His').'.pdf',
        );
    }
}
