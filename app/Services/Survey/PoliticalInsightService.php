<?php

namespace App\Services\Survey;

use App\Models\Voter;
use App\Models\Booth;
use App\Models\Village;
use Illuminate\Support\Collection;

class PoliticalInsightService
{


    /*
    |--------------------------------------------------------------------------
    | Overall Political Insights
    |--------------------------------------------------------------------------
    */

    public function getInsights(): array
    {

        $total = Voter::count();


        $supporters = Voter::whereIn(
            'support_level',
            [
                'Strong Support',
                'Moderate Support',
                'Leaning Support',
            ]
        )->count();


        $opposition = Voter::whereIn(
            'support_level',
            [
                'Strong Opposition',
                'Moderate Opposition',
                'Leaning Opposition',
            ]
        )->count();



        $neutral = Voter::where(
            'support_level',
            'Neutral'
        )->count();



        return [

            'total_voters' => $total,


            'support_percentage' =>
                $total > 0
                ? round(($supporters / $total) * 100,2)
                : 0,


            'opposition_percentage' =>
                $total > 0
                ? round(($opposition / $total) * 100,2)
                : 0,


            'neutral_percentage' =>
                $total > 0
                ? round(($neutral / $total) * 100,2)
                : 0,


        ];

    }





    /*
    |--------------------------------------------------------------------------
    | Swing Voter Intelligence
    |--------------------------------------------------------------------------
    */


    public function getSwingVoters(): Collection
    {

        return Voter::query()

            ->whereIn(
                'support_level',
                [
                    'Neutral',
                    'Undecided',
                    'Leaning Support',
                    'Leaning Opposition',
                ]
            )

            ->with([
                'house.booth.village'
            ])

            ->latest()

            ->limit(50)

            ->get();

    }





    /*
    |--------------------------------------------------------------------------
    | Weak Booth Detection
    |--------------------------------------------------------------------------
    */


    public function getWeakBooths(): Collection
    {

        return Booth::query()

            ->withCount('voters')

            ->with([
                'voters'
            ])

            ->get()

            ->map(function($booth){


                $total = $booth->voters->count();


                $support =
                    $booth->voters
                    ->whereIn(
                        'support_level',
                        [
                            'Strong Support',
                            'Moderate Support',
                            'Leaning Support',
                        ]
                    )
                    ->count();



                return [

                    'booth' =>
                        $booth->booth_name,


                    'total' =>
                        $total,


                    'support' =>
                        $support,


                    'strength' =>
                        $total > 0
                        ? round(($support/$total)*100,2)
                        : 0,

                ];


            })

            ->sortBy('strength')

            ->values();

    }





    /*
    |--------------------------------------------------------------------------
    | Village Priority
    |--------------------------------------------------------------------------
    */


    public function getPriorityVillages(): Collection
    {

        return Village::query()

            ->with([
                'booths.voters'
            ])

            ->get()

            ->map(function($village){


                $voters =
                    $village->booths
                    ->pluck('voters')
                    ->flatten();



                $undecided =
                    $voters
                    ->whereIn(
                        'support_level',
                        [
                            'Neutral',
                            'Undecided'
                        ]
                    )
                    ->count();



                return [

                    'village' =>
                        $village->name,


                    'total_voters' =>
                        $voters->count(),


                    'priority_score' =>
                        $undecided,

                ];


            })

            ->sortByDesc('priority_score')

            ->values();

    }


}