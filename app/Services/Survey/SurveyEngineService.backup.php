<?php

namespace App\Services\Survey;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\SurveyAnswer;
use Illuminate\Support\Facades\DB;

class SurveyEngineService
{
    public function getSurvey(int $surveyId): ?Survey
    {
        return Survey::with('questions')
            ->find($surveyId);
    }

    public function getQuestions(int $surveyId)
    {
        return SurveyQuestion::where('survey_id', $surveyId)
            ->orderBy('sort_order')
            ->get();
    }

    public function saveResponse(array $data): SurveyResponse
    {
        return DB::transaction(function () use ($data) {

            $response = SurveyResponse::create([
                'survey_id'    => $data['survey_id'],
                'user_id'      => auth()->id(),
                'voter_id'     => $data['voter_id'],
                'house_id'     => $data['house_id'] ?? null,
                'latitude'     => $data['latitude'] ?? null,
                'longitude'    => $data['longitude'] ?? null,
                'submitted_at' => now(),
            ]);

            foreach ($data['answers'] as $questionId => $answer) {

                SurveyAnswer::create([
                    'response_id' => $response->id,
                    'question_id' => $questionId,
                    'answer'      => is_array($answer)
                        ? json_encode($answer)
                        : $answer,
                ]);
            }

            return $response;
        });
    }
}