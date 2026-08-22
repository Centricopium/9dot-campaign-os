<?php

namespace App\Services\Survey;

use App\Models\Booth;
use App\Models\PoliticalParty;
use App\Models\Voter;
use Illuminate\Support\Collection;

class BoothIntelligenceService
{
    /**
     * Get complete intelligence for a booth.
     */
    public function getBoothIntelligence(int $boothId): array
    {
        $booth = Booth::with('village')->find($boothId);

        if (! $booth) {
            return $this->emptyBoothData();
        }

        /*
        |--------------------------------------------------------------------------
        | Booth Voters
        |--------------------------------------------------------------------------
        */

        $voters = Voter::query()
            ->with([
                'house',
                'politicalParty',
            ])
            ->whereHas('house', function ($query) use ($boothId) {
                $query->where('booth_id', $boothId);
            })
            ->get();

        $total = $voters->count();

        /*
        |--------------------------------------------------------------------------
        | Political Groups
        |--------------------------------------------------------------------------
        */

        $supporters = $this->getSupporters($voters);

        $neutral = $this->getNeutral($voters);

        $undecided = $this->getUndecided($voters);

        $opposition = $this->getOpposition($voters);

        /*
        |--------------------------------------------------------------------------
        | Percentages
        |--------------------------------------------------------------------------
        */

        $supportPercentage = $this->percentage(
            $supporters->count(),
            $total
        );

        $neutralPercentage = $this->percentage(
            $neutral->count() + $undecided->count(),
            $total
        );

        $oppositionPercentage = $this->percentage(
            $opposition->count(),
            $total
        );

        /*
        |--------------------------------------------------------------------------
        | Risk
        |--------------------------------------------------------------------------
        */

        $riskScore = $this->calculateRiskScore(
            $supportPercentage,
            $neutralPercentage,
            $oppositionPercentage
        );

        /*
        |--------------------------------------------------------------------------
        | House Intelligence
        |--------------------------------------------------------------------------
        */

        $houses = $this->getHouseAnalysis($voters);

        /*
        |--------------------------------------------------------------------------
        | Aggregate Party Intelligence
        |--------------------------------------------------------------------------
        */

        $partyIntelligence = $this->getPartyIntelligence($voters);

        /*
        |--------------------------------------------------------------------------
        | Gender-wise Party Intelligence
        |--------------------------------------------------------------------------
        */

        $genderAggregate = $this->getGenderAggregate($voters);

        /*
        |--------------------------------------------------------------------------
        | Booth Organisation Team
        |--------------------------------------------------------------------------
        */

        $organisationTeam = $this->getOrganisationTeam($voters);

        /*
        |--------------------------------------------------------------------------
        | Return
        |--------------------------------------------------------------------------
        */

        return [

            /*
            |--------------------------------------------------------------------------
            | Booth
            |--------------------------------------------------------------------------
            */

            'booth' => [

                'id' =>
                    $booth->id,

                'booth_no' =>
                    $booth->booth_no,

                'part_no' =>
                    $booth->part_no,

                'name' =>
                    $booth->booth_name,

                'category' =>
                    $booth->category,

                'village' =>
                    $booth->village?->name,

                'taluka' =>
                    $booth->village?->taluka,

                'district' =>
                    $booth->village?->district,
            ],

            /*
            |--------------------------------------------------------------------------
            | Booth Organisation Team
            |--------------------------------------------------------------------------
            */

            'organisation_team' =>
                $organisationTeam,

            /*
            |--------------------------------------------------------------------------
            | Summary
            |--------------------------------------------------------------------------
            */

            'summary' => [

                'total_voters' =>
                    $total,

                'supporters' =>
                    $supporters->count(),

                'support_percentage' =>
                    $supportPercentage,

                'neutral' =>
                    $neutral->count(),

                'neutral_percentage' =>
                    $neutralPercentage,

                'undecided' =>
                    $undecided->count(),

                'opposition' =>
                    $opposition->count(),

                'opposition_percentage' =>
                    $oppositionPercentage,

                'houses' =>
                    $voters
                        ->pluck('house_id')
                        ->filter()
                        ->unique()
                        ->count(),

                'male_voters' =>
                    $voters
                        ->where('gender', 'Male')
                        ->count(),

                'female_voters' =>
                    $voters
                        ->where('gender', 'Female')
                        ->count(),

                'other_voters' =>
                    $voters
                        ->filter(function ($voter) {
                            return ! in_array(
                                $voter->gender,
                                ['Male', 'Female'],
                                true
                            );
                        })
                        ->count(),

                'influencers' =>
                    $voters
                        ->where('is_influencer', true)
                        ->count(),

                'volunteers' =>
                    $voters
                        ->where('is_volunteer', true)
                        ->count(),
            ],

            /*
            |--------------------------------------------------------------------------
            | Gender Analysis
            |--------------------------------------------------------------------------
            */

            'gender_analysis' =>
                $this->getGenderAnalysis($voters),

            /*
            |--------------------------------------------------------------------------
            | Aggregate Party Intelligence
            |--------------------------------------------------------------------------
            */

            'party_intelligence' =>
                $partyIntelligence,

            /*
            |--------------------------------------------------------------------------
            | Gender Aggregate
            |--------------------------------------------------------------------------
            */

            'gender_aggregate' =>
                $genderAggregate,

            /*
            |--------------------------------------------------------------------------
            | Houses
            |--------------------------------------------------------------------------
            */

            'houses' =>
                $houses,

            /*
            |--------------------------------------------------------------------------
            | Voters
            |--------------------------------------------------------------------------
            */

            'voters' =>
                $voters,

            /*
            |--------------------------------------------------------------------------
            | Swing Voters
            |--------------------------------------------------------------------------
            */

            'swing_voters' =>
                $this->getSwingVoters($voters),

            /*
            |--------------------------------------------------------------------------
            | Influencers
            |--------------------------------------------------------------------------
            */

            'influencers' =>
                $voters
                    ->where('is_influencer', true)
                    ->values(),

            /*
            |--------------------------------------------------------------------------
            | Volunteers
            |--------------------------------------------------------------------------
            */

            'volunteers' =>
                $voters
                    ->where('is_volunteer', true)
                    ->values(),

            /*
            |--------------------------------------------------------------------------
            | Risk
            |--------------------------------------------------------------------------
            */

            'risk_score' =>
                $riskScore,

            'priority' =>
                $this->getPriority($riskScore),

            /*
            |--------------------------------------------------------------------------
            | Actions
            |--------------------------------------------------------------------------
            */

            'actions' =>
                $this->getActions(
                    $riskScore,
                    $neutralPercentage,
                    $supportPercentage
                ),
        ];
    }


    /**
     * Get intelligence for all active booths.
     */
    public function getAllBoothIntelligence(): Collection
    {
        return Booth::query()
            ->where('is_active', true)
            ->with('village')
            ->get()
            ->map(function ($booth) {

                $data = $this->getBoothIntelligence(
                    $booth->id
                );

                return [

                    'booth_id' =>
                        $booth->id,

                    'booth' =>
                        $booth->booth_name
                        ?? 'Unknown Booth',

                    'village' =>
                        $booth->village?->name
                        ?? 'Unknown Village',

                    'total_voters' =>
                        $data['summary']['total_voters'],

                    'male_voters' =>
                        $data['summary']['male_voters'],

                    'female_voters' =>
                        $data['summary']['female_voters'],

                    'houses' =>
                        $data['summary']['houses'],

                    'support_percentage' =>
                        $data['summary']['support_percentage'],

                    'neutral_percentage' =>
                        $data['summary']['neutral_percentage'],

                    'opposition_percentage' =>
                        $data['summary']['opposition_percentage'],

                    'risk_score' =>
                        $data['risk_score'],

                    'priority' =>
                        $data['priority'],

                    'action' =>
                        $data['actions'][0]
                        ?? 'Monitor booth',
                ];
            })
            ->sortByDesc('risk_score')
            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | Booth Organisation Team
    |--------------------------------------------------------------------------
    */

    private function getOrganisationTeam(
        Collection $voters
    ): array {

        $roles = [

            'Booth President',

            'Mahila Booth President',

            'Youth Booth President',

            'Booth Vice President',

            'Booth General Secretary',

            'Booth Secretary',

            'Booth Treasurer',

            'Panna Pramukh',

            'Polling Agent',

            'Booth Volunteer',

            'Mahila Booth Volunteer',

            'Youth Booth Volunteer',

            'Other',
        ];

        $result = [];

        foreach ($roles as $role) {

            $members = $voters
                ->filter(
                    function ($voter) use ($role) {

                        return $voter->booth_committee_role === $role;
                    }
                )
                ->map(
                    function ($voter) use ($role) {

                        return [

                            'id' =>
                                $voter->id,

                            'name' =>
                                $voter->name,

                            'mobile' =>
                                $voter->mobile,

                            'gender' =>
                                $voter->gender,

                            'role' =>
                                $role,
                        ];
                    }
                )
                ->values()
                ->toArray();

            /*
            |--------------------------------------------------------------------------
            | Only show assigned roles
            |--------------------------------------------------------------------------
            */

            if (! empty($members)) {

                $result[$role] = $members;
            }
        }

        return $result;
    }


    /*
    |--------------------------------------------------------------------------
    | Aggregate Party Intelligence
    |--------------------------------------------------------------------------
    */

    private function getPartyIntelligence(
        Collection $voters
    ): array {

        $result = [];

        $parties = PoliticalParty::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        foreach ($parties as $party) {

            $partyVoters = $voters->filter(
                function ($voter) use ($party) {

                    return
                        (int) $voter->political_party_id ===
                        (int) $party->id
                        &&
                        in_array(
                            $voter->support_level,
                            [
                                'Strong Support',
                                'Moderate Support',
                                'Leaning Support',
                            ],
                            true
                        );
                }
            );

            $result[] = [

                'id' =>
                    $party->id,

                'name' =>
                    $party->name,

                'short_name' =>
                    $party->short_name
                    ?: $party->name,

                'symbol' =>
                    $party->symbol,

                'strong' =>
                    $partyVoters
                        ->where(
                            'support_level',
                            'Strong Support'
                        )
                        ->count(),

                'moderate' =>
                    $partyVoters
                        ->where(
                            'support_level',
                            'Moderate Support'
                        )
                        ->count(),

                'leaning' =>
                    $partyVoters
                        ->where(
                            'support_level',
                            'Leaning Support'
                        )
                        ->count(),

                'total' =>
                    $partyVoters->count(),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Neutral
        |--------------------------------------------------------------------------
        */

        $result[] = [

            'id' => null,

            'name' => 'Neutral',

            'short_name' => 'Neutral',

            'symbol' => '⚪',

            'strong' => 0,

            'moderate' => 0,

            'leaning' => 0,

            'total' =>
                $voters
                    ->where(
                        'support_level',
                        'Neutral'
                    )
                    ->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Undecided
        |--------------------------------------------------------------------------
        */

        $result[] = [

            'id' => null,

            'name' => 'Undecided',

            'short_name' => 'Undecided',

            'symbol' => '🟠',

            'strong' => 0,

            'moderate' => 0,

            'leaning' => 0,

            'total' =>
                $voters
                    ->where(
                        'support_level',
                        'Undecided'
                    )
                    ->count(),
        ];

        return $result;
    }


    /*
    |--------------------------------------------------------------------------
    | Gender-wise Party Intelligence
    |--------------------------------------------------------------------------
    */

    private function getGenderAggregate(
        Collection $voters
    ): array {

        return [

            'male' =>
                $this->getGenderPartyIntelligence(
                    $voters,
                    'Male'
                ),

            'female' =>
                $this->getGenderPartyIntelligence(
                    $voters,
                    'Female'
                ),

            'other' =>
                $this->getOtherGenderPartyIntelligence(
                    $voters
                ),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Gender Party Intelligence
    |--------------------------------------------------------------------------
    */

    private function getGenderPartyIntelligence(
        Collection $voters,
        string $gender
    ): array {

        $genderVoters = $voters->where(
            'gender',
            $gender
        );

        return $this->buildGenderPartyRows(
            $genderVoters
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Other Gender Party Intelligence
    |--------------------------------------------------------------------------
    */

    private function getOtherGenderPartyIntelligence(
        Collection $voters
    ): array {

        $genderVoters = $voters->filter(
            function ($voter) {

                return ! in_array(
                    $voter->gender,
                    ['Male', 'Female'],
                    true
                );
            }
        );

        return $this->buildGenderPartyRows(
            $genderVoters
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Build Gender Party Rows
    |--------------------------------------------------------------------------
    */

    private function buildGenderPartyRows(
        Collection $voters
    ): array {

        $result = [];

        $parties = PoliticalParty::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        foreach ($parties as $party) {

            $partyVoters = $voters->filter(
                function ($voter) use ($party) {

                    return
                        (int) $voter->political_party_id ===
                        (int) $party->id
                        &&
                        in_array(
                            $voter->support_level,
                            [
                                'Strong Support',
                                'Moderate Support',
                                'Leaning Support',
                            ],
                            true
                        );
                }
            );

            $result[] = [

                'id' =>
                    $party->id,

                'name' =>
                    $party->name,

                'short_name' =>
                    $party->short_name
                    ?: $party->name,

                'symbol' =>
                    $party->symbol,

                'strong' =>
                    $partyVoters
                        ->where(
                            'support_level',
                            'Strong Support'
                        )
                        ->count(),

                'moderate' =>
                    $partyVoters
                        ->where(
                            'support_level',
                            'Moderate Support'
                        )
                        ->count(),

                'leaning' =>
                    $partyVoters
                        ->where(
                            'support_level',
                            'Leaning Support'
                        )
                        ->count(),

                'total' =>
                    $partyVoters->count(),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Neutral
        |--------------------------------------------------------------------------
        */

        $result[] = [

            'id' => null,

            'name' => 'Neutral',

            'short_name' => 'Neutral',

            'symbol' => '⚪',

            'strong' => 0,

            'moderate' => 0,

            'leaning' => 0,

            'total' =>
                $voters
                    ->where(
                        'support_level',
                        'Neutral'
                    )
                    ->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Undecided
        |--------------------------------------------------------------------------
        */

        $result[] = [

            'id' => null,

            'name' => 'Undecided',

            'short_name' => 'Undecided',

            'symbol' => '🟠',

            'strong' => 0,

            'moderate' => 0,

            'leaning' => 0,

            'total' =>
                $voters
                    ->where(
                        'support_level',
                        'Undecided'
                    )
                    ->count(),
        ];

        return $result;
    }


    /*
    |--------------------------------------------------------------------------
    | Gender Analysis
    |--------------------------------------------------------------------------
    */

    private function getGenderAnalysis(
        Collection $voters
    ): array {

        return [

            'male' =>
                $this->getGenderStats(
                    $voters,
                    'Male'
                ),

            'female' =>
                $this->getGenderStats(
                    $voters,
                    'Female'
                ),

            'other' =>
                $this->getOtherGenderStats(
                    $voters
                ),
        ];
    }


    private function getGenderStats(
        Collection $voters,
        string $gender
    ): array {

        return $this->buildPoliticalStats(
            $voters->where(
                'gender',
                $gender
            )
        );
    }


    private function getOtherGenderStats(
        Collection $voters
    ): array {

        return $this->buildPoliticalStats(
            $voters->filter(
                function ($voter) {

                    return ! in_array(
                        $voter->gender,
                        ['Male', 'Female'],
                        true
                    );
                }
            )
        );
    }


    private function buildPoliticalStats(
        Collection $voters
    ): array {

        $total =
            $voters->count();

        $supporters =
            $this->getSupporters($voters)->count();

        $neutral =
            $this->getNeutral($voters)->count();

        $undecided =
            $this->getUndecided($voters)->count();

        $opposition =
            $this->getOpposition($voters)->count();

        return [

            'total' =>
                $total,

            'supporters' =>
                $supporters,

            'support_percentage' =>
                $this->percentage(
                    $supporters,
                    $total
                ),

            'neutral' =>
                $neutral,

            'neutral_percentage' =>
                $this->percentage(
                    $neutral,
                    $total
                ),

            'undecided' =>
                $undecided,

            'opposition' =>
                $opposition,

            'opposition_percentage' =>
                $this->percentage(
                    $opposition,
                    $total
                ),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | House Intelligence
    |--------------------------------------------------------------------------
    */

    private function getHouseAnalysis(
        Collection $voters
    ): Collection {

        return $voters
            ->filter(
                fn ($voter) =>
                    ! empty($voter->house_id)
            )
            ->groupBy('house_id')
            ->map(function ($houseVoters) {

                $house =
                    $houseVoters
                        ->first()
                        ?->house;

                $votersCount =
                    $houseVoters->count();

                $supporters =
                    $this->getSupporters(
                        $houseVoters
                    )->count();

                $neutral =
                    $this->getNeutral(
                        $houseVoters
                    )->count();

                $undecided =
                    $this->getUndecided(
                        $houseVoters
                    )->count();

                $opposition =
                    $this->getOpposition(
                        $houseVoters
                    )->count();

                $supportPercentage =
                    $this->percentage(
                        $supporters,
                        $votersCount
                    );

                $neutralPercentage =
                    $this->percentage(
                        $neutral + $undecided,
                        $votersCount
                    );

                $oppositionPercentage =
                    $this->percentage(
                        $opposition,
                        $votersCount
                    );

                $riskScore =
                    $this->calculateRiskScore(
                        $supportPercentage,
                        $neutralPercentage,
                        $oppositionPercentage
                    );

                $head =
                    $house?->head_of_family
                    ??
                    $houseVoters
                        ->sortByDesc('age')
                        ->first()
                        ?->name;

                $mobile =
                    $house?->mobile
                    ??
                    $houseVoters
                        ->pluck('mobile')
                        ->filter()
                        ->first();

                $address =
                    $house?->address
                    ??
                    $houseVoters
                        ->pluck('address')
                        ->filter()
                        ->first();

                return [

                    'house_id' =>
                        $house?->id,

                    'house_no' =>
                        $house?->house_no
                        ?? 'Unknown',

                    'head_of_family' =>
                        $head,

                    'mobile' =>
                        $mobile,

                    'address' =>
                        $address,

                    'voters_count' =>
                        $votersCount,

                    'total_voters' =>
                        $votersCount,

                    'male_voters' =>
                        $houseVoters
                            ->where(
                                'gender',
                                'Male'
                            )
                            ->count(),

                    'female_voters' =>
                        $houseVoters
                            ->where(
                                'gender',
                                'Female'
                            )
                            ->count(),

                    'supporters' =>
                        $supporters,

                    'support_percentage' =>
                        $supportPercentage,

                    'neutral' =>
                        $neutral,

                    'neutral_percentage' =>
                        $neutralPercentage,

                    'undecided' =>
                        $undecided,

                    'opposition' =>
                        $opposition,

                    'opposition_percentage' =>
                        $oppositionPercentage,

                    'influencers' =>
                        $houseVoters
                            ->where(
                                'is_influencer',
                                true
                            )
                            ->count(),

                    'volunteers' =>
                        $houseVoters
                            ->where(
                                'is_volunteer',
                                true
                            )
                            ->count(),

                    'risk_score' =>
                        $riskScore,

                    'priority' =>
                        $this->getPriority(
                            $riskScore
                        ),
                ];
            })
            ->sortByDesc('risk_score')
            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | Political Groups
    |--------------------------------------------------------------------------
    */

    private function getSupporters(
        Collection $voters
    ): Collection {

        return $voters->whereIn(
            'support_level',
            [
                'Strong Support',
                'Moderate Support',
                'Leaning Support',
            ]
        );
    }


    private function getNeutral(
        Collection $voters
    ): Collection {

        return $voters->where(
            'support_level',
            'Neutral'
        );
    }


    private function getUndecided(
        Collection $voters
    ): Collection {

        return $voters->where(
            'support_level',
            'Undecided'
        );
    }


    private function getOpposition(
        Collection $voters
    ): Collection {

        return $voters->whereIn(
            'support_level',
            [
                'Strong Opposition',
                'Moderate Opposition',
                'Leaning Opposition',
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Swing Voters
    |--------------------------------------------------------------------------
    */

    private function getSwingVoters(
        Collection $voters
    ): Collection {

        return $voters
            ->whereIn(
                'support_level',
                [
                    'Neutral',
                    'Undecided',
                    'Leaning Support',
                    'Leaning Opposition',
                ]
            )
            ->sortByDesc(function ($voter) {

                return match ($voter->priority) {

                    'High' => 3,

                    'Medium' => 2,

                    default => 1,
                };
            })
            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | AI Risk Score
    |--------------------------------------------------------------------------
    */

    private function calculateRiskScore(
        float $support,
        float $neutral,
        float $opposition
    ): float {

        $score =
            ($neutral * 0.85)
            + ($opposition * 0.15)
            - ($support * 0.20);

        return round(
            max(
                0,
                min(
                    100,
                    $score
                )
            ),
            2
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Priority
    |--------------------------------------------------------------------------
    */

    private function getPriority(
        float $riskScore
    ): string {

        return match (true) {

            $riskScore >= 70 =>
                'HIGH',

            $riskScore >= 40 =>
                'MEDIUM',

            default =>
                'LOW',
        };
    }


    /*
    |--------------------------------------------------------------------------
    | Recommended Actions
    |--------------------------------------------------------------------------
    */

    private function getActions(
        float $riskScore,
        float $neutralPercentage,
        float $supportPercentage
    ): array {

        if ($riskScore >= 70) {

            return [

                'Review booth operational status',

                'Increase voter contact coverage',

                'Review local issue feedback',

                'Check booth team readiness',

                'Prepare booth action plan',
            ];
        }

        if ($riskScore >= 40) {

            return [

                'Increase voter contact coverage',

                'Review pending follow-ups',

                'Strengthen booth operations',

                'Review local issue feedback',
            ];
        }

        if ($supportPercentage >= 50) {

            return [

                'Maintain regular voter contact',

                'Review booth network',

                'Identify additional volunteers',

                'Keep voter records updated',
            ];
        }

        return [

            'Monitor voter sentiment',

            'Increase voter engagement',

            'Collect local issue feedback',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Percentage
    |--------------------------------------------------------------------------
    */

    private function percentage(
        int $value,
        int $total
    ): float {

        return $total > 0
            ? round(
                ($value / $total) * 100,
                2
            )
            : 0;
    }


    /*
    |--------------------------------------------------------------------------
    | Empty Booth Data
    |--------------------------------------------------------------------------
    */

    private function emptyBoothData(): array
    {
        return [

            'booth' => null,

            'organisation_team' => [],

            'summary' => $this->emptySummary(),

            'gender_analysis' =>
                $this->emptyGenderAnalysis(),

            'party_intelligence' => [],

            'gender_aggregate' => [

                'male' => [
                    'total' => 0,
                    'supporters' => 0,
                    'neutral' => 0,
                    'undecided' => 0,
                    'opposition' => 0,
                ],

                'female' => [
                    'total' => 0,
                    'supporters' => 0,
                    'neutral' => 0,
                    'undecided' => 0,
                    'opposition' => 0,
                ],

                'other' => [
                    'total' => 0,
                    'supporters' => 0,
                    'neutral' => 0,
                    'undecided' => 0,
                    'opposition' => 0,
                ],
            ],

            'houses' =>
                collect(),

            'voters' =>
                collect(),

            'swing_voters' =>
                collect(),

            'influencers' =>
                collect(),

            'volunteers' =>
                collect(),

            'risk_score' =>
                0,

            'priority' =>
                'LOW',

            'actions' =>
                [],
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Empty Summary
    |--------------------------------------------------------------------------
    */

    private function emptySummary(): array
    {
        return [

            'total_voters' => 0,

            'supporters' => 0,

            'support_percentage' => 0,

            'neutral' => 0,

            'neutral_percentage' => 0,

            'undecided' => 0,

            'opposition' => 0,

            'opposition_percentage' => 0,

            'houses' => 0,

            'male_voters' => 0,

            'female_voters' => 0,

            'other_voters' => 0,

            'influencers' => 0,

            'volunteers' => 0,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Empty Gender Analysis
    |--------------------------------------------------------------------------
    */

    private function emptyGenderAnalysis(): array
    {
        return [

            'male' =>
                $this->emptyGenderStats(),

            'female' =>
                $this->emptyGenderStats(),

            'other' =>
                $this->emptyGenderStats(),
        ];
    }


    private function emptyGenderStats(): array
    {
        return [

            'total' => 0,

            'supporters' => 0,

            'support_percentage' => 0,

            'neutral' => 0,

            'neutral_percentage' => 0,

            'undecided' => 0,

            'opposition' => 0,

            'opposition_percentage' => 0,
        ];
    }
}