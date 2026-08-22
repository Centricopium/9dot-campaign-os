<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAssemblyConstituency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateAssessment extends Model
{
    use ScopedToAssemblyConstituency;

    protected $fillable = [
        'candidate_id',
        'assessor_id',
        'winnability_score',
        'constituency_connect_score',
        'organisation_strength_score',
        'public_work_score',
        'integrity_score',
        'leadership_score',
        'party_loyalty_score',
        'campaign_readiness_score',
        'compliance_score',
        'risk_score',
        'confidence_score',
        'remarks',
        'evidence_notes',
        'is_submitted',
    ];

    protected $casts = [
        'overall_score' => 'decimal:2',
        'is_submitted' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (CandidateAssessment $assessment): void {
            $assessment->overall_score = round(
                ($assessment->winnability_score * .20)
                + ($assessment->constituency_connect_score * .15)
                + ($assessment->organisation_strength_score * .15)
                + ($assessment->public_work_score * .15)
                + ($assessment->integrity_score * .10)
                + ($assessment->leadership_score * .10)
                + ($assessment->party_loyalty_score * .05)
                + ($assessment->campaign_readiness_score * .05)
                + ($assessment->compliance_score * .05),
                2,
            );

            $assessment->submitted_at = $assessment->is_submitted
                ? ($assessment->submitted_at ?? now())
                : null;
        });
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }
}
