<?php

namespace App\Filament\Widgets;

use App\Models\Booth;
use App\Models\House;
use App\Models\PoliticalParty;
use App\Models\Voter;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BoothStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $stats = [];

        // Houses
        $stats[] = Stat::make('🏠 Houses', House::count())
            ->description('Total Houses')
            ->color('primary');

        // Voters
        $stats[] = Stat::make('👥 Voters', Voter::count())
            ->description('Total Voters')
            ->color('info');

        // Booths
        $stats[] = Stat::make('🏛 Booths', Booth::count())
            ->description('Total Booths')
            ->color('success');

        // Political Parties
        foreach (
            PoliticalParty::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get() as $party
        ) {

            $count = Voter::where('political_party_id', $party->id)->count();

            $stats[] = Stat::make(
                $party->symbol . ' ' . $party->short_name,
                $count
            )
                ->description($party->name)
                ->color('gray');
        }

        // Neutral
        $stats[] = Stat::make(
            '⚪ Neutral',
            Voter::where('support_level', 'Neutral')->count()
        )
            ->color('gray');

        // Undecided
        $stats[] = Stat::make(
            '❓ Undecided',
            Voter::where('support_level', 'Undecided')->count()
        )
            ->color('warning');

        // Volunteers
        $stats[] = Stat::make(
            '🙋 Volunteers',
            Voter::where('is_volunteer', true)->count()
        )
            ->color('success');

        // Influencers
        $stats[] = Stat::make(
            '⭐ Influencers',
            Voter::where('is_influencer', true)->count()
        )
            ->color('danger');

        return $stats;
    }
}