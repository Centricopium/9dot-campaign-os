<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CampaignIssue extends Model
{
    use ScopedToAssemblyConstituency;

    public const STATUSES = ['Open' => 'Open', 'Assigned' => 'Assigned', 'In Progress' => 'In Progress', 'Waiting' => 'Waiting', 'Escalated' => 'Escalated', 'Resolved' => 'Resolved - Awaiting Closure', 'Closed' => 'Closed', 'Reopened' => 'Reopened', 'Rejected' => 'Rejected'];

    public const PRIORITIES = ['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High', 'Critical' => 'Critical'];

    public const CATEGORIES = ['Water' => 'Water', 'Road' => 'Road', 'Electricity' => 'Electricity', 'Drainage' => 'Drainage', 'Health' => 'Health', 'Education' => 'Education', 'Government Scheme' => 'Government Scheme', 'Employment' => 'Employment', 'Public Safety' => 'Public Safety', 'Voter Data Correction' => 'Voter Data Correction', 'Campaign Request' => 'Campaign Request', 'Other' => 'Other'];

    public const SOURCES = ['Field Visit' => 'Field Visit', 'Phone Call' => 'Phone Call', 'Survey' => 'Survey', 'Office Visit' => 'Office Visit', 'WhatsApp' => 'WhatsApp', 'Public Meeting' => 'Public Meeting', 'Other' => 'Other'];

    protected $fillable = ['issue_code', 'constituency_id', 'village_id', 'booth_id', 'house_id', 'voter_id', 'campaign_task_id', 'reported_by', 'assigned_to', 'resolved_by', 'title', 'category', 'source', 'priority', 'status', 'description', 'contact_name', 'contact_mobile', 'due_at', 'resolved_at', 'closed_at', 'resolution_notes', 'attachments', 'resolution_proof', 'is_confidential'];

    protected $casts = ['due_at' => 'datetime', 'resolved_at' => 'datetime', 'closed_at' => 'datetime', 'attachments' => 'array', 'resolution_proof' => 'array', 'is_confidential' => 'boolean'];

    protected static function booted(): void
    {
        static::creating(function (CampaignIssue $issue): void {
            $issue->issue_code ??= 'GI-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
            $issue->reported_by ??= auth()->id();
            $issue->category ??= 'Other';
            $issue->source ??= 'Field Visit';
            $issue->priority ??= 'Medium';
            $issue->status ??= $issue->assigned_to ? 'Assigned' : 'Open';
            $issue->due_at ??= now()->addHours(match ($issue->priority) {
                'Critical' => 24, 'High' => 72, 'Low' => 336, default => 168
            });
        });

        static::saving(function (CampaignIssue $issue): void {
            if ($issue->village_id && ! Village::query()->whereKey($issue->village_id)->where('constituency_id', $issue->constituency_id)->exists()) {
                throw ValidationException::withMessages(['village_id' => 'The selected village does not belong to this constituency.']);
            }
            if ($issue->booth_id && ! Booth::query()->whereKey($issue->booth_id)->whereHas('village', fn (Builder $query) => $query->where('constituency_id', $issue->constituency_id))->exists()) {
                throw ValidationException::withMessages(['booth_id' => 'The selected booth does not belong to this constituency.']);
            }
        });

        static::saved(function (CampaignIssue $issue): void {
            $tracked = ['status', 'assigned_to', 'resolution_notes', 'attachments', 'resolution_proof'];
            if (! $issue->wasRecentlyCreated && ! $issue->wasChanged($tracked)) {
                return;
            }
            $issue->updates()->create(['user_id' => auth()->id(), 'old_status' => $issue->wasRecentlyCreated ? null : $issue->getOriginal('status'), 'new_status' => $issue->status, 'notes' => $issue->resolution_notes, 'attachments' => $issue->resolution_proof ?: $issue->attachments]);
        });
    }

    public function constituency(): BelongsTo
    {
        return $this->belongsTo(Constituency::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function booth(): BelongsTo
    {
        return $this->belongsTo(Booth::class);
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    public function voter(): BelongsTo
    {
        return $this->belongsTo(Voter::class);
    }

    public function campaignTask(): BelongsTo
    {
        return $this->belongsTo(CampaignTask::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(CampaignIssueUpdate::class)->latest();
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereNotNull('due_at')->where('due_at', '<', now())->whereNotIn('status', ['Resolved', 'Closed', 'Rejected']);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_at?->isPast() && ! in_array($this->status, ['Resolved', 'Closed', 'Rejected'], true);
    }

    public function start(User $user): void
    {
        $this->forceFill(['assigned_to' => $this->assigned_to ?: $user->id, 'status' => 'In Progress'])->save();
    }

    public function escalate(User $user, string $notes): void
    {
        $this->forceFill(['status' => 'Escalated', 'assigned_to' => $this->assigned_to ?: $user->id, 'resolution_notes' => $notes])->save();
    }

    public function resolve(User $user, string $notes, array $proof = []): void
    {
        $this->forceFill(['status' => 'Resolved', 'resolved_by' => $user->id, 'resolved_at' => now(), 'resolution_notes' => $notes, 'resolution_proof' => array_values(array_filter($proof))])->save();
    }

    public function close(User $user, ?string $notes = null): void
    {
        if ($this->status !== 'Resolved') {
            throw ValidationException::withMessages(['status' => 'Only resolved issues can be closed.']);
        } $this->forceFill(['status' => 'Closed', 'closed_at' => now(), 'resolution_notes' => $notes ?: $this->resolution_notes])->save();
    }

    public function reopen(User $user, string $notes): void
    {
        $this->forceFill(['status' => 'Reopened', 'closed_at' => null, 'resolution_notes' => $notes])->save();
    }
}
