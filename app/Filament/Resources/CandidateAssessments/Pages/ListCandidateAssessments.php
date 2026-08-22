<?php

namespace App\Filament\Resources\CandidateAssessments\Pages;

use App\Filament\Resources\CandidateAssessments\CandidateAssessmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCandidateAssessments extends ListRecords
{
    protected static string $resource = CandidateAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('New Assessment')];
    }
}
