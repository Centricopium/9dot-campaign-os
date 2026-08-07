<?php

namespace App\Filament\Widgets;

use App\Models\PoliticalParty;
use App\Models\Voter;
use Filament\Widgets\Widget;

class PartySupportWidget extends Widget
{
    protected string $view = 'filament.widgets.party-support-widget';

    public function getParties()
    {
        return PoliticalParty::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($party) {

                $party->count = Voter::where('political_party_id', $party->id)->count();

                $party->strong_support = Voter::where('political_party_id', $party->id)
                    ->where('support_level', 'Strong Support')
                    ->count();

                $party->support = Voter::where('political_party_id', $party->id)
                    ->where('support_level', 'Support')
                    ->count();

                $party->leaning = Voter::where('political_party_id', $party->id)
                    ->where('support_level', 'Leaning')
                    ->count();

                $party->neutral = Voter::where('political_party_id', $party->id)
                    ->where('support_level', 'Neutral')
                    ->count();

                $party->opposition_leaning = Voter::where('political_party_id', $party->id)
                    ->where('support_level', 'Opposition Leaning')
                    ->count();

                $party->strong_opposition = Voter::where('political_party_id', $party->id)
                    ->where('support_level', 'Strong Opposition')
                    ->count();

                $party->undecided = Voter::where('political_party_id', $party->id)
                    ->where('support_level', 'Undecided')
                    ->count();

                return $party;
            });
    }
}