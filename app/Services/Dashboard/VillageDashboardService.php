<?php

namespace App\Services\Dashboard;

use App\Models\PoliticalParty;
use App\Models\Village;
use App\Models\Voter;

class VillageDashboardService
{
    public function summary(Village $village): array
    {
        $query = Voter::query()
            ->whereHas('house.booth', function ($q) use ($village) {
                $q->where('village_id', $village->id);
            });

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

            'neutral' => (clone $query)
                ->where('support_level', 'Neutral')
                ->count(),

            'undecided' => (clone $query)
                ->where('support_level', 'Undecided')
                ->count(),

            'strong_support' => (clone $query)
                ->where('support_level', 'Strong Support')
                ->count(),

            'moderate_support' => (clone $query)
                ->where('support_level', 'Moderate Support')
                ->count(),

            'leaning_support' => (clone $query)
                ->where('support_level', 'Leaning Support')
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
        ];

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

                'total' => (clone $partyQuery)->count(),

                'strong_support' => (clone $partyQuery)
                    ->where('support_level', 'Strong Support')
                    ->count(),

                'moderate_support' => (clone $partyQuery)
                    ->where('support_level', 'Moderate Support')
                    ->count(),

                'leaning_support' => (clone $partyQuery)
                    ->where('support_level', 'Leaning Support')
                    ->count(),

                'neutral' => (clone $partyQuery)
                    ->where('support_level', 'Neutral')
                    ->count(),

                'undecided' => (clone $partyQuery)
                    ->where('support_level', 'Undecided')
                    ->count(),

                'leaning_opposition' => (clone $partyQuery)
                    ->where('support_level', 'Leaning Opposition')
                    ->count(),

                'moderate_opposition' => (clone $partyQuery)
                    ->where('support_level', 'Moderate Opposition')
                    ->count(),

                'strong_opposition' => (clone $partyQuery)
                    ->where('support_level', 'Strong Opposition')
                    ->count(),
            ];
        }

        return $summary;
    }
}