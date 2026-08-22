<?php

namespace App\Models\Concerns;

use App\Models\Booth;
use App\Models\CampaignBudget;
use App\Models\CampaignCommunication;
use App\Models\CampaignDailyBriefing;
use App\Models\CampaignEvent;
use App\Models\CampaignExpense;
use App\Models\CampaignIssue;
use App\Models\CampaignTask;
use App\Models\Candidate;
use App\Models\CandidateAssessment;
use App\Models\Constituency;
use App\Models\House;
use App\Models\SecurityAuditLog;
use App\Models\SurveyAnswer;
use App\Models\SurveyResponse;
use App\Models\Village;
use App\Models\Voter;
use App\Models\VoterImportBatch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\UnauthorizedException;

trait ScopedToAssemblyConstituency
{
    protected static function bootScopedToAssemblyConstituency(): void
    {
        static::addGlobalScope('assembly_constituency', function (Builder $query): void {
            $user = auth()->user();

            if (! $user?->isAssemblyAdmin() || $user->isSuperAdmin()) {
                return;
            }

            if (! $user->constituency_id) {
                $query->whereRaw('1 = 0');

                return;
            }

            static::applyConstituencyConstraint($query, (int) $user->constituency_id);
        });

        static::saving(function (Model $model): void {
            $user = auth()->user();

            if (! $user?->isAssemblyAdmin() || $user->isSuperAdmin()) {
                return;
            }

            if (! $user->constituency_id || ! static::belongsToConstituency($model, (int) $user->constituency_id)) {
                throw new UnauthorizedException('You may only manage records in your assigned constituency.');
            }
        });
    }

    protected static function applyConstituencyConstraint(Builder $query, int $constituencyId): void
    {
        match (static::class) {
            Constituency::class => $query->whereKey($constituencyId),
            Village::class => $query->where('constituency_id', $constituencyId),
            Booth::class => $query->whereHas('village', fn (Builder $q) => $q->where('constituency_id', $constituencyId)),
            Candidate::class => $query->where('constituency_id', $constituencyId),
            CandidateAssessment::class => $query->whereHas('candidate', fn (Builder $q) => $q->where('constituency_id', $constituencyId)),
            CampaignTask::class => $query->where('constituency_id', $constituencyId),
            CampaignIssue::class => $query->where('constituency_id', $constituencyId),
            CampaignDailyBriefing::class => $query->where('constituency_id', $constituencyId),
            VoterImportBatch::class => $query->where('constituency_id', $constituencyId),
            SecurityAuditLog::class => $query->where('constituency_id', $constituencyId),
            CampaignEvent::class => $query->where('constituency_id', $constituencyId),
            CampaignBudget::class => $query->where('constituency_id', $constituencyId),
            CampaignCommunication::class => $query->where('constituency_id', $constituencyId),
            CampaignExpense::class => $query->where('constituency_id', $constituencyId),
            House::class => $query->whereHas('booth.village', fn (Builder $q) => $q->where('constituency_id', $constituencyId)),
            Voter::class => $query->whereHas('house.booth.village', fn (Builder $q) => $q->where('constituency_id', $constituencyId)),
            SurveyResponse::class => $query->whereHas('house.booth.village', fn (Builder $q) => $q->where('constituency_id', $constituencyId)),
            SurveyAnswer::class => $query->whereHas('response.house.booth.village', fn (Builder $q) => $q->where('constituency_id', $constituencyId)),
            default => $query->whereRaw('1 = 0'),
        };
    }

    protected static function belongsToConstituency(Model $model, int $constituencyId): bool
    {
        return match (static::class) {
            Constituency::class => ! $model->exists || (int) $model->getKey() === $constituencyId,
            Village::class => (int) $model->constituency_id === $constituencyId,
            Booth::class => Village::query()->whereKey($model->village_id)->where('constituency_id', $constituencyId)->exists(),
            Candidate::class => (int) $model->constituency_id === $constituencyId,
            CandidateAssessment::class => Candidate::query()->whereKey($model->candidate_id)->where('constituency_id', $constituencyId)->exists(),
            CampaignTask::class => (int) $model->constituency_id === $constituencyId,
            CampaignIssue::class => (int) $model->constituency_id === $constituencyId,
            CampaignDailyBriefing::class => (int) $model->constituency_id === $constituencyId,
            VoterImportBatch::class => (int) $model->constituency_id === $constituencyId,
            SecurityAuditLog::class => (int) $model->constituency_id === $constituencyId,
            CampaignEvent::class => (int) $model->constituency_id === $constituencyId,
            CampaignBudget::class => (int) $model->constituency_id === $constituencyId,
            CampaignCommunication::class => (int) $model->constituency_id === $constituencyId,
            CampaignExpense::class => (int) $model->constituency_id === $constituencyId,
            House::class => Booth::query()->whereKey($model->booth_id)->exists(),
            Voter::class => House::query()->whereKey($model->house_id)->exists(),
            SurveyResponse::class => House::query()->whereKey($model->house_id)->exists(),
            SurveyAnswer::class => SurveyResponse::query()->whereKey($model->response_id)->exists(),
            default => false,
        };
    }
}
