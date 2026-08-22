<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Filament\Resources\CampaignIssues\CampaignIssueResource;
use App\Models\CampaignIssue;
use App\Models\Constituency;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use UnitEnum;

class IssueTracker extends Page
{
    use AuthorizesPagePermission;

    protected static string $requiredPermission = 'campaign_issue.view';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static string|UnitEnum|null $navigationGroup = 'Campaign Operations';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Issue Tracker';

    protected static ?string $title = 'Public Issue & Grievance Tracker';

    protected string $view = 'filament.pages.issue-tracker';

    public string $constituencyId = '';

    public string $status = '';

    public string $category = '';

    public function mount(): void
    {
        $user = auth()->user();
        if ($user?->isAssemblyAdmin() && ! $user->isSuperAdmin()) {
            $this->constituencyId = (string) $user->constituency_id;
        }
    }

    public function resetFilters(): void
    {
        $this->mount();
        $this->status = '';
        $this->category = '';
    }

    public function getConstituenciesProperty(): Collection
    {
        return Constituency::query()->orderBy('name')->get(['id', 'name']);
    }

    public function getIssuesProperty(): Collection
    {
        return $this->query()->with(['constituency', 'village', 'assignee'])->orderByRaw("CASE priority WHEN 'Critical' THEN 1 WHEN 'High' THEN 2 WHEN 'Medium' THEN 3 ELSE 4 END")->orderBy('due_at')->limit(50)->get();
    }

    public function getStatsProperty(): array
    {
        $q = $this->query();

        return ['total' => (clone $q)->count(), 'open' => (clone $q)->whereIn('status', ['Open', 'Assigned', 'In Progress', 'Waiting', 'Reopened'])->count(), 'escalated' => (clone $q)->where('status', 'Escalated')->count(), 'resolved' => (clone $q)->where('status', 'Resolved')->count(), 'closed' => (clone $q)->where('status', 'Closed')->count(), 'overdue' => (clone $q)->overdue()->count()];
    }

    public function createIssueUrl(): string
    {
        return CampaignIssueResource::getUrl('create');
    }

    public function issueUrl(CampaignIssue $issue): string
    {
        return CampaignIssueResource::getUrl('view', ['record' => $issue]);
    }

    private function query(): Builder
    {
        return CampaignIssueResource::getEloquentQuery()->when($this->constituencyId, fn (Builder $q, string $id) => $q->where('constituency_id', $id))->when($this->status, fn (Builder $q, string $status) => $q->where('status', $status))->when($this->category, fn (Builder $q, string $category) => $q->where('category', $category));
    }
}
