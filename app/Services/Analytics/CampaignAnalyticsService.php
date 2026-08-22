<?php

namespace App\Services\Analytics;

use App\Models\Booth;
use App\Models\BoothOrganisation;
use App\Models\House;
use App\Models\SurveyAnswer;
use App\Models\SurveyResponse;
use App\Models\Village;
use App\Models\Voter;
use App\Services\Reports\CampaignReportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CampaignAnalyticsService
{
    public function __construct(private CampaignReportService $reports) {}

    public function generate(array $filters): array
    {
        $constituencies = $this->reportSeries('constituency_summary', $filters, 0, 4);
        $talukas = $this->multiSeries('taluka_performance', $filters, 1, ['Voters' => 4, 'Support %' => 7]);
        $villages = $this->multiSeries('village_performance', $filters, 1, ['Voters' => 3, 'Support %' => 6]);
        $boothRiskRows = collect($this->reports->generate('booth_risk', $filters, 500)['rows']);
        $support = $this->reportSeries('political_support', $filters, 0, 1);
        $party = $this->reportSeries('party_support', $filters, 0, 1);
        $gender = $this->reportSeries('gender_analysis', $filters, 0, 1);
        $ages = $this->reportSeries('age_demographic', $filters, 0, 1);
        $executive = collect($this->reports->generate('campaign_executive', $filters, 100)['rows'])->mapWithKeys(fn ($row) => [$row[0] => $this->number($row[1])]);
        $houses = collect($this->reports->generate('house_voters', $filters, 5000)['rows']);
        $volunteers = collect($this->reports->generate('volunteer_contacts', $filters, 50000)['rows']);
        $totalBooths = $this->boothQuery($filters)->count();
        $presidentBooths = $this->assignedOrganisationBooths($filters, 'Booth President');
        $pollingAgentBooths = $this->assignedOrganisationBooths($filters, 'Polling Agent');

        return [
            'overview' => [
                $this->chart('constituency_voters', 'Constituency-wise Voter Comparison', 'Overview', 'bar', $constituencies),
                $this->chart('taluka_performance', 'Taluka-wise Voters & Support %', 'Overview', 'multi', $talukas),
                $this->chart('village_performance', 'Village-wise Campaign Performance', 'Overview', 'multi', $villages),
                $this->chart('campaign_progress', 'Campaign Progress Trend', 'Overview', 'line', $this->campaignTrend($filters)),
            ],
            'political' => [
                $this->chart('booth_support', 'Booth-wise Support vs Opposition', 'Political Support', 'stacked', $this->boothSupport($boothRiskRows)),
                $this->chart('risk_distribution', 'High / Medium / Low Risk Booths', 'Political Support', 'donut', $this->riskDistribution($boothRiskRows)),
                $this->chart('support_distribution', 'Political Support Distribution', 'Political Support', 'donut', $support),
                $this->chart('party_support', 'Party-wise Support', 'Political Support', 'bar', $party),
                $this->chart('swing_distribution', 'Swing Voter Distribution', 'Political Support', 'bar', collect($support)->filter(fn ($item) => in_array($item['label'], ['Neutral', 'Undecided', 'Leaning Support', 'Leaning Opposition'], true))->values()->all()),
            ],
            'demographics' => [
                $this->chart('gender', 'Male / Female / Other Voters', 'Demographics', 'donut', $gender),
                $this->chart('age', 'Age-group Demographic', 'Demographics', 'bar', $ages),
                $this->chart('people_coverage', 'Volunteer & Influencer Coverage', 'Demographics', 'donut', [
                    ['label' => 'Volunteers', 'value' => $executive->get('Volunteers', 0)],
                    ['label' => 'Influencers', 'value' => $executive->get('Influencers', 0)],
                    ['label' => 'Other Voters', 'value' => max(0, $executive->get('Total Voters', 0) - $executive->get('Volunteers', 0) - $executive->get('Influencers', 0))],
                ]),
            ],
            'survey' => [
                $this->chart('survey_progress', 'Survey Response Progress', 'Survey', 'progress', [
                    ['label' => 'Responses', 'value' => $executive->get('Survey Responses', 0)],
                    ['label' => 'Remaining Voters', 'value' => max(0, $executive->get('Total Voters', 0) - $executive->get('Survey Responses', 0))],
                ]),
                $this->chart('survey_answers', 'Survey Question-wise Answers', 'Survey', 'bar', $this->surveyAnswers($filters)),
                $this->chart('survey_daily', 'Daily Survey Response Trend', 'Survey', 'line', $this->surveyTrend($filters)),
            ],
            'organisation' => [
                $this->chart('president_coverage', 'Booth President Coverage', 'Organisation', 'donut', [
                    ['label' => 'President Assigned', 'value' => $presidentBooths],
                    ['label' => 'President Pending', 'value' => max(0, $totalBooths - $presidentBooths)],
                ]),
                $this->chart('polling_agent_coverage', 'Polling Agent Coverage', 'Organisation', 'donut', [
                    ['label' => 'Polling Agent Assigned', 'value' => $pollingAgentBooths],
                    ['label' => 'Polling Agent Pending', 'value' => max(0, $totalBooths - $pollingAgentBooths)],
                ]),
                $this->chart('volunteers_by_booth', 'Booth-wise Volunteer Strength', 'Organisation', 'bar', $this->volunteersByBooth($volunteers)),
                $this->chart('volunteer_gap', 'Volunteer Gap by Booth', 'Organisation', 'donut', $this->volunteerGap($volunteers, $totalBooths)),
                $this->chart('organisation_readiness', 'President & Volunteer Readiness', 'Organisation', 'progress', [
                    ['label' => 'President Coverage', 'value' => $totalBooths ? round($presidentBooths * 100 / $totalBooths, 1) : 0],
                    ['label' => 'Booths with Volunteers', 'value' => $totalBooths ? round($volunteers->pluck(3)->filter()->unique()->count() * 100 / $totalBooths, 1) : 0],
                ], '%'),
            ],
            'health' => [
                $this->chart('house_verification', 'House Contact / Verification Progress', 'Data Health', 'donut', $this->houseVerification($houses)),
                $this->chart('missing_data', 'Missing Data Percentage', 'Data Health', 'progress', [
                    ['label' => 'Complete Data', 'value' => $executive->get('Data Completion %', 0)],
                    ['label' => 'Incomplete Data', 'value' => max(0, 100 - $executive->get('Data Completion %', 0))],
                ], '%'),
                $this->chart('contact_coverage', 'Voter Contact Coverage', 'Data Health', 'progress', [
                    ['label' => 'Mobile Available', 'value' => $executive->get('Contact Data Available', 0)],
                    ['label' => 'Mobile Missing', 'value' => max(0, $executive->get('Total Voters', 0) - $executive->get('Contact Data Available', 0))],
                ]),
            ],
            'maps' => [
                $this->chart('booth_heatmap', 'Booth Risk Heatmap', 'Maps', 'heatmap', $this->boothHeatmap($boothRiskRows)),
                $this->chart('geographical_map', 'Taluka / Village Geographical Map', 'Maps', 'map', $this->geoPoints($filters)),
            ],
        ];
    }

    private function chart(string $key, string $title, string $group, string $type, array $data, string $suffix = ''): array
    {
        return compact('key', 'title', 'group', 'type', 'data', 'suffix');
    }

    private function reportSeries(string $report, array $filters, int $label, int $value): array
    {
        return collect($this->reports->generate($report, $filters, 100)['rows'])->map(fn ($row) => ['label' => (string) ($row[$label] ?? 'Unknown'), 'value' => $this->number($row[$value] ?? 0)])->all();
    }

    private function multiSeries(string $report, array $filters, int $label, array $columns): array
    {
        return collect($this->reports->generate($report, $filters, 40)['rows'])->map(function ($row) use ($label, $columns): array {
            return ['label' => (string) ($row[$label] ?? 'Unknown'), 'series' => collect($columns)->map(fn ($index, $name) => ['name' => $name, 'value' => $this->number($row[$index] ?? 0)])->values()->all()];
        })->all();
    }

    private function boothSupport(Collection $rows): array
    {
        return $rows->take(40)->map(fn ($row) => ['label' => 'Booth ' . ($row[2] ?? ''), 'series' => [['name' => 'Support', 'value' => max(0, $this->number($row[4]) - $this->number($row[5]) - $this->number($row[6]))], ['name' => 'Neutral', 'value' => $this->number($row[5])], ['name' => 'Opposition', 'value' => $this->number($row[6])]]])->all();
    }

    private function riskDistribution(Collection $rows): array
    {
        return collect(['HIGH', 'MEDIUM', 'LOW'])->map(fn ($level) => ['label' => $level, 'value' => $rows->where(8, $level)->count()])->all();
    }

    private function campaignTrend(array $filters): array
    {
        $query = Voter::query()->selectRaw('DATE(created_at) as day, COUNT(*) as aggregate')->groupBy('day')->orderBy('day');
        $this->applyVoterFilters($query, $filters);
        return $query->limit(30)->get()->map(fn ($row) => ['label' => $row->day, 'value' => $row->aggregate])->all();
    }

    private function surveyTrend(array $filters): array
    {
        $query = SurveyResponse::query()->selectRaw('DATE(created_at) as day, COUNT(*) as aggregate')->groupBy('day')->orderBy('day');
        $this->applySurveyFilters($query, $filters);
        return $query->limit(30)->get()->map(fn ($row) => ['label' => $row->day, 'value' => $row->aggregate])->all();
    }

    private function surveyAnswers(array $filters): array
    {
        $query = SurveyAnswer::query()->with('question')->selectRaw('question_id, answer, COUNT(*) as aggregate')->groupBy('question_id', 'answer')->orderByDesc('aggregate');
        $query->when(array_filter([$filters['constituency_id'], $filters['taluka'], $filters['village_id'], $filters['booth_id']]), fn ($q) => $q->whereHas('response.house.booth.village', function ($v) use ($filters): void {
            $v->when($filters['constituency_id'], fn ($x, $id) => $x->where('constituency_id', $id))->when($filters['taluka'], fn ($x, $taluka) => $x->where('taluka', $taluka))->when($filters['village_id'], fn ($x, $id) => $x->whereKey($id))->when($filters['booth_id'], fn ($x, $id) => $x->whereHas('booths', fn ($b) => $b->whereKey($id)));
        }));
        return $query->limit(20)->get()->map(fn ($row) => ['label' => str($row->question?->question ?? 'Question')->limit(28) . ': ' . str($row->answer)->limit(20), 'value' => $row->aggregate])->all();
    }

    private function volunteersByBooth(Collection $rows): array
    {
        return $rows->groupBy(3)->map(fn ($items, $booth) => ['label' => 'Booth ' . $booth, 'value' => $items->count()])->sortByDesc('value')->take(40)->values()->all();
    }

    private function volunteerGap(Collection $rows, int $totalBooths): array
    {
        $counts = $rows->groupBy(3)->map->count();
        return [['label' => 'No Volunteer', 'value' => max(0, $totalBooths - $counts->count())], ['label' => 'Low (1–2)', 'value' => $counts->filter(fn ($count) => $count <= 2)->count()], ['label' => 'Adequate (3+)', 'value' => $counts->filter(fn ($count) => $count >= 3)->count()]];
    }

    private function assignedOrganisationBooths(array $filters, string $role): int
    {
        return BoothOrganisation::query()
            ->where('role', $role)
            ->where('is_active', true)
            ->whereIn('booth_id', $this->boothQuery($filters)->select('id'))
            ->distinct('booth_id')
            ->count('booth_id');
    }

    private function houseVerification(Collection $rows): array
    {
        return [['label' => 'Verified', 'value' => $rows->where(9, 'Verified')->count()], ['label' => 'Pending', 'value' => $rows->where(9, 'Pending')->count()]];
    }

    private function boothHeatmap(Collection $rows): array
    {
        return $rows->take(120)->map(fn ($row) => ['label' => 'Booth ' . ($row[2] ?? ''), 'value' => $this->number($row[7] ?? 0), 'level' => strtolower($row[8] ?? 'low')])->all();
    }

    private function geoPoints(array $filters): array
    {
        return Village::query()->when($filters['constituency_id'], fn ($q, $id) => $q->where('constituency_id', $id))->when($filters['taluka'], fn ($q, $taluka) => $q->where('taluka', $taluka))->when($filters['village_id'], fn ($q, $id) => $q->whereKey($id))->whereNotNull('latitude')->whereNotNull('longitude')->limit(200)->get()->map(fn ($village) => ['label' => $village->name, 'taluka' => $village->taluka, 'lat' => (float) $village->latitude, 'lng' => (float) $village->longitude, 'value' => $village->total_voters])->all();
    }

    private function boothQuery(array $filters): Builder
    {
        return Booth::query()->when($filters['booth_id'], fn ($q, $id) => $q->whereKey($id))->when($filters['village_id'], fn ($q, $id) => $q->where('village_id', $id))->when($filters['taluka'], fn ($q) => $q->whereHas('village', fn ($v) => $v->where('taluka', $filters['taluka'])))->when($filters['constituency_id'], fn ($q) => $q->whereHas('village', fn ($v) => $v->where('constituency_id', $filters['constituency_id'])));
    }

    private function applyVoterFilters(Builder $query, array $filters): void
    {
        $query->when($filters['booth_id'], fn ($q, $id) => $q->whereHas('house', fn ($h) => $h->where('booth_id', $id)))->when($filters['village_id'], fn ($q, $id) => $q->whereHas('house.booth', fn ($b) => $b->where('village_id', $id)))->when($filters['taluka'], fn ($q, $taluka) => $q->whereHas('house.booth.village', fn ($v) => $v->where('taluka', $taluka)))->when($filters['constituency_id'], fn ($q, $id) => $q->whereHas('house.booth.village', fn ($v) => $v->where('constituency_id', $id)));
    }

    private function applySurveyFilters(Builder $query, array $filters): void
    {
        $query->when($filters['booth_id'], fn ($q, $id) => $q->whereHas('house', fn ($h) => $h->where('booth_id', $id)))->when($filters['village_id'], fn ($q, $id) => $q->whereHas('house.booth', fn ($b) => $b->where('village_id', $id)))->when($filters['taluka'], fn ($q, $taluka) => $q->whereHas('house.booth.village', fn ($v) => $v->where('taluka', $taluka)))->when($filters['constituency_id'], fn ($q, $id) => $q->whereHas('house.booth.village', fn ($v) => $v->where('constituency_id', $id)));
    }

    private function number(mixed $value): float|int
    {
        return is_numeric($value) ? $value + 0 : (float) preg_replace('/[^0-9.\-]/', '', (string) $value);
    }
}
