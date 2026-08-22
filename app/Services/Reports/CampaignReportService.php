<?php

namespace App\Services\Reports;

use App\Models\Booth;
use App\Models\BoothOrganisation;
use App\Models\Candidate;
use App\Models\Constituency;
use App\Models\House;
use App\Models\SurveyResponse;
use App\Models\Village;
use App\Models\Voter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CampaignReportService
{
    public static function catalog(): array
    {
        return [
            'constituency_summary' => ['Constituency Summary Report', 'Constituency-level campaign coverage and voter totals.'],
            'taluka_performance' => ['Taluka-wise Performance Report', 'Taluka coverage, voter support and campaign readiness.'],
            'village_performance' => ['Village-wise Performance Report', 'Village coverage, support and campaign readiness.'],
            'booth_voters' => ['Booth-wise Voter Report', 'Booth-wise electoral roll and contact information.'],
            'booth_risk' => ['Booth Intelligence & Risk Report', 'Weakness, undecided share and booth priority.'],
            'voter_house_booth' => ['Voter List with House/Booth Details', 'Full voter location and electoral mapping.'],
            'political_support' => ['Political Support Analysis', 'Support-level distribution by campaign area.'],
            'support_voter_lists' => ['Support / Neutral / Opposition Voter Lists', 'Actionable voters filtered by support level.'],
            'volunteers_influencers' => ['Volunteer & Influencer Report', 'Campaign volunteers and local influencers.'],
            'booth_presidents' => ['Booth President Contact List', 'Booth presidents with booth, village and mobile details.'],
            'volunteer_contacts' => ['Volunteer Contact List', 'Campaign and organisation volunteers with mobile details.'],
            'survey_intelligence' => ['Survey Response & Intelligence Report', 'Survey completion and response activity.'],
            'gender_analysis' => ['Gender-wise Voter Analysis', 'Male, female and other voter distribution.'],
            'age_demographic' => ['Age-group / Demographic Report', 'Age bands and voter demographics.'],
            'party_support' => ['Party-wise Support Report', 'Political party preference distribution.'],
            'weak_priority_booths' => ['Weak Booth & Priority Booth Report', 'Booths requiring campaign intervention.'],
            'swing_voters' => ['Swing Voter Report', 'Neutral, undecided and leaning voters.'],
            'house_voters' => ['House-wise Voter Report', 'Household size, support and contact readiness.'],
            'missing_data' => ['Missing / Incomplete Data Report', 'Records requiring data cleanup.'],
            'campaign_executive' => ['Campaign Progress Executive Summary', 'Leadership-ready campaign health summary.'],
            'candidate_selection' => ['Candidate Selection Assessment Report', 'Same-party candidate strength, risk, confidence and final selection comparison.'],
        ];
    }

    public function generate(string $type, array $filters, int $limit = 100): array
    {
        $catalog = self::catalog();
        abort_unless(isset($catalog[$type]), 404);

        [$headers, $rows] = match ($type) {
            'constituency_summary' => $this->constituencySummary($filters, $limit),
            'taluka_performance' => $this->talukaPerformance($filters, $limit),
            'village_performance' => $this->villagePerformance($filters, $limit),
            'booth_risk', 'weak_priority_booths' => $this->boothRisk($filters, $limit),
            'political_support' => $this->politicalSupport($filters),
            'survey_intelligence' => $this->surveyIntelligence($filters, $limit),
            'gender_analysis' => $this->groupedVoters($filters, 'gender', 'Gender'),
            'age_demographic' => $this->ageDemographic($filters),
            'party_support' => $this->partySupport($filters),
            'house_voters' => $this->houseVoters($filters, $limit),
            'campaign_executive' => $this->campaignExecutive($filters),
            'volunteers_influencers' => $this->voterList($filters, $limit, 'campaign_people'),
            'booth_presidents' => $this->organisationContacts($filters, $limit, 'presidents'),
            'volunteer_contacts' => $this->organisationContacts($filters, $limit, 'volunteers'),
            'swing_voters' => $this->voterList($filters, $limit, 'swing'),
            'missing_data' => $this->voterList($filters, $limit, 'missing'),
            'support_voter_lists' => $this->voterList($filters, $limit, 'support'),
            'booth_voters', 'voter_house_booth' => $this->voterList($filters, $limit, 'all'),
            'candidate_selection' => $this->candidateSelection($filters, $limit),
        };

        return [
            'key' => $type,
            'title' => $catalog[$type][0],
            'description' => $catalog[$type][1],
            'headers' => $headers,
            'rows' => $rows,
            'row_count' => count($rows),
            'generated_at' => now(),
        ];
    }

    private function constituencySummary(array $filters, int $limit): array
    {
        $rows = Constituency::query()
            ->when($filters['constituency_id'], fn ($q, $id) => $q->whereKey($id))
            ->withCount('villages')
            ->limit($limit)
            ->get()
            ->map(function (Constituency $constituency) use ($filters): array {
                $voters = Voter::query()->whereHas('house.booth.village', fn ($q) => $q->where('constituency_id', $constituency->id)->when($filters['taluka'], fn ($v, $taluka) => $v->where('taluka', $taluka)));
                $booths = Booth::query()->whereHas('village', fn ($q) => $q->where('constituency_id', $constituency->id)->when($filters['taluka'], fn ($v, $taluka) => $v->where('taluka', $taluka)));

                return [
                    $constituency->name,
                    $constituency->district ?? '-',
                    $constituency->villages_count,
                    $booths->count(),
                    $voters->count(),
                    (clone $voters)->where('is_volunteer', true)->count(),
                    (clone $voters)->where('is_influencer', true)->count(),
                    (clone $voters)->where('support_level', 'Undecided')->count(),
                ];
            })->all();

        return [['Constituency', 'District', 'Villages', 'Booths', 'Voters', 'Volunteers', 'Influencers', 'Undecided'], $rows];
    }

    private function talukaPerformance(array $filters, int $limit): array
    {
        $talukas = Village::query()
            ->when($filters['constituency_id'], fn ($q, $id) => $q->where('constituency_id', $id))
            ->when($filters['taluka'], fn ($q, $taluka) => $q->where('taluka', $taluka))
            ->with('constituency')
            ->whereNotNull('taluka')->where('taluka', '!=', '')
            ->get()->groupBy(fn (Village $village) => $village->constituency_id.'|'.$village->taluka)
            ->take($limit);

        $rows = $talukas->map(function (Collection $villages): array {
            $first = $villages->first();
            $villageIds = $villages->pluck('id');
            $voters = Voter::query()->whereHas('house.booth', fn ($q) => $q->whereIn('village_id', $villageIds));
            $total = $voters->count();
            $support = (clone $voters)->whereIn('support_level', ['Strong Support', 'Moderate Support', 'Leaning Support'])->count();

            return [$first->constituency?->name, $first->taluka, $villages->count(), Booth::query()->whereIn('village_id', $villageIds)->count(), $total, $support, (clone $voters)->where('support_level', 'Undecided')->count(), $total ? round($support * 100 / $total, 1).'%' : '0%'];
        })->values()->all();

        return [['Constituency', 'Taluka', 'Villages', 'Booths', 'Voters', 'Supporters', 'Undecided', 'Support %'], $rows];
    }

    private function villagePerformance(array $filters, int $limit): array
    {
        $rows = Village::query()
            ->when($filters['constituency_id'], fn ($q, $id) => $q->where('constituency_id', $id))
            ->when($filters['taluka'], fn ($q, $taluka) => $q->where('taluka', $taluka))
            ->when($filters['village_id'], fn ($q, $id) => $q->whereKey($id))
            ->with('constituency')->withCount('booths')->limit($limit)->get()
            ->map(function (Village $village): array {
                $voters = Voter::query()->whereHas('house.booth', fn ($q) => $q->where('village_id', $village->id));
                $total = $voters->count();
                $support = (clone $voters)->whereIn('support_level', ['Strong Support', 'Moderate Support', 'Leaning Support'])->count();

                return [$village->constituency?->name, $village->name, $village->booths_count, $total, $support, (clone $voters)->where('support_level', 'Undecided')->count(), $total ? round($support * 100 / $total, 1).'%' : '0%'];
            })->all();

        return [['Constituency', 'Village', 'Booths', 'Voters', 'Supporters', 'Undecided', 'Support %'], $rows];
    }

    private function boothRisk(array $filters, int $limit): array
    {
        $rows = $this->boothQuery($filters)->with('village.constituency')->limit($limit)->get()
            ->map(function (Booth $booth): array {
                $voters = Voter::query()->whereHas('house', fn ($q) => $q->where('booth_id', $booth->id));
                $total = $voters->count();
                $undecided = (clone $voters)->whereIn('support_level', ['Neutral', 'Undecided'])->count();
                $opposition = (clone $voters)->whereIn('support_level', ['Leaning Opposition', 'Moderate Opposition', 'Strong Opposition'])->count();
                $risk = $total ? round((($undecided * .6) + ($opposition * .4)) * 100 / $total, 1) : 0;

                return [$booth->village?->constituency?->name, $booth->village?->name, $booth->booth_no, $booth->booth_name, $total, $undecided, $opposition, $risk.'%', $risk >= 50 ? 'HIGH' : ($risk >= 25 ? 'MEDIUM' : 'LOW')];
            })->all();

        return [['Constituency', 'Village', 'Booth No', 'Booth', 'Voters', 'Neutral/Undecided', 'Opposition', 'Risk Score', 'Priority'], $rows];
    }

    private function voterList(array $filters, int $limit, string $mode): array
    {
        $query = $this->voterQuery($filters)->with(['house.booth.village.constituency', 'politicalParty']);

        match ($mode) {
            'campaign_people' => $query->where(fn ($q) => $q->where('is_volunteer', true)->orWhere('is_influencer', true)),
            'swing' => $query->whereIn('support_level', ['Neutral', 'Undecided', 'Leaning Support', 'Leaning Opposition']),
            'missing' => $query->where(fn ($q) => $q->whereNull('epic_no')->orWhereNull('house_id')->orWhereNull('gender')->orWhereNull('age')->orWhereNull('mobile')),
            'support' => $query->whereNotNull('support_level'),
            default => null,
        };

        $rows = $query->orderBy('part_no')->orderBy('serial_no')->limit($limit)->get()->map(function (Voter $voter): array {
            $booth = $voter->house?->booth;

            return [
                $booth?->village?->constituency?->name,
                $booth?->village?->name,
                $booth?->booth_no ?? $voter->part_no,
                $voter->house?->house_no ?? $voter->house_no,
                $voter->serial_no,
                $voter->epic_no,
                trim($voter->name.' '.($voter->surname ?? '')),
                $voter->gender,
                $voter->age,
                $voter->mobile,
                $voter->support_level,
                $voter->politicalParty?->short_name ?? $voter->party_preference,
                $voter->is_volunteer ? 'Yes' : 'No',
                $voter->is_influencer ? 'Yes' : 'No',
            ];
        })->all();

        return [['Constituency', 'Village', 'Booth', 'House', 'Serial', 'EPIC', 'Voter', 'Gender', 'Age', 'Mobile', 'Support', 'Party', 'Volunteer', 'Influencer'], $rows];
    }

    private function politicalSupport(array $filters): array
    {
        return $this->groupedVoters($filters, 'support_level', 'Support Level');
    }

    private function organisationContacts(array $filters, int $limit, string $mode): array
    {
        $query = BoothOrganisation::query()
            ->with(['booth.village.constituency', 'voter'])
            ->where('is_active', true)
            ->whereIn('booth_id', Booth::query()->select('id'))
            ->whereHas('booth', function ($booth) use ($filters): void {
                $booth->when($filters['booth_id'], fn ($q, $id) => $q->whereKey($id))
                    ->when(! $filters['booth_id'] && $filters['village_id'], fn ($q, $id) => $q->where('village_id', $id))
                    ->when(! $filters['village_id'] && $filters['taluka'], fn ($q) => $q->whereHas('village', fn ($v) => $v->where('taluka', $filters['taluka'])))
                    ->when(! $filters['village_id'] && $filters['constituency_id'], fn ($q) => $q->whereHas('village', fn ($v) => $v->where('constituency_id', $filters['constituency_id'])));
            });

        if ($mode === 'presidents') {
            $query->whereIn('role', ['Booth President', 'Mahila Booth President', 'Youth Booth President']);
        } else {
            $query->whereIn('role', ['Booth Volunteer', 'Mahila Booth Volunteer', 'Youth Booth Volunteer']);
        }

        $rows = $query->orderBy('booth_id')->orderBy('role')->limit($limit)->get()
            ->map(function (BoothOrganisation $member): array {
                $booth = $member->booth;
                $voter = $member->voter;

                return [
                    $booth?->village?->constituency?->name,
                    $booth?->village?->taluka,
                    $booth?->village?->name,
                    $booth?->booth_no,
                    $booth?->booth_name,
                    $member->role,
                    $voter?->name,
                    $voter?->mobile,
                    $voter?->house?->house_no ?? $voter?->house_no,
                    $member->is_active ? 'Active' : 'Inactive',
                ];
            });

        if (in_array($mode, ['presidents', 'volunteers'], true) && $rows->count() < $limit) {
            $flagged = $this->voterQuery($filters)
                ->when(
                    $mode === 'presidents',
                    fn (Builder $query) => $query->whereIn('booth_committee_role', [
                        'Booth President',
                        'Mahila Booth President',
                        'Youth Booth President',
                    ]),
                    fn (Builder $query) => $query->where('is_volunteer', true),
                )
                ->with('house.booth.village.constituency')
                ->limit($limit - $rows->count())
                ->get()
                ->map(function (Voter $voter) use ($mode): array {
                    $booth = $voter->house?->booth;

                    return [
                        $booth?->village?->constituency?->name,
                        $booth?->village?->taluka,
                        $booth?->village?->name,
                        $booth?->booth_no,
                        $booth?->booth_name,
                        $mode === 'presidents' ? $voter->booth_committee_role : 'Campaign Volunteer',
                        trim($voter->name.' '.($voter->surname ?? '')),
                        $voter->mobile,
                        $voter->house?->house_no ?? $voter->house_no,
                        $voter->is_active ? 'Active' : 'Inactive',
                    ];
                });

            $rows = $rows
                ->concat($flagged)
                ->unique(fn (array $row) => implode('|', [$row[3], $row[5], $row[6], $row[7]]));
        }

        return [['Constituency', 'Taluka', 'Village', 'Booth No', 'Booth', 'Role', 'Name', 'Mobile', 'House', 'Status'], $rows->values()->all()];
    }

    private function groupedVoters(array $filters, string $column, string $label): array
    {
        $rows = $this->voterQuery($filters)->selectRaw("COALESCE({$column}, 'Not Set') as group_name, COUNT(*) as aggregate")
            ->groupBy($column)->orderByDesc('aggregate')->get()->map(fn ($row) => [$row->group_name, $row->aggregate])->all();

        return [[$label, 'Voters'], $rows];
    }

    private function ageDemographic(array $filters): array
    {
        $query = $this->voterQuery($filters);
        $bands = [['18–25', 18, 25], ['26–35', 26, 35], ['36–45', 36, 45], ['46–60', 46, 60], ['61+', 61, 150]];
        $rows = collect($bands)->map(fn ($band) => [$band[0], (clone $query)->whereBetween('age', [$band[1], $band[2]])->count()])->all();
        $rows[] = ['Age Not Set', (clone $query)->whereNull('age')->count()];

        return [['Age Group', 'Voters'], $rows];
    }

    private function partySupport(array $filters): array
    {
        $rows = $this->voterQuery($filters)->leftJoin('political_parties', 'voters.political_party_id', '=', 'political_parties.id')
            ->selectRaw("COALESCE(political_parties.short_name, voters.party_preference, 'Not Set') as party, COUNT(voters.id) as aggregate")
            ->groupBy('political_parties.short_name', 'voters.party_preference')->orderByDesc('aggregate')->get()->map(fn ($row) => [$row->party, $row->aggregate])->all();

        return [['Party', 'Voters'], $rows];
    }

    private function surveyIntelligence(array $filters, int $limit): array
    {
        $query = SurveyResponse::query()->with(['survey', 'voter.house.booth.village.constituency'])->withCount('answers');
        $this->applySurveyFilters($query, $filters);
        $rows = $query->latest('submitted_at')->limit($limit)->get()->map(function (SurveyResponse $response): array {
            $booth = $response->voter?->house?->booth ?? $response->house?->booth;

            return [$response->survey?->name, $response->voter?->name, $booth?->village?->constituency?->name, $booth?->village?->name, $booth?->booth_no, $response->answers_count, optional($response->submitted_at)->format('d-m-Y H:i')];
        })->all();

        return [['Survey', 'Voter', 'Constituency', 'Village', 'Booth', 'Answers', 'Submitted'], $rows];
    }

    private function houseVoters(array $filters, int $limit): array
    {
        $query = House::query()->with('booth.village.constituency')->withCount(['voters', 'voters as volunteers_count' => fn ($q) => $q->where('is_volunteer', true), 'voters as influencers_count' => fn ($q) => $q->where('is_influencer', true)]);
        $this->applyHouseFilters($query, $filters);
        $rows = $query->limit($limit)->get()->map(fn (House $house) => [$house->booth?->village?->constituency?->name, $house->booth?->village?->name, $house->booth?->booth_no, $house->house_no, $house->head_of_family, $house->mobile, $house->voters_count, $house->volunteers_count, $house->influencers_count, $house->is_verified ? 'Verified' : 'Pending'])->all();

        return [['Constituency', 'Village', 'Booth', 'House', 'Head of Family', 'Mobile', 'Voters', 'Volunteers', 'Influencers', 'Status'], $rows];
    }

    private function campaignExecutive(array $filters): array
    {
        $voters = $this->voterQuery($filters);
        $total = $voters->count();
        $rows = [
            ['Total Voters', $total],
            ['Active Voters', (clone $voters)->where('is_active', true)->count()],
            ['Volunteers', (clone $voters)->where('is_volunteer', true)->count()],
            ['Influencers', (clone $voters)->where('is_influencer', true)->count()],
            ['Neutral / Undecided', (clone $voters)->whereIn('support_level', ['Neutral', 'Undecided'])->count()],
            ['Survey Responses', tap(SurveyResponse::query(), fn ($q) => $this->applySurveyFilters($q, $filters))->count()],
            ['Contact Data Available', (clone $voters)->whereNotNull('mobile')->count()],
            ['Data Completion %', $total ? round((clone $voters)->whereNotNull('epic_no')->whereNotNull('house_id')->count() * 100 / $total, 1).'%' : '0%'],
        ];

        return [['Campaign Metric', 'Value'], $rows];
    }

    private function candidateSelection(array $filters, int $limit): array
    {
        $rows = Candidate::query()
            ->with(['constituency', 'politicalParty'])
            ->withAvg('submittedAssessments as average_score', 'overall_score')
            ->withAvg('submittedAssessments as average_risk_score', 'risk_score')
            ->withAvg('submittedAssessments as average_confidence_score', 'confidence_score')
            ->withCount('submittedAssessments')
            ->when($filters['constituency_id'], fn ($query, $id) => $query->where('constituency_id', $id))
            ->orderBy('constituency_id')
            ->orderByDesc('average_score')
            ->limit($limit)
            ->get()
            ->map(fn (Candidate $candidate): array => [
                $candidate->constituency?->name,
                $candidate->politicalParty?->name,
                $candidate->election_name,
                $candidate->election_year,
                $candidate->name,
                $candidate->current_position,
                $candidate->submitted_assessments_count,
                $candidate->average_score === null ? 'Pending' : number_format((float) $candidate->average_score, 1),
                $candidate->average_risk_score === null ? 'Pending' : number_format((float) $candidate->average_risk_score, 1),
                $candidate->average_confidence_score === null ? 'Pending' : number_format((float) $candidate->average_confidence_score, 1).'%',
                $candidate->system_recommendation,
                $candidate->status,
                $candidate->is_final ? 'FINAL SELECTED' : '—',
            ])->all();

        return [[
            'Constituency', 'Party', 'Election', 'Year', 'Candidate', 'Current Position',
            'Assessments', 'Strength Score', 'Risk Score', 'Confidence', 'System Recommendation', 'Status', 'Final Decision',
        ], $rows];
    }

    private function voterQuery(array $filters): Builder
    {
        $query = Voter::query();
        $query->when($filters['support_level'], fn ($q, $level) => $q->where('support_level', $level));
        $query->when($filters['booth_id'], fn ($q, $id) => $q->whereHas('house', fn ($h) => $h->where('booth_id', $id)));
        $query->when(! $filters['booth_id'] && $filters['village_id'], fn ($q) => $q->whereHas('house.booth', fn ($b) => $b->where('village_id', $filters['village_id'])));
        $query->when(! $filters['village_id'] && $filters['taluka'], fn ($q) => $q->whereHas('house.booth.village', fn ($v) => $v->where('taluka', $filters['taluka'])));
        $query->when(! $filters['booth_id'] && ! $filters['village_id'] && $filters['constituency_id'], fn ($q) => $q->whereHas('house.booth.village', fn ($v) => $v->where('constituency_id', $filters['constituency_id'])));

        return $query;
    }

    private function boothQuery(array $filters): Builder
    {
        return Booth::query()->when($filters['booth_id'], fn ($q, $id) => $q->whereKey($id))->when(! $filters['booth_id'] && $filters['village_id'], fn ($q, $id) => $q->where('village_id', $id))->when(! $filters['village_id'] && $filters['taluka'], fn ($q) => $q->whereHas('village', fn ($v) => $v->where('taluka', $filters['taluka'])))->when(! $filters['village_id'] && $filters['constituency_id'], fn ($q) => $q->whereHas('village', fn ($v) => $v->where('constituency_id', $filters['constituency_id'])));
    }

    private function applyHouseFilters(Builder $query, array $filters): void
    {
        $query->when($filters['booth_id'], fn ($q, $id) => $q->where('booth_id', $id))->when(! $filters['booth_id'] && $filters['village_id'], fn ($q) => $q->whereHas('booth', fn ($b) => $b->where('village_id', $filters['village_id'])))->when(! $filters['village_id'] && $filters['taluka'], fn ($q) => $q->whereHas('booth.village', fn ($v) => $v->where('taluka', $filters['taluka'])))->when(! $filters['village_id'] && $filters['constituency_id'], fn ($q) => $q->whereHas('booth.village', fn ($v) => $v->where('constituency_id', $filters['constituency_id'])));
    }

    private function applySurveyFilters(Builder $query, array $filters): void
    {
        $query->when($filters['booth_id'], fn ($q, $id) => $q->whereHas('house', fn ($h) => $h->where('booth_id', $id)))->when(! $filters['booth_id'] && $filters['village_id'], fn ($q) => $q->whereHas('house.booth', fn ($b) => $b->where('village_id', $filters['village_id'])))->when(! $filters['village_id'] && $filters['taluka'], fn ($q) => $q->whereHas('house.booth.village', fn ($v) => $v->where('taluka', $filters['taluka'])))->when(! $filters['village_id'] && $filters['constituency_id'], fn ($q) => $q->whereHas('house.booth.village', fn ($v) => $v->where('constituency_id', $filters['constituency_id'])));
    }
}
