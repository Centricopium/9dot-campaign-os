<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Filament\Resources\CampaignCommunications\CampaignCommunicationResource;
use App\Models\CampaignCommunication;
use App\Models\Constituency;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use UnitEnum;

class MediaCommunicationCentre extends Page
{
    use AuthorizesPagePermission;

    protected static string $requiredPermission = 'campaign_communication.view';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static string|UnitEnum|null $navigationGroup = 'Campaign Operations';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'Media & Communication';

    protected static ?string $title = 'Media & Communication Centre';

    protected string $view = 'filament.pages.media-communication-centre';

    public string $constituencyId = '';

    public string $channel = '';

    public string $status = '';

    public function mount(): void
    {
        $user = auth()->user();

        if ($user?->constituency_id && ! $user->isSuperAdmin() && ! $user->can('campaign_communication.approve')) {
            $this->constituencyId = (string) $user->constituency_id;
        }
    }

    public function getConstituenciesProperty(): Collection
    {
        return Constituency::query()->orderBy('name')->get(['id', 'name']);
    }

    public function getStatsProperty(): array
    {
        $query = $this->query();

        return [
            'total' => (clone $query)->count(),
            'today' => (clone $query)->whereDate('scheduled_at', today())->count(),
            'scheduled' => (clone $query)->whereBetween('scheduled_at', [now(), now()->addDays(7)])->whereIn('status', ['Approved', 'Scheduled'])->count(),
            'pending' => (clone $query)->where('status', 'Pending Approval')->count(),
            'published' => (clone $query)->where('status', 'Published')->where('published_at', '>=', now()->subDays(30))->count(),
            'overdue' => (clone $query)->whereNotNull('scheduled_at')->where('scheduled_at', '<', now())->whereNotIn('status', ['Published', 'Cancelled', 'Rejected'])->count(),
            'reach' => (int) (clone $query)->where('status', 'Published')->sum('actual_reach'),
            'engagement' => (int) (clone $query)->where('status', 'Published')->sum('engagement_count'),
        ];
    }

    public function getCalendarProperty(): Collection
    {
        return $this->query()
            ->with(['constituency:id,name', 'owner:id,name'])
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>=', now()->subDay())
            ->orderBy('scheduled_at')
            ->limit(30)
            ->get();
    }

    public function getApprovalQueueProperty(): Collection
    {
        return $this->query()
            ->with(['constituency:id,name', 'owner:id,name'])
            ->where('status', 'Pending Approval')
            ->orderByRaw("CASE priority WHEN 'Urgent' THEN 1 WHEN 'High' THEN 2 ELSE 3 END")
            ->oldest('updated_at')
            ->limit(10)
            ->get();
    }

    public function getChannelPerformanceProperty(): Collection
    {
        return $this->query()
            ->where('status', 'Published')
            ->selectRaw('channel, COUNT(*) as content_count, SUM(actual_reach) as total_reach, SUM(engagement_count) as total_engagement')
            ->groupBy('channel')
            ->orderByDesc('total_reach')
            ->get();
    }

    public function createUrl(): string
    {
        return CampaignCommunicationResource::getUrl('create');
    }

    public function registerUrl(): string
    {
        return CampaignCommunicationResource::getUrl('index');
    }

    public function communicationUrl(CampaignCommunication $communication): string
    {
        return CampaignCommunicationResource::getUrl('edit', ['record' => $communication]);
    }

    private function query(): Builder
    {
        return CampaignCommunicationResource::getEloquentQuery()
            ->when($this->constituencyId, fn (Builder $query, string $id): Builder => $query->where('constituency_id', $id))
            ->when($this->channel, fn (Builder $query, string $channel): Builder => $query->where('channel', $channel))
            ->when($this->status, fn (Builder $query, string $status): Builder => $query->where('status', $status));
    }
}
