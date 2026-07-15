<?php

namespace App\Filament\Resources\SurveyAnswers\Pages;

use App\Filament\Resources\SurveyAnswers\SurveyAnswerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSurveyAnswers extends ListRecords
{
    protected static string $resource = SurveyAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
