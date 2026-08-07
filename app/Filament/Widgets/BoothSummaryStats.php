<?php

namespace App\Filament\Widgets;

use App\Models\House;
use App\Models\Voter;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BoothSummaryStats extends StatsOverviewWidget
{
    public ?int $boothId = null;

    protected function getStats(): array
    {
        $houseQuery = House::query();

        $voterQuery = Voter::query();

        if ($this->boothId) {

            $houseQuery->where('booth_id', $this->boothId);

            $voterQuery->whereHas('house', function ($query) {
                $query->where('booth_id', $this->boothId);
            });
        }

        return [

            Stat::make('🏠 Houses', $houseQuery->count())
                ->description('Total Houses')
                ->color('primary'),

            Stat::make('👥 Voters', $voterQuery->count())
                ->description('Total Voters')
                ->color('success'),

            Stat::make(
                '🙋 Volunteers',
                (clone $voterQuery)->where('is_volunteer', true)->count()
            )
                ->color('warning'),

            Stat::make(
                '⭐ Influencers',
                (clone $voterQuery)->where('is_influencer', true)->count()
            )
                ->color('danger'),

        ];
    }
}