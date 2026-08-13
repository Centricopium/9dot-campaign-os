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

    public function getPoliticalSummary(House $house): array
    {
        return [
            'strong_congress' => $house->voters()
                ->where('support_level', 'Strong Congress')
                ->count(),

            'congress_leaning' => $house->voters()
                ->where('support_level', 'Congress Leaning')
                ->count(),

            'neutral' => $house->voters()
                ->where('support_level', 'Neutral')
                ->count(),

            'undecided' => $house->voters()
                ->where('support_level', 'Undecided')
                ->count(),

            'bjp_leaning' => $house->voters()
                ->where('support_level', 'BJP Leaning')
                ->count(),

            'strong_bjp' => $house->voters()
                ->where('support_level', 'Strong BJP')
                ->count(),

            'other' => $house->voters()
                ->where('support_level', 'Other')
                ->count(),
        ];
    }

    public function getPartySummary(House $house): array
    {
        return $house->voters()
            ->with('politicalParty')
            ->get()
            ->groupBy('political_party_id')
            ->map(function ($voters) {

                $party = $voters->first()?->politicalParty;

                return [
                    'party_id' => $party?->id,

                    'party_name' => $party?->name ?? 'Unknown',

                    'short_name' => $party?->short_name ?? '-',

                    'total' => $voters->count(),

                    'strong_congress' => $voters
                        ->where('support_level', 'Strong Congress')
                        ->count(),

                    'congress_leaning' => $voters
                        ->where('support_level', 'Congress Leaning')
                        ->count(),

                    'neutral' => $voters
                        ->where('support_level', 'Neutral')
                        ->count(),

                    'undecided' => $voters
                        ->where('support_level', 'Undecided')
                        ->count(),

                    'bjp_leaning' => $voters
                        ->where('support_level', 'BJP Leaning')
                        ->count(),

                    'strong_bjp' => $voters
                        ->where('support_level', 'Strong BJP')
                        ->count(),

                    'other' => $voters
                        ->where('support_level', 'Other')
                        ->count(),
                ];
            })
            ->values()
            ->toArray();
    }

    public function getFamilyCount(House $house): int
    {
        return $house->voters()->count();
    }
}