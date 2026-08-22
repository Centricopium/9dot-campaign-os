<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CampaignTask extends Model
{
    use ScopedToAssemblyConstituency;

    public const STATUSES = [
        'Pending' => 'Pending',
        'Assigned' => 'Assigned',
        'In Progress' => 'In Progress',
        'On Hold' => 'On Hold',
        'Completed' => 'Completed - Awaiting Review',
        'Approved' => 'Approved',
        'Rejected' => 'Rejected - Rework Required',
        'Cancelled' => 'Cancelled',
    ];

    public const PRIORITIES = [
        'Low' => 'Low',
        'Medium' => 'Medium',
        'High' => 'High',
        'Critical' => 'Critical',
    ];

    public const CATEGORIES = [
        'Field Visit' => 'Field Visit',
        'House Contact' => 'House Contact',
        'Booth Organisation' => 'Booth Organisation',
        'Volunteer Meeting' => 'Volunteer Meeting',
        'Survey Follow-up' => 'Survey Follow-up',
        'Issue Resolution' => 'Issue Resolution',
        'Event Preparation' => 'Event Preparation',
        'Data Verification' => 'Data Verification',
        'Campaign Material' => 'Campaign Material',
        'Other' => 'Other',
    ];

    protected $fillable = [
        'task_code',
        'constituency_id',
        'village_id',
        'booth_id',
        'assigned_to',
        'created_by',
        'reviewed_by',
        'title',
        'category',
        'priority',
        'status',
        'description',
        'completion_percent',
        'starts_at',
        'due_at',
        'completed_at',
        'reviewed_at',
        'requires_proof',
        'proof_files',
        'field_notes',
        'completion_notes',
        'review_notes',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'completion_percent' => 'integer',
        'starts_at' => 'datetime',
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'requires_proof' => 'boolean',
        'proof_files' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    protected static function booted(): void
    {
        static::creating(function (CampaignTask $task): void {
            $task->task_code ??= 'FT-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
            $task->created_by ??= auth()->id();
            $task->category ??= 'Field Visit';
            $task->priority ??= 'Medium';
            $task->status ??= 'Pending';
            $task->completion_percent ??= 0;

            if ($task->assigned_to && $task->status === 'Pending') {
                $task->status = 'Assigned';
            }
        });

        static::saving(function (CampaignTask $task): void {
            if ($task->village_id && ! Village::query()
                ->whereKey($task->village_id)
                ->where('constituency_id', $task->constituency_id)
                ->exists()) {
                throw ValidationException::withMessages([
                    'village_id' => 'The selected village does not belong to this constituency.',
                ]);
            }

            if ($task->booth_id && ! Booth::query()
                ->whereKey($task->booth_id)
                ->when($task->village_id, fn (Builder $query) => $query->where('village_id', $task->village_id))
                ->whereHas('village', fn (Builder $query) => $query->where('constituency_id', $task->constituency_id))
                ->exists()) {
                throw ValidationException::withMessages([
                    'booth_id' => 'The selected booth does not belong to this task area.',
                ]);
            }

            $task->completion_percent = max(0, min(100, (int) $task->completion_percent));

            if (in_array($task->status, ['Completed', 'Approved'], true)) {
                $task->completion_percent = 100;
                $task->completed_at ??= now();
            }
        });

        static::saved(function (CampaignTask $task): void {
            $tracked = ['status', 'completion_percent', 'field_notes', 'completion_notes', 'review_notes', 'proof_files'];

            if (! $task->wasRecentlyCreated && ! $task->wasChanged($tracked)) {
                return;
            }

            $task->updates()->create([
                'user_id' => auth()->id(),
                'old_status' => $task->wasRecentlyCreated ? null : $task->getOriginal('status'),
                'new_status' => $task->status,
                'completion_percent' => $task->completion_percent,
                'notes' => $task->review_notes ?: ($task->completion_notes ?: $task->field_notes),
                'proof_files' => $task->proof_files,
                'latitude' => $task->latitude,
                'longitude' => $task->longitude,
            ]);
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

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(CampaignTaskUpdate::class)->latest();
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->whereNotIn('status', ['Approved', 'Cancelled']);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_at?->isPast()
            && ! in_array($this->status, ['Approved', 'Cancelled'], true);
    }

    public function start(User $user): void
    {
        $this->forceFill([
            'assigned_to' => $this->assigned_to ?: $user->getKey(),
            'status' => 'In Progress',
            'completion_percent' => max(10, $this->completion_percent),
            'starts_at' => $this->starts_at ?: now(),
        ])->save();
    }

    public function complete(User $user, ?string $notes = null, array $proofFiles = []): void
    {
        $proofFiles = array_values(array_filter($proofFiles));

        if ($this->requires_proof && empty($proofFiles) && empty($this->proof_files)) {
            throw ValidationException::withMessages([
                'proof_files' => 'Completion proof is required for this task.',
            ]);
        }

        $this->forceFill([
            'assigned_to' => $this->assigned_to ?: $user->getKey(),
            'status' => 'Completed',
            'completion_percent' => 100,
            'completed_at' => now(),
            'completion_notes' => $notes,
            'proof_files' => empty($proofFiles) ? $this->proof_files : $proofFiles,
        ])->save();
    }

    public function review(User $reviewer, bool $approved, ?string $notes = null): void
    {
        if ($this->status !== 'Completed') {
            throw ValidationException::withMessages([
                'status' => 'Only completed tasks can be reviewed.',
            ]);
        }

        DB::transaction(function () use ($reviewer, $approved, $notes): void {
            $this->forceFill([
                'status' => $approved ? 'Approved' : 'Rejected',
                'reviewed_by' => $reviewer->getKey(),
                'reviewed_at' => now(),
                'review_notes' => $notes,
            ])->save();
        });
    }
}
