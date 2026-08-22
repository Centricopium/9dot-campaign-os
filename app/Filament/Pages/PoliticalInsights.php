<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Services\Survey\PoliticalInsightService;
use App\Services\Survey\AIStrategyService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class PoliticalInsights extends Page
{
    use AuthorizesPagePermission;
    protected static string $requiredPermission = 'report.view';

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedSparkles;


    protected static ?string $navigationLabel =
        'AI Political Insights';


    protected static string|UnitEnum|null $navigationGroup =
        'Campaign';


    protected static ?int $navigationSort =
        4;


    protected static ?string $title =
        'AI Political Insights';


    protected string $view =
        'filament.pages.political-insights';



    public function getDataProperty(): array
    {

        $politicalInsightService = app(
            PoliticalInsightService::class
        );


        $aiStrategyService = app(
            AIStrategyService::class
        );



        return [

            /*
            |--------------------------------------------------------------------------
            | AI Political Overview
            |--------------------------------------------------------------------------
            */

            'insights' =>
                $politicalInsightService->getInsights(),



            /*
            |--------------------------------------------------------------------------
            | Swing Voters
            |--------------------------------------------------------------------------
            */

            'swingVoters' =>
                $politicalInsightService->getSwingVoters(),



            /*
            |--------------------------------------------------------------------------
            | Weak Booth Detection
            |--------------------------------------------------------------------------
            */

            'weakBooths' =>
                $politicalInsightService->getWeakBooths(),



            /*
            |--------------------------------------------------------------------------
            | Priority Villages
            |--------------------------------------------------------------------------
            */

            'priorityVillages' =>
                $politicalInsightService->getPriorityVillages(),



            /*
            |--------------------------------------------------------------------------
            | AI Campaign Strategy
            |--------------------------------------------------------------------------
            */

            'strategy' =>
                $aiStrategyService->generate(),



            /*
            |--------------------------------------------------------------------------
            | AI Booth War Room
            |--------------------------------------------------------------------------
            */

            'boothRecommendations' =>
                $aiStrategyService->getBoothRecommendations(),


        ];

    }

}
