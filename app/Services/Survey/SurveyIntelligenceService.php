<?php

namespace App\Services\Survey;

use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyResponse;
use App\Models\SurveyQuestion;
use App\Models\Voter;
use Illuminate\Support\Collection;

class SurveyIntelligenceService
{

    /*
    |--------------------------------------------------------------------------
    | Overview KPI
    |--------------------------------------------------------------------------
    */

    public function getOverview(?int $surveyId = null): array
    {

        $totalSurveys = Survey::count();


        $activeSurveys = Survey::where('status', 'Active')
            ->count();



        $responsesQuery = SurveyResponse::query();


        if ($surveyId) {

            $responsesQuery->where(
                'survey_id',
                $surveyId
            );

        }


        $totalResponses = $responsesQuery->count();



        $totalVoters = Voter::count();



        $responseCoverage = $totalVoters > 0

            ? round(
                ($totalResponses / $totalVoters) * 100,
                1
            )

            : 0;



        $todayResponses = SurveyResponse::query()
            ->when(
                $surveyId,
                fn ($q) => $q->where('survey_id', $surveyId)
            )
            ->whereDate(
                'created_at',
                today()
            )
            ->count();



        $weekResponses = SurveyResponse::query()
            ->when(
                $surveyId,
                fn ($q) => $q->where('survey_id', $surveyId)
            )
            ->where(
                'created_at',
                '>=',
                now()->startOfWeek()
            )
            ->count();



        $monthResponses = SurveyResponse::query()
            ->when(
                $surveyId,
                fn ($q) => $q->where('survey_id', $surveyId)
            )
            ->where(
                'created_at',
                '>=',
                now()->startOfMonth()
            )
            ->count();



        return [

            'total_surveys' => $totalSurveys,

            'active_surveys' => $activeSurveys,

            'total_responses' => $totalResponses,

            'total_voters' => $totalVoters,

            'response_coverage' => $responseCoverage,

            'today_responses' => $todayResponses,

            'week_responses' => $weekResponses,

            'month_responses' => $monthResponses,

        ];

    }




    /*
    |--------------------------------------------------------------------------
    | Survey Performance
    |--------------------------------------------------------------------------
    */

    public function getSurveyPerformance(?int $surveyId = null): Collection
    {

        return Survey::query()

            ->withCount([
                'questions',
                'responses',
            ])

            ->when(
                $surveyId,
                fn ($q) => $q->where('id', $surveyId)
            )

            ->orderByDesc('responses_count')

            ->get();

    }





    /*
    |--------------------------------------------------------------------------
    | Question Intelligence
    |--------------------------------------------------------------------------
    */

    public function getQuestionIntelligence(?int $surveyId = null): Collection
    {

        $questions = SurveyQuestion::query()

            ->with('survey')

            ->withCount('answers')

            ->when(
                $surveyId,
                fn ($q) =>
                    $q->where(
                        'survey_id',
                        $surveyId
                    )
            )

            ->orderBy('survey_id')

            ->orderBy('sort_order')

            ->get();



        return $questions->map(function ($question) {



            $answers = SurveyAnswer::query()

                ->where(
                    'question_id',
                    $question->id
                )

                ->get();



            $distribution = $answers

                ->groupBy(function ($answer) {


                    return trim(
                        (string) $answer->answer
                    );


                })

                ->map(function ($items) use ($answers) {


                    $count = $items->count();



                    $percentage = $answers->count() > 0

                        ? round(
                            ($count / $answers->count()) * 100,
                            1
                        )

                        : 0;



                    return [

                        'count' => $count,

                        'percentage' => $percentage,

                    ];

                })

                ->sortByDesc('count');




            return [

                'id' => $question->id,

                'survey_id' => $question->survey_id,

                'survey_name' =>
                    $question->survey?->name ?? '-',


                'question' =>
                    $question->question,


                'type' =>
                    $question->type,


                'required' =>
                    $question->required,


                'answers_count' =>
                    $answers->count(),


                'distribution' =>
                    $distribution,

            ];


        });

    }





    /*
    |--------------------------------------------------------------------------
    | Geography Intelligence
    |--------------------------------------------------------------------------
    */

    public function getGeographicIntelligence(?int $surveyId = null): array
    {

        $responses = SurveyResponse::query()

            ->when(
                $surveyId,
                fn ($q) =>
                    $q->where(
                        'survey_id',
                        $surveyId
                    )
            )

            ->with([
                'voter.house.booth.village.constituency',
                'house.booth.village.constituency',
            ])

            ->get();



        $constituencies = [];

        $villages = [];

        $booths = [];



        foreach ($responses as $response) {



            $house = $response->house;


            $voter = $response->voter;



            $booth = $voter?->house?->booth
                ?? $house?->booth;



            $village = $booth?->village;



            $constituency = $village?->constituency;




            if ($constituency) {

                $id = $constituency->id;


                $constituencies[$id]['name']
                    ??= $constituency->name;


                $constituencies[$id]['responses']
                    ??= 0;


                $constituencies[$id]['responses']++;

            }




            if ($village) {

                $id = $village->id;


                $villages[$id]['name']
                    ??= $village->name;


                $villages[$id]['responses']
                    ??= 0;


                $villages[$id]['responses']++;

            }




            if ($booth) {

                $id = $booth->id;


                $booths[$id]['name']
                    ??=
                    $booth->booth_name
                    ?: 'Booth '.$booth->booth_no;



                $booths[$id]['responses']
                    ??= 0;


                $booths[$id]['responses']++;

            }

        }




        return [

            'constituencies' =>
                collect($constituencies)
                    ->sortByDesc('responses')
                    ->values(),


            'villages' =>
                collect($villages)
                    ->sortByDesc('responses')
                    ->values(),


            'booths' =>
                collect($booths)
                    ->sortByDesc('responses')
                    ->values(),

        ];

    }





    /*
    |--------------------------------------------------------------------------
    | Recent Responses
    |--------------------------------------------------------------------------
    */

    public function getRecentResponses(
        ?int $surveyId = null,
        int $limit = 15
    ): Collection
    {


        return SurveyResponse::query()

            ->when(
                $surveyId,
                fn ($q) =>
                    $q->where(
                        'survey_id',
                        $surveyId
                    )
            )

            ->with([
                'survey',
                'voter',
                'house',
            ])

            ->latest('submitted_at')

            ->latest('created_at')

            ->limit($limit)

            ->get();

    }

}