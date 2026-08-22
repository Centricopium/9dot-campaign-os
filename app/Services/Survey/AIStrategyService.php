<?php

namespace App\Services\Survey;

use App\Models\Voter;
use Illuminate\Support\Collection;

class AIStrategyService
{

    /**
     * Generate AI Campaign Strategy
     */
    public function generate(): array
    {

        $totalVoters = Voter::count();


        $supporters = Voter::whereIn(
            'support_level',
            [
                'Strong Support',
                'Moderate Support',
                'Leaning Support',
            ]
        )->count();



        $neutral = Voter::where(
            'support_level',
            'Neutral'
        )->count();



        $opposition = Voter::whereIn(
            'support_level',
            [
                'Strong Opposition',
                'Moderate Opposition',
                'Leaning Opposition',
            ]
        )->count();



        $neutralPercentage =
            $totalVoters > 0
            ? round(($neutral / $totalVoters) * 100, 2)
            : 0;



        $supportPercentage =
            $totalVoters > 0
            ? round(($supporters / $totalVoters) * 100, 2)
            : 0;



        return [

            'total_voters' =>
                $totalVoters,


            'support_percentage' =>
                $supportPercentage,


            'neutral_percentage' =>
                $neutralPercentage,


            'opposition_percentage' =>
                $totalVoters > 0
                ? round(($opposition / $totalVoters) * 100, 2)
                : 0,



            'campaign_status' =>
                $this->getCampaignStatus(
                    $neutralPercentage
                ),



            'priority' =>
                $this->getPriority(
                    $neutralPercentage
                ),



            'actions' =>
                $this->getActions(
                    $neutralPercentage
                ),

        ];

    }





    /**
     * AI Booth War Room Analysis
     */
    public function getBoothRecommendations(): Collection
    {

        return Voter::with(
            'house.booth'
        )

        ->get()

        ->groupBy(function ($voter){

            return $voter->house?->booth?->id;

        })


        ->filter(function ($voters, $key){

            return !is_null($key);

        })


        ->map(function ($voters){


            $booth =
                $voters->first()
                ?->house
                ?->booth;



            $total =
                $voters->count();



            $support =
                $voters->whereIn(
                    'support_level',
                    [
                        'Strong Support',
                        'Moderate Support',
                        'Leaning Support',
                    ]
                )->count();



            $neutral =
                $voters->where(
                    'support_level',
                    'Neutral'
                )->count();



            $supportPercentage =
                $total > 0
                ? round(
                    ($support / $total) * 100,
                    2
                )
                : 0;



            $neutralPercentage =
                $total > 0
                ? round(
                    ($neutral / $total) * 100,
                    2
                )
                : 0;




            /**
             * AI Priority Score
             *
             * Neutral voters get highest weight
             */
            $score =
                round(
                    ($neutralPercentage * 0.7)
                    +
                    ((100 - $supportPercentage) * 0.3)
                );




            return [

                'booth_id' =>
                    $booth?->id,


                'booth' =>
                    $booth?->booth_name
                    ?? 'Unknown Booth',



                'total_voters' =>
                    $total,



                'support_percentage' =>
                    $supportPercentage,



                'neutral_percentage' =>
                    $neutralPercentage,



                'ai_score' =>
                    $score,



                'priority' =>
                    match(true){

                        $score >= 70 =>
                            'HIGH',


                        $score >= 40 =>
                            'MEDIUM',


                        default =>
                            'LOW',

                    },



                'action' =>
                    $this->getBoothAction(
                        $score
                    ),

            ];


        })


        ->sortByDesc('ai_score')

        ->values();

    }






    private function getCampaignStatus(
        float $neutral
    ): string {


        if($neutral >= 70){

            return 'Immediate Ground Campaign Required';

        }


        if($neutral >= 40){

            return 'Needs Voter Conversion Campaign';

        }


        return 'Campaign Position Stable';

    }








    private function getPriority(
        float $neutral
    ): string {


        return match(true){


            $neutral >= 70 =>
                'HIGH',


            $neutral >= 40 =>
                'MEDIUM',


            default =>
                'LOW',

        };

    }








    private function getActions(
        float $neutral
    ): array {


        if($neutral >= 70){

            return [

                'Door-to-door voter contact',

                'Identify local influencers',

                'Conduct issue based survey',

                'Activate booth volunteers',

                'Create village level action plan',

            ];

        }



        return [

            'Continue voter engagement',

            'Strengthen booth network',

            'Monitor voter sentiment',

        ];

    }








    private function getBoothAction(
        int $score
    ): string {


        return match(true){


            $score >= 70 =>
                'Immediate door-to-door campaign',


            $score >= 40 =>
                'Voter conversion activity required',


            default =>
                'Maintain booth engagement',

        };

    }


}