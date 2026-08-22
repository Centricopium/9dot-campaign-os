<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Candidate extends Model
{
    use ScopedToAssemblyConstituency;

    protected $fillable = [
        'constituency_id',
        'political_party_id',
        'election_name',
        'election_year',
        'name',
        'mobile',
        'email',
        'photo',
        'current_position',
        'political_experience_years',
        'profile_summary',
        'public_work',
        'strengths',
        'concerns',
        'status',
        'final_decision_notes',
    ];

    protected $casts = [
        'election_year' => 'integer',
        'political_experience_years' => 'integer',
        'is_final' => 'boolean',
        'finalised_at' => 'datetime',
    ];

    public function constituency(): BelongsTo
    {
        return $this->belongsTo(Constituency::class);
    }

    public function politicalParty(): BelongsTo
    {
        return $this->belongsTo(PoliticalParty::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(CandidateAssessment::class);
    }

    public function submittedAssessments(): HasMany
    {
        return $this->assessments()->where('is_submitted', true);
    }

    public function finalisedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalised_by');
    }

    public function getSystemRecommendationAttribute(): string
    {
        if (! $this->submitted_assessments_count || $this->average_score === null) {
            return 'Assessment Pending';
        }

        if ((float) $this->average_confidence_score < 60) {
            return 'Low Confidence Review';
        }

        if ((float) $this->average_risk_score >= 60) {
            return 'High Risk Review';
        }

        return match (true) {
            (float) $this->average_score >= 80 => 'Strongly Recommended',
            (float) $this->average_score >= 70 => 'Recommended',
            (float) $this->average_score >= 60 => 'Consider',
            default => 'Not Recommended',
        };
    }

    public function selectAsFinal(User $user, ?string $notes = null): void
    {
        DB::transaction(function () use ($user, $notes): void {
            static::query()
                ->where('constituency_id', $this->constituency_id)
                ->where('political_party_id', $this->political_party_id)
                ->where('election_name', $this->election_name)
                ->where('election_year', $this->election_year)
                ->whereKeyNot($this->getKey())
                ->update([
                    'is_final' => false,
                    'status' => 'Not Selected',
                    'finalised_by' => null,
                    'finalised_at' => null,
                ]);

            $this->forceFill([
                'is_final' => true,
                'status' => 'Final Selected',
                'finalised_by' => $user->getKey(),
                'finalised_at' => now(),
                'final_decision_notes' => $notes,
            ])->save();
        });
    }
}
