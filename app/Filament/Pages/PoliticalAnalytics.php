<?php

namespace App\Filament\Pages;

use App\Services\Survey\PoliticalAnalyticsService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class PoliticalAnalytics extends Page
{
    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedChartBarSquare;


    protected static ?string $navigationLabel = 'Political Analytics';


    protected static string|UnitEnum|null $navigationGroup = 'Campaign';


    protected static ?int $navigationSort = 3;


    protected static ?string $title = 'Political Analytics';


    protected string $view = 'filament.pages.political-analytics';



    public function getDataProperty(): array
    {

        $service = app(
            PoliticalAnalyticsService::class
        );


        return [

    'overview' => $service->getOverview(),

    'supportAnalysis' => $service->getSupportAnalysis(),

    'partyAnalysis' => $service->getPartyAnalysis(),

    'boothAnalysis' => $service->getBoothAnalysis(),

    'villageAnalysis' => $service->getVillageAnalysis(),

];

    }

}