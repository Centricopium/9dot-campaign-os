<?php

namespace App\Services\Dashboard;

use App\Models\Booth;
use App\Models\PoliticalParty;
use App\Models\Voter;

class BoothDashboardService
{
    public function summary(Booth $booth): array
    {
        $query = Voter::whereHas('house', function ($q) use ($booth) {
            $q->where('booth_id', $booth->id);
        });

        $summary = [

            'houses' => $booth->houses()->count(),

            'voters' => (clone $query)->count(),

            'neutral' => (clone $query)
                ->where('support_level', 'Neutral')
                ->count(),

            'undecided' => (clone $query)
                ->where('support_level', 'Undecided')
                ->count(),

            'volunteers' => (clone $query)
                ->where('is_volunteer', true)
                ->count(),

            'influencers' => (clone $query)
                ->where('is_influencer', true)
                ->count(),

            'parties' => [],

        ];

        $parties = PoliticalParty::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        foreach ($parties as $party) {

            $summary['parties'][] = [

                'id' => $party->id,

                'name' => $party->name,

                'short_name' => $party->short_name,

                'symbol' => $party->symbol,

                'color' => $party->color,

                'count' => (clone $query)
                    ->where('political_party_id', $party->id)
                    ->count(),

                'strong_support' => (clone $query)
                    ->where('political_party_id', $party->id)
                    ->where('support_level', 'Strong Support')
                    ->count(),

                'support' => (clone $query)
                    ->where('political_party_id', $party->id)
                    ->where('support_level', 'Support')
                    ->count(),

                'leaning' => (clone $query)
                    ->where('political_party_id', $party->id)
                    ->where('support_level', 'Leaning')
                    ->count(),

                'neutral' => (clone $query)
                    ->where('political_party_id', $party->id)
                    ->where('support_level', 'Neutral')
                    ->count(),

                'opposition_leaning' => (clone $query)
                    ->where('political_party_id', $party->id)
                    ->where('support_level', 'Opposition Leaning')
                    ->count(),

                'strong_opposition' => (clone $query)
                    ->where('political_party_id', $party->id)
                    ->where('support_level', 'Strong Opposition')
                    ->count(),

                'undecided' => (clone $query)
                    ->where('political_party_id', $party->id)
                    ->where('support_level', 'Undecided')
                    ->count(),

            ];
        }

        return $summary;
    }
}