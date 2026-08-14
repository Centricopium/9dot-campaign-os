<?php

namespace App\Services\Dashboard;

use App\Models\Booth;
use App\Models\PoliticalParty;
use App\Models\Voter;

class BoothDashboardService
{
    public function summary(Booth $booth): array
    {
        $voterQuery = Voter::query()
            ->whereHas('house', function ($query) use ($booth) {
                $query->where('booth_id', $booth->id);
            });

        /*
        |--------------------------------------------------------------------------
        | Overall Booth Statistics
        |--------------------------------------------------------------------------
        */

        $stats = (clone $voterQuery)
            ->selectRaw('COUNT(*) as total')

            ->selectRaw("
                SUM(
                    CASE
                        WHEN gender = 'Male'
                        THEN 1
                        ELSE 0
                    END
                ) as male
            ")

            ->selectRaw("
                SUM(
                    CASE
                        WHEN gender = 'Female'
                        THEN 1
                        ELSE 0
                    END
                ) as female
            ")

            ->selectRaw("
                SUM(
                    CASE
                        WHEN gender = 'Other'
                             OR gender IS NULL
                        THEN 1
                        ELSE 0
                    END
                ) as other
            ")

            ->selectRaw("
                SUM(
                    CASE
                        WHEN is_active = 1
                        THEN 1
                        ELSE 0
                    END
                ) as active_voters
            ")

            ->selectRaw("
                SUM(
                    CASE
                        WHEN is_volunteer = 1
                        THEN 1
                        ELSE 0
                    END
                ) as volunteers
            ")

            ->selectRaw("
                SUM(
                    CASE
                        WHEN is_influencer = 1
                        THEN 1
                        ELSE 0
                    END
                ) as influencers
            ")

            ->selectRaw("
                SUM(
                    CASE
                        WHEN support_level = 'Neutral'
                        THEN 1
                        ELSE 0
                    END
                ) as neutral
            ")

            ->selectRaw("
                SUM(
                    CASE
                        WHEN support_level = 'Undecided'
                        THEN 1
                        ELSE 0
                    END
                ) as undecided
            ")

            ->first();

        /*
        |--------------------------------------------------------------------------
        | Party + Support Level Aggregation
        |--------------------------------------------------------------------------
        */

        $partyStats = (clone $voterQuery)
            ->select([
                'political_party_id',
                'support_level',
            ])
            ->selectRaw('COUNT(*) as total')
            ->whereNotNull('political_party_id')
            ->groupBy(
                'political_party_id',
                'support_level'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Active Political Parties
        |--------------------------------------------------------------------------
        */

        $parties = PoliticalParty::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $partySummary = [];

        foreach ($parties as $party) {

            $rows = $partyStats->where(
                'political_party_id',
                $party->id
            );

            $partySummary[] = [

                'id' => $party->id,

                'name' => $party->name,

                'short_name' => $party->short_name,

                'symbol' => $party->symbol,

                'color' => $party->color,

                /*
                |--------------------------------------------------------------------------
                | Total
                |--------------------------------------------------------------------------
                */

                'total' => $rows->sum('total'),

                /*
                |--------------------------------------------------------------------------
                | Support
                |--------------------------------------------------------------------------
                */

                'strong_support' => $this->supportCount(
                    $rows,
                    'Strong Support'
                ),

                'moderate_support' => $this->supportCount(
                    $rows,
                    'Moderate Support'
                ),

                'leaning_support' => $this->supportCount(
                    $rows,
                    'Leaning Support'
                ),

                /*
                |--------------------------------------------------------------------------
                | Neutral / Undecided
                |--------------------------------------------------------------------------
                */

                'neutral' => $this->supportCount(
                    $rows,
                    'Neutral'
                ),

                'undecided' => $this->supportCount(
                    $rows,
                    'Undecided'
                ),

                /*
                |--------------------------------------------------------------------------
                | Opposition
                |--------------------------------------------------------------------------
                */

                'leaning_opposition' => $this->supportCount(
                    $rows,
                    'Leaning Opposition'
                ),

                'moderate_opposition' => $this->supportCount(
                    $rows,
                    'Moderate Opposition'
                ),

                'strong_opposition' => $this->supportCount(
                    $rows,
                    'Strong Opposition'
                ),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Final Summary
        |--------------------------------------------------------------------------
        */

        return [

            'houses' => $booth->houses()->count(),

            'voters' => (int) ($stats->total ?? 0),

            'male' => (int) ($stats->male ?? 0),

            'female' => (int) ($stats->female ?? 0),

            'other' => (int) ($stats->other ?? 0),

            'active_voters' => (int) ($stats->active_voters ?? 0),

            'volunteers' => (int) ($stats->volunteers ?? 0),

            'influencers' => (int) ($stats->influencers ?? 0),

            'neutral' => (int) ($stats->neutral ?? 0),

            'undecided' => (int) ($stats->undecided ?? 0),

            'parties' => $partySummary,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Support Level Counter
    |--------------------------------------------------------------------------
    */

    protected function supportCount(
        $rows,
        string $supportLevel
    ): int {
        return (int) (
            $rows
                ->where('support_level', $supportLevel)
                ->sum('total')
        );
    }
}