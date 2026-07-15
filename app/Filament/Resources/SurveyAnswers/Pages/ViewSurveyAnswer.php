<?php

namespace App\Filament\Resources\SurveyAnswers\Pages;

use App\Filament\Resources\SurveyAnswers\SurveyAnswerResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSurveyAnswer extends ViewRecord
{
    protected static string $resource = SurveyAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
