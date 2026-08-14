<?php

namespace App\Services\Dashboard;

use App\Models\Constituency;
use App\Models\PoliticalParty;
use Illuminate\Support\Facades\DB;

class ConstituencyDashboardService
{
    public function summary(Constituency $constituency): array
    {
        /*
        |--------------------------------------------------------------------------
        | Base Voter Query
        |--------------------------------------------------------------------------
        */

        $voterBase = DB::table('voters')
            ->join(
                'houses',
                'houses.id',
                '=',
                'voters.house_id'
            )
            ->join(
                'booths',
                'booths.id',
                '=',
                'houses.booth_id'
            )
            ->join(
                'villages',
                'villages.id',
                '=',
                'booths.village_id'
            )
            ->where(
                'villages.constituency_id',
                $constituency->id
            );

        /*
        |--------------------------------------------------------------------------
        | Main Voter Statistics
        |--------------------------------------------------------------------------
        */

        $stats = (clone $voterBase)
            ->selectRaw('COUNT(*) as voters')

            // Gender
            ->selectRaw("
                SUM(
                    CASE
                        WHEN voters.gender = 'Male'
                        THEN 1
                        ELSE 0
                    END
                ) as male
            ")

            ->selectRaw("
                SUM(
                    CASE
                        WHEN voters.gender = 'Female'
                        THEN 1
                        ELSE 0
                    END
                ) as female
            ")

            ->selectRaw("
                SUM(
                    CASE
                        WHEN voters.gender NOT IN ('Male', 'Female')
                             OR voters.gender IS NULL
                        THEN 1
                        ELSE 0
                    END
                ) as other
            ")

            // Campaign People
            ->selectRaw("
                SUM(
                    CASE
                        WHEN voters.is_volunteer = 1
                        THEN 1
                        ELSE 0
                    END
                ) as volunteers
            ")

            ->selectRaw("
                SUM(
                    CASE
                        WHEN voters.is_influencer = 1
                        THEN 1
                        ELSE 0
                    END
                ) as influencers
            ")

            ->selectRaw("
                SUM(
                    CASE
                        WHEN voters.is_active = 1
                        THEN 1
                        ELSE 0
                    END
                ) as active_voters
            ")

            // Support Levels
            ->selectRaw("
                SUM(
                    CASE
                        WHEN voters.support_level = 'Strong Support'
                        THEN 1
                        ELSE 0
                    END
                ) as strong_support
            ")

            ->selectRaw("
                SUM(
                    CASE
                        WHEN voters.support_level = 'Moderate Support'
                        THEN 1
                        ELSE 0
                    END
                ) as moderate_support
            ")

            ->selectRaw("
                SUM(
                    CASE
                        WHEN voters.support_level = 'Leaning Support'
                        THEN 1
                        ELSE 0
                    END
                ) as leaning_support
            ")

            ->selectRaw("
                SUM(
                    CASE
                        WHEN voters.support_level = 'Neutral'
                        THEN 1
                        ELSE 0
                    END
                ) as neutral
            ")

            ->selectRaw("
                SUM(
                    CASE
                        WHEN voters.support_level = 'Undecided'
                        THEN 1
                        ELSE 0
                    END
                ) as undecided
            ")

            ->selectRaw("
                SUM(
                    CASE
                        WHEN voters.support_level = 'Leaning Opposition'
                        THEN 1
                        ELSE 0
                    END
                ) as leaning_opposition
            ")

            ->selectRaw("
                SUM(
                    CASE
                        WHEN voters.support_level = 'Moderate Opposition'
                        THEN 1
                        ELSE 0
                    END
                ) as moderate_opposition
            ")

            ->selectRaw("
                SUM(
                    CASE
                        WHEN voters.support_level = 'Strong Opposition'
                        THEN 1
                        ELSE 0
                    END
                ) as strong_opposition
            ")

            ->first();

        /*
        |--------------------------------------------------------------------------
        | Basic Master Counts
        |--------------------------------------------------------------------------
        */

        // Villages
        $villages = DB::table('villages')
            ->where(
                'constituency_id',
                $constituency->id
            )
            ->count();

        // Booths
        $booths = DB::table('booths')
            ->join(
                'villages',
                'villages.id',
                '=',
                'booths.village_id'
            )
            ->where(
                'villages.constituency_id',
                $constituency->id
            )
            ->count();

        // Houses
        $houses = DB::table('houses')
            ->join(
                'booths',
                'booths.id',
                '=',
                'houses.booth_id'
            )
            ->join(
                'villages',
                'villages.id',
                '=',
                'booths.village_id'
            )
            ->where(
                'villages.constituency_id',
                $constituency->id
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Party + Support Aggregation
        |--------------------------------------------------------------------------
        */

        $partyStats = (clone $voterBase)
            ->select([
                'voters.political_party_id',
                'voters.support_level',
            ])
            ->selectRaw('COUNT(*) as total')
            ->groupBy(
                'voters.political_party_id',
                'voters.support_level'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Active Political Parties
        |--------------------------------------------------------------------------
        */

        $parties = PoliticalParty::query()
            ->where(
                'is_active',
                true
            )
            ->orderBy(
                'sort_order'
            )
            ->get();

        $partySummary = [];

        foreach ($parties as $party) {

            $rows = $partyStats->where(
                'political_party_id',
                $party->id
            );

            $partySummary[] = [

                'id' => (int) $party->id,

                'name' => $party->name,

                'short_name' => $party->short_name,

                'symbol' => $party->symbol,

                'color' => $party->color,

                'total' => (int) $rows->sum('total'),

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

                'neutral' => $this->supportCount(
                    $rows,
                    'Neutral'
                ),

                'undecided' => $this->supportCount(
                    $rows,
                    'Undecided'
                ),

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
        | Village-wise Summary
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | DB::table()->get() returns stdClass objects.
        | We convert every row into a plain PHP array.
        |
        */

        $villageStats = DB::table('villages')
            ->leftJoin(
                'booths',
                'booths.village_id',
                '=',
                'villages.id'
            )
            ->leftJoin(
                'houses',
                'houses.booth_id',
                '=',
                'booths.id'
            )
            ->leftJoin(
                'voters',
                'voters.house_id',
                '=',
                'houses.id'
            )
            ->where(
                'villages.constituency_id',
                $constituency->id
            )
            ->groupBy(
                'villages.id',
                'villages.name',
                'villages.taluka'
            )
            ->select([
                'villages.id',
                'villages.name',
                'villages.taluka',
            ])
            ->selectRaw(
                'COUNT(DISTINCT booths.id) as booths'
            )
            ->selectRaw(
                'COUNT(DISTINCT houses.id) as houses'
            )
            ->selectRaw(
                'COUNT(DISTINCT voters.id) as voters'
            )
            ->orderBy(
                'villages.name'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Convert Village Statistics to Arrays
        |--------------------------------------------------------------------------
        */

        $villagesList = $villageStats
            ->map(function ($village) {

                return [
                    'id' => (int) ($village->id ?? 0),

                    'name' => (string) ($village->name ?? ''),

                    'taluka' => (string) ($village->taluka ?? ''),

                    'booths' => (int) ($village->booths ?? 0),

                    'houses' => (int) ($village->houses ?? 0),

                    'voters' => (int) ($village->voters ?? 0),
                ];
            })
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | Final Dashboard Summary
        |--------------------------------------------------------------------------
        */

        return [

            /*
            |--------------------------------------------------------------------------
            | Master Counts
            |--------------------------------------------------------------------------
            */

            'villages' => (int) $villages,

            'booths' => (int) $booths,

            'houses' => (int) $houses,

            /*
            |--------------------------------------------------------------------------
            | Voter Counts
            |--------------------------------------------------------------------------
            */

            'voters' => (int) ($stats->voters ?? 0),

            'male' => (int) ($stats->male ?? 0),

            'female' => (int) ($stats->female ?? 0),

            'other' => (int) ($stats->other ?? 0),

            /*
            |--------------------------------------------------------------------------
            | Campaign People
            |--------------------------------------------------------------------------
            */

            'volunteers' => (int) ($stats->volunteers ?? 0),

            'influencers' => (int) ($stats->influencers ?? 0),

            'active_voters' => (int) ($stats->active_voters ?? 0),

            /*
            |--------------------------------------------------------------------------
            | Political Support
            |--------------------------------------------------------------------------
            */

            'strong_support' => (int) ($stats->strong_support ?? 0),

            'moderate_support' => (int) ($stats->moderate_support ?? 0),

            'leaning_support' => (int) ($stats->leaning_support ?? 0),

            'neutral' => (int) ($stats->neutral ?? 0),

            'undecided' => (int) ($stats->undecided ?? 0),

            'leaning_opposition' => (int) ($stats->leaning_opposition ?? 0),

            'moderate_opposition' => (int) ($stats->moderate_opposition ?? 0),

            'strong_opposition' => (int) ($stats->strong_opposition ?? 0),

            /*
            |--------------------------------------------------------------------------
            | Party Summary
            |--------------------------------------------------------------------------
            */

            'parties' => $partySummary,

            /*
            |--------------------------------------------------------------------------
            | Village Summary
            |--------------------------------------------------------------------------
            */

            'villages_list' => $villagesList,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Support Level Counter
    |--------------------------------------------------------------------------
    */

    protected function supportCount(
        $rows,
        string $level
    ): int {

        return (int) $rows
            ->where(
                'support_level',
                $level
            )
            ->sum('total');
    }
}