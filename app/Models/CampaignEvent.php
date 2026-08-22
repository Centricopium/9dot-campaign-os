<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CampaignEvent extends Model
{
    use ScopedToAssemblyConstituency;

    public const TYPES = ['Public Meeting' => 'Public Meeting', 'Rally' => 'Rally', 'Candidate Tour' => 'Candidate Tour', 'Corner Meeting' => 'Corner Meeting', 'Volunteer Meeting' => 'Volunteer Meeting', 'Booth Meeting' => 'Booth Meeting', 'Press Conference' => 'Press Conference', 'Training' => 'Training', 'Door-to-Door Drive' => 'Door-to-Door Drive', 'Other' => 'Other'];

    public const STATUSES = ['Planned' => 'Planned', 'Confirmed' => 'Confirmed', 'In Progress' => 'In Progress', 'Completed' => 'Completed', 'Postponed' => 'Postponed', 'Cancelled' => 'Cancelled'];

    public const PERMISSIONS = ['Not Required' => 'Not Required', 'Pending' => 'Pending', 'Approved' => 'Approved', 'Rejected' => 'Rejected'];

    public const REQUIREMENTS = ['Venue' => 'Venue', 'Police Permission' => 'Police Permission', 'Loudspeaker Permission' => 'Loudspeaker Permission', 'Vehicle Permission' => 'Vehicle Permission', 'Stage & Sound' => 'Stage & Sound', 'Electricity' => 'Electricity', 'Security Team' => 'Security Team', 'Medical Support' => 'Medical Support', 'Campaign Material' => 'Campaign Material', 'Media Team' => 'Media Team', 'Transport' => 'Transport', 'Refreshment' => 'Refreshment'];

    protected $fillable = ['event_code', 'constituency_id', 'village_id', 'booth_id', 'candidate_id', 'coordinator_id', 'created_by', 'title', 'event_type', 'status', 'venue', 'address', 'starts_at', 'ends_at', 'expected_attendance', 'actual_attendance', 'estimated_budget', 'actual_expense', 'permission_status', 'permission_reference', 'requirements', 'attachments', 'description', 'outcome_notes'];

    protected $casts = ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'expected_attendance' => 'integer', 'actual_attendance' => 'integer', 'estimated_budget' => 'decimal:2', 'actual_expense' => 'decimal:2', 'requirements' => 'array', 'attachments' => 'array'];

    protected static function booted(): void
    {
        static::creating(function (self $e): void {
            $e->event_code ??= 'EV-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
            $e->created_by ??= auth()->id();
            $e->event_type ??= 'Public Meeting';
            $e->status ??= 'Planned';
        });
        static::saving(function (self $e): void {
            if ($e->village_id && ! Village::query()->whereKey($e->village_id)->where('constituency_id', $e->constituency_id)->exists()) {
                throw ValidationException::withMessages(['village_id' => 'Village does not belong to the selected Assembly.']);
            }if ($e->booth_id && ! Booth::query()->whereKey($e->booth_id)->whereHas('village', fn (Builder $q) => $q->where('constituency_id', $e->constituency_id))->exists()) {
                throw ValidationException::withMessages(['booth_id' => 'Booth does not belong to the selected Assembly.']);
            }if ($e->candidate_id && ! Candidate::query()->whereKey($e->candidate_id)->where('constituency_id', $e->constituency_id)->exists()) {
                throw ValidationException::withMessages(['candidate_id' => 'Candidate does not belong to the selected Assembly.']);
            }
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

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(CampaignEventTeamMember::class);
    }

    public function getAttendancePercentAttribute(): int
    {
        return $this->expected_attendance > 0 ? min(100, (int) round(($this->actual_attendance / $this->expected_attendance) * 100)) : 0;
    }
}
