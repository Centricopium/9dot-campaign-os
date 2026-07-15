<?php

namespace App\Filament\Widgets;

use App\Models\Booth;
use App\Models\Constituency;
use App\Models\House;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\User;
use App\Models\Village;
use App\Models\Voter;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CampaignStats extends StatsOverviewWidget

{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [

            Stat::make('Total Voters', number_format(Voter::count()))
                ->description('Registered voters')
                ->color('primary'),

            Stat::make('Total Houses', number_format(House::count()))
                ->description('Mapped houses')
                ->color('success'),

            Stat::make('Total Booths', number_format(Booth::count()))
                ->description('Polling booths')
                ->color('warning'),

            Stat::make('Total Villages', number_format(Village::count()))
                ->description('Villages')
                ->color('info'),

            Stat::make('Constituencies', number_format(Constituency::count()))
                ->description('Assembly constituencies')
                ->color('gray'),

            Stat::make('Surveys', number_format(Survey::count()))
                ->description('Available surveys')
                ->color('success'),

            Stat::make('Responses', number_format(SurveyResponse::count()))
                ->description('Survey responses')
                ->color('primary'),

            Stat::make('Active Users', User::where('is_active', true)->count())
                ->description('Campaign users')
                ->color('warning'),

        ];
    }
}