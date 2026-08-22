<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Filament\Resources\CampaignTasks\CampaignTaskResource;
use App\Models\CampaignTask;
use App\Models\Constituency;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use UnitEnum;

class FieldOperations extends Page
{
    use AuthorizesPagePermission;

    protected static string $requiredPermission = 'campaign_task.view';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static string|UnitEnum|null $navigationGroup = 'Campaign Operations';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Field Operations';

    protected static ?string $title = 'Campaign Field Operations';

    protected string $view = 'filament.pages.field-operations';

    public string $constituencyId = '';

    public string $status = '';

    public string $priority = '';

    public function mount(): void
    {
        $user = auth()->user();

        if ($user?->isAssemblyAdmin() && ! $user->isSuperAdmin() && $user->constituency_id) {
            $this->constituencyId = (string) $user->constituency_id;
        }
    }

    public function resetFilters(): void
    {
        $user = auth()->user();
        $this->constituencyId = $user?->isAssemblyAdmin() && ! $user->isSuperAdmin()
            ? (string) ($user->constituency_id ?? '')
            : '';
        $this->status = '';
        $this->priority = '';
    }

    public function getConstituenciesProperty(): Collection
    {
        return Constituency::query()->orderBy('name')->get(['id', 'name']);
    }

    public function getTasksProperty(): Collection
    {
        return $this->filteredQuery()
            ->with(['constituency', 'village', 'booth', 'assignee'])
            ->orderByRaw("CASE priority WHEN 'Critical' THEN 1 WHEN 'High' THEN 2 WHEN 'Medium' THEN 3 ELSE 4 END")
            ->orderBy('due_at')
            ->limit(50)
            ->get();
    }

    public function getStatsProperty(): array
    {
        $base = $this->filteredQuery();

        return [
            'total' => (clone $base)->count(),
            'active' => (clone $base)->whereIn('status', ['Assigned', 'In Progress', 'On Hold', 'Rejected'])->count(),
            'completed' => (clone $base)->where('status', 'Completed')->count(),
            'approved' => (clone $base)->where('status', 'Approved')->count(),
            'overdue' => (clone $base)->overdue()->count(),
            'critical' => (clone $base)->where('priority', 'Critical')->whereNotIn('status', ['Approved', 'Cancelled'])->count(),
        ];
    }

    public function getWorkloadProperty(): Collection
    {
        return $this->filteredQuery()
            ->whereNotNull('assigned_to')
            ->whereNotIn('status', ['Approved', 'Cancelled'])
            ->selectRaw('assigned_to, COUNT(*) as task_count, SUM(CASE WHEN due_at < ? THEN 1 ELSE 0 END) as overdue_count', [now()])
            ->with('assignee:id,name,employee_code')
            ->groupBy('assigned_to')
            ->orderByDesc('task_count')
            ->limit(8)
            ->get();
    }

    public function createTaskUrl(): string
    {
        return CampaignTaskResource::getUrl('create');
    }

    public function taskUrl(CampaignTask $task): string
    {
        return CampaignTaskResource::getUrl('view', ['record' => $task]);
    }

    private function filteredQuery(): Builder
    {
        return CampaignTaskResource::getEloquentQuery()
            ->when($this->constituencyId, fn (Builder $query, string $id): Builder => $query->where('constituency_id', $id))
            ->when($this->status, fn (Builder $query, string $status): Builder => $query->where('status', $status))
            ->when($this->priority, fn (Builder $query, string $priority): Builder => $query->where('priority', $priority));
    }
}
