<?php

namespace App\Services;

use App\Models\SurveyAnswer;
use App\Models\SurveyResponse;

class SurveyService
{
    public function save(
        int $surveyId,
        int $voterId,
        int $houseId,
        int $userId,
        array $answers
    ): SurveyResponse {

        $response = SurveyResponse::create([
            'survey_id'    => $surveyId,
            'user_id'      => $userId,
            'voter_id'     => $voterId,
            'house_id'     => $houseId,
            'submitted_at' => now(),
        ]);

        foreach ($answers as $questionId => $answer) {

            SurveyAnswer::create([
                'response_id' => $response->id,
                'question_id' => $questionId,
                'answer'      => is_array($answer)
                    ? json_encode($answer)
                    : $answer,
            ]);

        }

        return $response;
    }
}