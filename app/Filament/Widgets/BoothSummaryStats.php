<?php

namespace App\Filament\Widgets;

use App\Models\House;
use App\Models\Voter;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BoothSummaryStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [

            Stat::make('🏠 Houses', House::count())
                ->description('Total Houses')
                ->color('primary'),

            Stat::make('👥 Voters', Voter::count())
                ->description('Total Voters')
                ->color('success'),

            Stat::make(
                '🙋 Volunteers',
                Voter::where('is_volunteer', true)->count()
            )
                ->color('warning'),

            Stat::make(
                '⭐ Influencers',
                Voter::where('is_influencer', true)->count()
            )
                ->color('danger'),

        ];
    }
}