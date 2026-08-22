<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Validation\UnauthorizedException;

class CampaignCommunication extends Model
{
    use ScopedToAssemblyConstituency;

    public const CHANNELS = ['WhatsApp' => 'WhatsApp', 'SMS' => 'SMS', 'Facebook' => 'Facebook', 'Instagram' => 'Instagram', 'YouTube' => 'YouTube', 'X / Twitter' => 'X / Twitter', 'Press / News' => 'Press / News', 'Print Material' => 'Print Material', 'Email' => 'Email', 'Other' => 'Other'];

    public const CONTENT_TYPES = ['Post' => 'Post', 'Video' => 'Video', 'Reel / Short' => 'Reel / Short', 'Story' => 'Story', 'Press Release' => 'Press Release', 'Speech / Talking Points' => 'Speech / Talking Points', 'Poster / Creative' => 'Poster / Creative', 'Bulk Message' => 'Bulk Message', 'Article' => 'Article', 'Other' => 'Other'];

    public const AUDIENCES = ['General Voters' => 'General Voters', 'Strong Supporters' => 'Strong Supporters', 'Swing / Undecided Voters' => 'Swing / Undecided Voters', 'Women Voters' => 'Women Voters', 'Youth Voters' => 'Youth Voters', 'Senior Citizens' => 'Senior Citizens', 'Volunteers' => 'Volunteers', 'Booth Team' => 'Booth Team', 'Media' => 'Media', 'Other' => 'Other'];

    public const LANGUAGES = ['Gujarati' => 'Gujarati', 'Hindi' => 'Hindi', 'English' => 'English', 'Marathi' => 'Marathi', 'Other' => 'Other'];

    public const PRIORITIES = ['Normal' => 'Normal', 'High' => 'High', 'Urgent' => 'Urgent'];

    public const STATUSES = ['Draft' => 'Draft', 'Pending Approval' => 'Pending Approval', 'Approved' => 'Approved', 'Scheduled' => 'Scheduled', 'Published' => 'Published', 'Rejected' => 'Rejected', 'Cancelled' => 'Cancelled'];

    protected $fillable = ['communication_code', 'constituency_id', 'owner_id', 'created_by', 'approved_by', 'title', 'channel', 'content_type', 'audience', 'language', 'priority', 'status', 'scheduled_at', 'published_at', 'approved_at', 'message', 'call_to_action', 'asset_path', 'published_url', 'planned_reach', 'actual_reach', 'engagement_count', 'estimated_cost', 'actual_cost', 'approval_notes', 'notes'];

    protected $casts = ['scheduled_at' => 'datetime', 'published_at' => 'datetime', 'approved_at' => 'datetime', 'planned_reach' => 'integer', 'actual_reach' => 'integer', 'engagement_count' => 'integer', 'estimated_cost' => 'decimal:2', 'actual_cost' => 'decimal:2'];

    protected static function booted(): void
    {
        static::creating(function (self $communication): void {
            $communication->communication_code ??= 'COM-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
            $communication->created_by ??= auth()->id();
            $communication->status ??= 'Draft';
        });

        static::saving(function (self $communication): void {
            $user = auth()->user();

            if ($user?->constituency_id && ! $user->isSuperAdmin() && ! $user->can('campaign_communication.approve') && (int) $communication->constituency_id !== (int) $user->constituency_id) {
                throw new UnauthorizedException('You may only manage communication for your assigned constituency.');
            }
        });
    }

    public function constituency(): BelongsTo
    {
        return $this->belongsTo(Constituency::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approve(User $user, ?string $notes = null): void
    {
        $status = $this->scheduled_at ? 'Scheduled' : 'Approved';
        $this->forceFill(['status' => $status, 'approved_by' => $user->id, 'approved_at' => now(), 'approval_notes' => $notes])->save();
    }

    public function reject(User $user, string $notes): void
    {
        $this->forceFill(['status' => 'Rejected', 'approved_by' => $user->id, 'approved_at' => now(), 'approval_notes' => $notes])->save();
    }

    public function markPublished(?string $url = null, ?int $reach = null, ?int $engagement = null): void
    {
        $this->forceFill([
            'status' => 'Published',
            'published_at' => now(),
            'published_url' => $url ?: $this->published_url,
            'actual_reach' => $reach ?? $this->actual_reach,
            'engagement_count' => $engagement ?? $this->engagement_count,
        ])->save();
    }

    public function getEngagementRateAttribute(): float
    {
        return $this->actual_reach > 0 ? round(($this->engagement_count / $this->actual_reach) * 100, 1) : 0;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->scheduled_at?->isPast() && ! in_array($this->status, ['Published', 'Cancelled', 'Rejected'], true);
    }
}
