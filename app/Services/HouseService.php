<?php

namespace App\Services;

use App\Models\House;

class HouseService
{
    public function getHouse(int $houseId): ?House
    {
        return House::with([
            'booth.village.constituency',
            'voters.politicalParty',
        ])->find($houseId);
    }

    /*
    |--------------------------------------------------------------------------
    | Political Intelligence
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | Political Intelligence must use the same support_level values
    | used by the Voter, Booth, Village and Constituency dashboards.
    |
    */

    public function getPoliticalSummary(House $house): array
    {
        $voters = $house->voters;

        return [

            'strong_support' => $voters
                ->where('support_level', 'Strong Support')
                ->count(),

            'moderate_support' => $voters
                ->where('support_level', 'Moderate Support')
                ->count(),

            'leaning_support' => $voters
                ->where('support_level', 'Leaning Support')
                ->count(),

            'neutral' => $voters
                ->where('support_level', 'Neutral')
                ->count(),

            'undecided' => $voters
                ->where('support_level', 'Undecided')
                ->count(),

            'leaning_opposition' => $voters
                ->where('support_level', 'Leaning Opposition')
                ->count(),

            'moderate_opposition' => $voters
                ->where('support_level', 'Moderate Opposition')
                ->count(),

            'strong_opposition' => $voters
                ->where('support_level', 'Strong Opposition')
                ->count(),

            'total_voters' => $voters->count(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Political Party Summary
    |--------------------------------------------------------------------------
    */

    public function getPartySummary(House $house): array
    {
        return $house->voters
            ->groupBy('political_party_id')
            ->map(function ($voters) {

                $party = $voters->first()?->politicalParty;

                return [

                    'party_id' => $party?->id,

                    'party_name' => $party?->name ?? 'Unknown',

                    'short_name' => $party?->short_name ?? '-',

                    'symbol' => $party?->symbol ?? null,

                    'color' => $party?->color ?? null,

                    'total' => $voters->count(),

                    'strong_support' => $voters
                        ->where('support_level', 'Strong Support')
                        ->count(),

                    'moderate_support' => $voters
                        ->where('support_level', 'Moderate Support')
                        ->count(),

                    'leaning_support' => $voters
                        ->where('support_level', 'Leaning Support')
                        ->count(),

                    'neutral' => $voters
                        ->where('support_level', 'Neutral')
                        ->count(),

                    'undecided' => $voters
                        ->where('support_level', 'Undecided')
                        ->count(),

                    'leaning_opposition' => $voters
                        ->where('support_level', 'Leaning Opposition')
                        ->count(),

                    'moderate_opposition' => $voters
                        ->where('support_level', 'Moderate Opposition')
                        ->count(),

                    'strong_opposition' => $voters
                        ->where('support_level', 'Strong Opposition')
                        ->count(),
                ];
            })
            ->values()
            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Family / Household Voter Count
    |--------------------------------------------------------------------------
    */

    public function getFamilyCount(House $house): int
    {
        return $house->voters()->count();
    }
}