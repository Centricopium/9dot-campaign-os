<?php

namespace App\Filament\Pages;

use App\Services\Survey\SurveyIntelligenceService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SurveyIntelligence extends Page
{
    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedChartBarSquare;


    protected static ?string $navigationLabel = 'Survey Intelligence';


    protected static string|UnitEnum|null $navigationGroup = 'Campaign';


    protected static ?int $navigationSort = 2;


    protected static ?string $title = 'Survey Intelligence';


    protected string $view = 'filament.pages.survey-intelligence';



    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

    public ?int $surveyId = null;



    /*
    |--------------------------------------------------------------------------
    | Dashboard Data
    |--------------------------------------------------------------------------
    */

    public function getDataProperty(): array
    {
        $service = app(SurveyIntelligenceService::class);


        return [

            /*
            |--------------------------------------------------------------------------
            | Main KPIs
            |--------------------------------------------------------------------------
            */

            'overview' => $service->getOverview(
                $this->surveyId
            ),



            /*
            |--------------------------------------------------------------------------
            | Survey Performance
            |--------------------------------------------------------------------------
            */

            'surveys' => $service->getSurveyPerformance(
                $this->surveyId
            ),



            /*
            |--------------------------------------------------------------------------
            | Question Analytics
            |--------------------------------------------------------------------------
            */

            'questions' => $service->getQuestionIntelligence(
                $this->surveyId
            ),



            /*
            |--------------------------------------------------------------------------
            | Geography Analytics
            |--------------------------------------------------------------------------
            */

            'geography' => $service->getGeographicIntelligence(
                $this->surveyId
            ),



            /*
            |--------------------------------------------------------------------------
            | Latest Responses
            |--------------------------------------------------------------------------
            */

            'recentResponses' => $service->getRecentResponses(
                $this->surveyId
            ),

        ];
    }



    /*
    |--------------------------------------------------------------------------
    | Survey Filter Changed
    |--------------------------------------------------------------------------
    */

    public function updatedSurveyId(): void
    {
        $this->dispatch('$refresh');
    }
}