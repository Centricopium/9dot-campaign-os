<?php

namespace App\Filament\Resources\CandidateAssessments\Pages;

use App\Filament\Resources\CandidateAssessments\CandidateAssessmentResource;
use App\Models\CandidateAssessment;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateCandidateAssessment extends CreateRecord
{
    protected static string $resource = CandidateAssessmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['assessor_id'] = auth()->id();

        if (CandidateAssessment::query()
            ->where('candidate_id', $data['candidate_id'])
            ->where('assessor_id', $data['assessor_id'])
            ->exists()) {
            throw ValidationException::withMessages([
                'data.candidate_id' => 'You already created an assessment for this candidate.',
            ]);
        }

        return $data;
    }
}
