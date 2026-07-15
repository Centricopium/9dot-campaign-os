<?php

namespace App\Filament\Resources\SurveyAnswers\Pages;

use App\Filament\Resources\SurveyAnswers\SurveyAnswerResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSurveyAnswer extends EditRecord
{
    protected static string $resource = SurveyAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
