<?php

namespace App\Filament\Resources\CandidateAssessments\Pages;

use App\Filament\Resources\CandidateAssessments\CandidateAssessmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCandidateAssessment extends EditRecord
{
    protected static string $resource = CandidateAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['assessor_id'] = $this->record->assessor_id;

        return $data;
    }
}
