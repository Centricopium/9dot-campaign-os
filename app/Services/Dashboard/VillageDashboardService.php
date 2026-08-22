<?php

namespace App\Services\Dashboard;

use App\Models\PoliticalParty;
use App\Models\Village;
use App\Models\Voter;
use App\Services\Survey\BoothIntelligenceService;
use Illuminate\Support\Collection;

class VillageDashboardService
{
    /**
     * Get complete village dashboard summary.
     */
    public function summary(Village $village): array
    {
        /*
        |--------------------------------------------------------------------------
        | Village Voter Query
        |--------------------------------------------------------------------------
        */

        $query = Voter::query()
            ->whereHas('house.booth', function ($q) use ($village) {
                $q->where('village_id', $village->id);
            });


        /*
        |--------------------------------------------------------------------------
        | Basic Village Summary
        |--------------------------------------------------------------------------
        */

        $summary = [
            'houses' => $village->booths()
                ->withCount('houses')
                ->get()
                ->sum('houses_count'),

            'booths' => $village->booths()->count(),

            'voters' => (clone $query)->count(),

            'male' => (clone $query)
                ->where('gender', 'Male')
                ->count(),

            'female' => (clone $query)
                ->where('gender', 'Female')
                ->count(),

            'other' => (clone $query)
                ->whereNotIn('gender', ['Male', 'Female'])
                ->count(),

            'volunteers' => (clone $query)
                ->where('is_volunteer', true)
                ->count(),

            'influencers' => (clone $query)
                ->where('is_influencer', true)
                ->count(),

            'active_voters' => (clone $query)
                ->where('is_active', true)
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Political Support
            |--------------------------------------------------------------------------
            */

            'strong_support' => (clone $query)
                ->where('support_level', 'Strong Support')
                ->count(),

            'moderate_support' => (clone $query)
                ->where('support_level', 'Moderate Support')
                ->count(),

            'leaning_support' => (clone $query)
                ->where('support_level', 'Leaning Support')
                ->count(),

            'neutral' => (clone $query)
                ->where('support_level', 'Neutral')
                ->count(),

            'undecided' => (clone $query)
                ->where('support_level', 'Undecided')
                ->count(),

            'leaning_opposition' => (clone $query)
                ->where('support_level', 'Leaning Opposition')
                ->count(),

            'moderate_opposition' => (clone $query)
                ->where('support_level', 'Moderate Opposition')
                ->count(),

            'strong_opposition' => (clone $query)
                ->where('support_level', 'Strong Opposition')
                ->count(),

            'parties' => [],

            /*
            |--------------------------------------------------------------------------
            | Booth Intelligence
            |--------------------------------------------------------------------------
            */

            'booth_intelligence' => [],
        ];


        /*
        |--------------------------------------------------------------------------
        | Political Parties
        |--------------------------------------------------------------------------
        */

        $parties = PoliticalParty::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();


        foreach ($parties as $party) {

            $partyQuery = (clone $query)
                ->where('political_party_id', $party->id);


            $summary['parties'][] = [

                'id' => $party->id,

                'name' => $party->name,

                'short_name' => $party->short_name,

                'symbol' => $party->symbol,

                'color' => $party->color,

                'total' =>
                    (clone $partyQuery)->count(),

                'strong_support' =>
                    (clone $partyQuery)
                        ->where(
                            'support_level',
                            'Strong Support'
                        )
                        ->count(),

                'moderate_support' =>
                    (clone $partyQuery)
                        ->where(
                            'support_level',
                            'Moderate Support'
                        )
                        ->count(),

                'leaning_support' =>
                    (clone $partyQuery)
                        ->where(
                            'support_level',
                            'Leaning Support'
                        )
                        ->count(),

                'neutral' =>
                    (clone $partyQuery)
                        ->where(
                            'support_level',
                            'Neutral'
                        )
                        ->count(),

                'undecided' =>
                    (clone $partyQuery)
                        ->where(
                            'support_level',
                            'Undecided'
                        )
                        ->count(),

                'leaning_opposition' =>
                    (clone $partyQuery)
                        ->where(
                            'support_level',
                            'Leaning Opposition'
                        )
                        ->count(),

                'moderate_opposition' =>
                    (clone $partyQuery)
                        ->where(
                            'support_level',
                            'Moderate Opposition'
                        )
                        ->count(),

                'strong_opposition' =>
                    (clone $partyQuery)
                        ->where(
                            'support_level',
                            'Strong Opposition'
                        )
                        ->count(),
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | BOOTH INTELLIGENCE
        |--------------------------------------------------------------------------
        |
        | Reuse BoothIntelligenceService so Village Dashboard and
        | Booth Intelligence always use the same calculations.
        |
        */

        $boothService = app(
            BoothIntelligenceService::class
        );


        $summary['booth_intelligence'] =
            $village->booths()
                ->where('is_active', true)
                ->with('village')
                ->orderBy('booth_name')
                ->get()
                ->map(function ($booth) use ($boothService) {

                    $data =
                        $boothService->getBoothIntelligence(
                            $booth->id
                        );


                    $boothData =
                        $data['booth'] ?? [];


                    $boothSummary =
                        $data['summary'] ?? [];


                    /*
                    |--------------------------------------------------------------------------
                    | Calculate AI Risk / Priority
                    |--------------------------------------------------------------------------
                    */

                    $riskScore =
                        (float) (
                            $data['risk_score']
                            ?? 0
                        );


                    $priority =
                        $data['priority']
                        ?? 'LOW';


                    /*
                    |--------------------------------------------------------------------------
                    | Data Availability
                    |--------------------------------------------------------------------------
                    */

                    $totalVoters =
                        (int) (
                            $boothSummary['total_voters']
                            ?? 0
                        );


                    $hasData =
                        $totalVoters > 0;


                    /*
                    |--------------------------------------------------------------------------
                    | Return Booth Intelligence Row
                    |--------------------------------------------------------------------------
                    */

                    return [

                        'booth_id' =>
                            $booth->id,

                        'booth' =>
                            $boothData['name']
                            ?? $booth->booth_name
                            ?? 'Unknown Booth',

                        'booth_no' =>
                            $boothData['booth_no']
                            ?? $booth->booth_no
                            ?? '-',

                        'part_no' =>
                            $boothData['part_no']
                            ?? $booth->part_no
                            ?? '-',

                        'village' =>
                            $boothData['village']
                            ?? $booth->village?->name
                            ?? $village->name,

                        'total_voters' =>
                            $totalVoters,

                        'houses' =>
                            (int) (
                                $boothSummary['houses']
                                ?? 0
                            ),

                        'male_voters' =>
                            (int) (
                                $boothSummary['male_voters']
                                ?? 0
                            ),

                        'female_voters' =>
                            (int) (
                                $boothSummary['female_voters']
                                ?? 0
                            ),

                        'supporters' =>
                            (int) (
                                $boothSummary['supporters']
                                ?? 0
                            ),

                        'support_percentage' =>
                            (float) (
                                $boothSummary[
                                    'support_percentage'
                                ] ?? 0
                            ),

                        'neutral' =>
                            (int) (
                                $boothSummary['neutral']
                                ?? 0
                            ),

                        'neutral_percentage' =>
                            (float) (
                                $boothSummary[
                                    'neutral_percentage'
                                ] ?? 0
                            ),

                        'undecided' =>
                            (int) (
                                $boothSummary['undecided']
                                ?? 0
                            ),

                        'opposition' =>
                            (int) (
                                $boothSummary['opposition']
                                ?? 0
                            ),

                        'opposition_percentage' =>
                            (float) (
                                $boothSummary[
                                    'opposition_percentage'
                                ] ?? 0
                            ),

                        'risk_score' =>
                            $riskScore,

                        'priority' =>
                            $priority,

                        'has_data' =>
                            $hasData,

                        'data_status' =>
                            $hasData
                                ? 'READY'
                                : 'DATA PENDING',

                        'action' =>
                            $data['actions'][0]
                            ?? 'Monitor booth',
                    ];
                })
                ->sortByDesc('risk_score')
                ->values()
                ->all();


        return $summary;
    }


    /**
     * Get only booth intelligence for a village.
     */
    public function boothIntelligence(
        Village $village
    ): Collection {

        return collect(
            $this->summary($village)['booth_intelligence']
            ?? []
        );
    }
}