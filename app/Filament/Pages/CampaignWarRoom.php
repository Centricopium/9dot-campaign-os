<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Models\Booth;
use App\Models\BoothOrganisation;
use App\Models\CampaignDailyBriefing;
use App\Models\CampaignIssue;
use App\Models\CampaignTask;
use App\Models\Constituency;
use App\Models\SurveyResponse;
use App\Models\Voter;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use UnitEnum;

class CampaignWarRoom extends Page
{
    use AuthorizesPagePermission;

    protected static string $requiredPermission = 'war_room.view';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCommandLine;

    protected static string|UnitEnum|null $navigationGroup = 'Campaign Operations';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Campaign War Room';

    protected static ?string $title = 'Campaign War Room & Daily Planner';

    protected string $view = 'filament.pages.campaign-war-room';

    public string $constituencyId = '';

    public string $planDate = '';

    public ?string $priorityMessage = null;

    public ?string $morningObjectives = null;

    public int $targetTasks = 0;

    public int $targetContacts = 0;

    public int $targetIssues = 0;

    public ?string $plannedEvents = null;

    public ?string $eveningSummary = null;

    public ?string $achievements = null;

    public ?string $blockers = null;

    public ?string $nextDayPriorities = null;

    public function mount(): void
    {
        $this->planDate = now()->toDateString();
        $user = auth()->user();
        if ($user?->isAssemblyAdmin() && ! $user->isSuperAdmin()) {
            $this->constituencyId = (string) $user->constituency_id;
        }
        $this->loadPlan();
    }

    public function updatedConstituencyId(): void
    {
        $this->loadPlan();
    }

    public function updatedPlanDate(): void
    {
        $this->loadPlan();
    }

    public function getConstituenciesProperty(): Collection
    {
        return Constituency::query()->orderBy('name')->get(['id', 'name']);
    }

    public function saveMorningPlan(): void
    {
        $this->validate(['constituencyId' => 'required|integer', 'planDate' => 'required|date', 'morningObjectives' => 'required|string', 'targetTasks' => 'integer|min:0', 'targetContacts' => 'integer|min:0', 'targetIssues' => 'integer|min:0']);
        $plan = $this->plan();
        $plan->fill(['priority_message' => $this->priorityMessage, 'morning_objectives' => $this->morningObjectives, 'target_tasks' => $this->targetTasks, 'target_contacts' => $this->targetContacts, 'target_issues' => $this->targetIssues, 'planned_events' => $this->plannedEvents, 'status' => 'Active', 'created_by' => $plan->created_by ?: auth()->id()])->save();
        Notification::make()->title('Morning campaign plan saved')->success()->send();
    }

    public function saveEveningReview(): void
    {
        $this->validate(['constituencyId' => 'required|integer', 'planDate' => 'required|date', 'eveningSummary' => 'required|string']);
        $plan = $this->plan();
        $plan->fill(['evening_summary' => $this->eveningSummary, 'achievements' => $this->achievements, 'blockers' => $this->blockers, 'next_day_priorities' => $this->nextDayPriorities, 'status' => 'Reviewed', 'reviewed_by' => auth()->id(), 'reviewed_at' => now(), 'created_by' => $plan->created_by ?: auth()->id()])->save();
        Notification::make()->title('Evening review completed')->success()->send();
    }

    public function getStatsProperty(): array
    {
        if (! $this->constituencyId) {
            return array_fill_keys(['tasksToday', 'tasksDone', 'tasksOverdue', 'issuesOpen', 'issuesCritical', 'issuesOverdue', 'issuesResolved', 'surveyToday', 'volunteers', 'booths', 'presidents', 'agents'], 0);
        }
        $cid = (int) $this->constituencyId;
        $date = $this->planDate ?: now()->toDateString();
        $tasks = CampaignTask::query()->where('constituency_id', $cid);
        $issues = CampaignIssue::query()->where('constituency_id', $cid);
        $booths = Booth::query()->whereHas('village', fn (Builder $q) => $q->where('constituency_id', $cid));
        $org = BoothOrganisation::query()->whereHas('booth.village', fn (Builder $q) => $q->where('constituency_id', $cid))->where('is_active', true);

        return ['tasksToday' => (clone $tasks)->whereDate('due_at', $date)->count(), 'tasksDone' => (clone $tasks)->whereDate('completed_at', $date)->count(), 'tasksOverdue' => (clone $tasks)->overdue()->count(), 'issuesOpen' => (clone $issues)->whereNotIn('status', ['Resolved', 'Closed', 'Rejected'])->count(), 'issuesCritical' => (clone $issues)->where('priority', 'Critical')->whereNotIn('status', ['Closed', 'Rejected'])->count(), 'issuesOverdue' => (clone $issues)->overdue()->count(), 'issuesResolved' => (clone $issues)->whereDate('resolved_at', $date)->count(), 'surveyToday' => SurveyResponse::query()->whereHas('house.booth.village', fn (Builder $q) => $q->where('constituency_id', $cid))->whereDate('submitted_at', $date)->count(), 'volunteers' => Voter::query()->whereHas('house.booth.village', fn (Builder $q) => $q->where('constituency_id', $cid))->where('is_volunteer', true)->count(), 'booths' => (clone $booths)->count(), 'presidents' => (clone $org)->where('role', 'Booth President')->distinct('booth_id')->count('booth_id'), 'agents' => (clone $org)->where('role', 'Polling Agent')->distinct('booth_id')->count('booth_id')];
    }

    public function getAlertsProperty(): Collection
    {
        if (! $this->constituencyId) {
            return collect();
        }
        $taskAlerts = CampaignTask::query()->where('constituency_id', $this->constituencyId)->overdue()->limit(5)->get()->map(fn ($x) => ['type' => 'Task', 'title' => $x->title, 'meta' => $x->task_code, 'priority' => $x->priority]);
        $issueAlerts = CampaignIssue::query()->where('constituency_id', $this->constituencyId)->where(function (Builder $q) {
            $q->overdue()->orWhere('priority', 'Critical');
        })->whereNotIn('status', ['Closed', 'Rejected'])->limit(5)->get()->map(fn ($x) => ['type' => 'Issue', 'title' => $x->title, 'meta' => $x->issue_code, 'priority' => $x->priority]);

        return $taskAlerts->concat($issueAlerts)->take(10);
    }

    private function plan(): CampaignDailyBriefing
    {
        return CampaignDailyBriefing::firstOrNew(['constituency_id' => (int) $this->constituencyId, 'briefing_date' => $this->planDate]);
    }

    private function loadPlan(): void
    {
        $plan = ($this->constituencyId && $this->planDate) ? CampaignDailyBriefing::query()->where('constituency_id', $this->constituencyId)->whereDate('briefing_date', $this->planDate)->first() : null;
        foreach (['priorityMessage' => 'priority_message', 'morningObjectives' => 'morning_objectives', 'targetTasks' => 'target_tasks', 'targetContacts' => 'target_contacts', 'targetIssues' => 'target_issues', 'plannedEvents' => 'planned_events', 'eveningSummary' => 'evening_summary', 'achievements' => 'achievements', 'blockers' => 'blockers', 'nextDayPriorities' => 'next_day_priorities'] as $property => $column) {
            $this->{$property} = $plan?->{$column} ?? (in_array($property, ['targetTasks', 'targetContacts', 'targetIssues'], true) ? 0 : null);
        }
    }
}
