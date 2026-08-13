<?php

namespace App\Services\Dashboard;

use App\Models\Constituency;
use App\Models\PoliticalParty;
use Illuminate\Support\Facades\DB;

class ConstituencyDashboardService
{
    public function summary(Constituency $constituency): array
    {
        $voterBase = DB::table('voters')
            ->join('houses', 'houses.id', '=', 'voters.house_id')
            ->join('booths', 'booths.id', '=', 'houses.booth_id')
            ->join('villages', 'villages.id', '=', 'booths.village_id')
            ->where('villages.constituency_id', $constituency->id);

        /*
        |--------------------------------------------------------------------------
        | Main voter aggregation
        |--------------------------------------------------------------------------
        */

        $stats = (clone $voterBase)
            ->selectRaw('COUNT(*) as voters')
            ->selectRaw("SUM(CASE WHEN voters.gender = 'Male' THEN 1 ELSE 0 END) as male")
            ->selectRaw("SUM(CASE WHEN voters.gender = 'Female' THEN 1 ELSE 0 END) as female")
            ->selectRaw("SUM(CASE WHEN voters.gender NOT IN ('Male', 'Female') OR voters.gender IS NULL THEN 1 ELSE 0 END) as other")
            ->selectRaw("SUM(CASE WHEN voters.is_volunteer = 1 THEN 1 ELSE 0 END) as volunteers")
            ->selectRaw("SUM(CASE WHEN voters.is_influencer = 1 THEN 1 ELSE 0 END) as influencers")
            ->selectRaw("SUM(CASE WHEN voters.is_active = 1 THEN 1 ELSE 0 END) as active_voters")
            ->selectRaw("SUM(CASE WHEN voters.support_level = 'Neutral' THEN 1 ELSE 0 END) as neutral")
            ->selectRaw("SUM(CASE WHEN voters.support_level = 'Undecided' THEN 1 ELSE 0 END) as undecided")
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Basic master counts
        |--------------------------------------------------------------------------
        */

        $villages = DB::table('villages')
            ->where('constituency_id', $constituency->id)
            ->count();

        $booths = DB::table('booths')
            ->join('villages', 'villages.id', '=', 'booths.village_id')
            ->where('villages.constituency_id', $constituency->id)
            ->count();

        $houses = DB::table('houses')
            ->join('booths', 'booths.id', '=', 'houses.booth_id')
            ->join('villages', 'villages.id', '=', 'booths.village_id')
            ->where('villages.constituency_id', $constituency->id)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Party + Support aggregation
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

                'total' => $rows->sum('total'),

                'strong_congress' => $this->supportCount(
                    $rows,
                    'Strong Congress'
                ),

                'congress_leaning' => $this->supportCount(
                    $rows,
                    'Congress Leaning'
                ),

                'neutral' => $this->supportCount(
                    $rows,
                    'Neutral'
                ),

                'undecided' => $this->supportCount(
                    $rows,
                    'Undecided'
                ),

                'bjp_leaning' => $this->supportCount(
                    $rows,
                    'BJP Leaning'
                ),

                'strong_bjp' => $this->supportCount(
                    $rows,
                    'Strong BJP'
                ),

                'other' => $this->supportCount(
                    $rows,
                    'Other'
                ),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Village-wise aggregation
        |--------------------------------------------------------------------------
        */

        $villageStats = DB::table('villages')
            ->leftJoin('booths', 'booths.village_id', '=', 'villages.id')
            ->leftJoin('houses', 'houses.booth_id', '=', 'booths.id')
            ->leftJoin('voters', 'voters.house_id', '=', 'houses.id')
            ->where('villages.constituency_id', $constituency->id)
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
            ->selectRaw('COUNT(DISTINCT booths.id) as booths')
            ->selectRaw('COUNT(DISTINCT houses.id) as houses')
            ->selectRaw('COUNT(voters.id) as voters')
            ->orderBy('villages.name')
            ->get();

        return [
            'villages' => $villages,
            'booths' => $booths,
            'houses' => $houses,

            'voters' => (int) ($stats->voters ?? 0),
            'male' => (int) ($stats->male ?? 0),
            'female' => (int) ($stats->female ?? 0),
            'other' => (int) ($stats->other ?? 0),

            'volunteers' => (int) ($stats->volunteers ?? 0),
            'influencers' => (int) ($stats->influencers ?? 0),
            'active_voters' => (int) ($stats->active_voters ?? 0),

            'neutral' => (int) ($stats->neutral ?? 0),
            'undecided' => (int) ($stats->undecided ?? 0),

            'parties' => $partySummary,

            'villages_list' => $villageStats,
        ];
    }

    protected function supportCount($rows, string $level): int
    {
        return (int) $rows
            ->where('support_level', $level)
            ->sum('total');
    }
}