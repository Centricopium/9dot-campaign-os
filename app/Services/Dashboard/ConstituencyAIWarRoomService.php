<?php

namespace App\Services\Dashboard;

use App\Models\Constituency;
use App\Models\Booth;
use App\Models\Village;
use App\Models\Voter;

class ConstituencyAIWarRoomService
{

    /**
     * Complete AI War Room Analysis
     */
    public function analyse(Constituency $constituency): array
    {


        /*
        |--------------------------------------------------------------------------
        | Booth Risk Analysis
        |--------------------------------------------------------------------------
        */


        $booths = Booth::query()

            ->whereHas('village', function ($q) use ($constituency) {

                $q->where(
                    'constituency_id',
                    $constituency->id
                );

            })

            ->with('village')

            ->get();




        $boothRisk = $booths->map(function ($booth) {



            $votersQuery = Voter::whereHas(
                'house',
                function ($q) use ($booth) {

                    $q->where(
                        'booth_id',
                        $booth->id
                    );

                }
            );



            $total = (clone $votersQuery)
                ->count();



            $neutral = (clone $votersQuery)

                ->where(
                    'support_level',
                    'Neutral'
                )

                ->count();



            $support = (clone $votersQuery)

                ->where(
                    'support_level',
                    'Strong Support'
                )

                ->count();




            $risk = 0;



            if($total > 0){


                $neutralPercent =
                    ($neutral / $total) * 100;



                $supportPercent =
                    ($support / $total) * 100;



                $risk =
                    ($neutralPercent * 0.7)
                    +
                    ((100 - $supportPercent) * 0.3);


            }





            $priority =

                $risk >= 70

                ? 'HIGH'

                :

                (

                    $risk >= 40

                    ? 'MEDIUM'

                    :

                    'LOW'

                );






            /*
            |--------------------------------------------------------------------------
            | AI Reason
            |--------------------------------------------------------------------------
            */


            $reason = match($priority){


                'HIGH' =>

                    'High neutral voters. Immediate door-to-door campaign required.',



                'MEDIUM' =>

                    'Mixed voter sentiment. Need booth level engagement.',



                default =>

                    'Strong position. Maintain relationship.'

            };







            /*
            |--------------------------------------------------------------------------
            | AI Action
            |--------------------------------------------------------------------------
            */


            $action = match($priority){


                'HIGH' =>

                    'Personal meetings + influencer activation + voter conversion',



                'MEDIUM' =>

                    'Regular follow-up and issue based communication',



                default =>

                    'Maintain support network'

            };







            return [


                'booth_id' => $booth->id,



                'booth_name' =>

                    $booth->booth_name
                    ??
                    ('Booth '.$booth->id),




                'village' =>

                    $booth->village?->name
                    ??
                    '-',




                'voters' =>

                    $total,




                'neutral' =>

                    $neutral,




                'support' =>

                    $support,




                'risk_score' =>

                    round($risk),





                'priority' =>

                    $priority,





                'reason' =>

                    $reason,





                'action' =>

                    $action,






                /*
                |--------------------------------------------------------------------------
                | Booth Intelligence URL
                |--------------------------------------------------------------------------
                */


                'url' =>

                    url(
                        '/admin/booth-intelligence?booth='
                        .
                        $booth->id
                    )


            ];



        })

        ->sortByDesc('risk_score')

        ->values();








        /*
        |--------------------------------------------------------------------------
        | Swing Villages
        |--------------------------------------------------------------------------
        */


        $villages = Village::query()

            ->where(
                'constituency_id',
                $constituency->id
            )


            ->get()



            ->map(function($village){



                $votersQuery = Voter::whereHas(

                    'house.booth',

                    function($q) use ($village){

                        $q->where(
                            'village_id',
                            $village->id
                        );

                    }

                );




                $total = (clone $votersQuery)
                    ->count();





                $neutral = (clone $votersQuery)

                    ->where(
                        'support_level',
                        'Neutral'
                    )

                    ->count();






                return [


                    'village' =>

                        $village->name,




                    'voters' =>

                        $total,





                    'neutral_percentage' =>


                        $total

                        ?

                        round(
                            ($neutral/$total)*100
                        )

                        :

                        0,






                    'priority' =>


                        $total
                        &&
                        (($neutral/$total) > 0.35)


                        ?

                        'HIGH'


                        :

                        'NORMAL'


                ];



            })

            ->sortByDesc('neutral_percentage')

            ->values();










        /*
        |--------------------------------------------------------------------------
        | Final AI Output
        |--------------------------------------------------------------------------
        */


        return [



            'booth_risk' =>

                $boothRisk,





            'high_risk_booths' =>

                $boothRisk

                ->where(
                    'priority',
                    'HIGH'
                )

                ->count(),





            'medium_risk_booths' =>

                $boothRisk

                ->where(
                    'priority',
                    'MEDIUM'
                )

                ->count(),





            'safe_booths' =>

                $boothRisk

                ->where(
                    'priority',
                    'LOW'
                )

                ->count(),






            'swing_villages' =>

                $villages,






            'recommendation' =>


                'Focus on high neutral booths with door-to-door campaign and local influencer meetings.',






            'war_mode' =>


                $boothRisk

                    ->where(
                        'priority',
                        'HIGH'
                    )

                    ->count() > 0


                    ?


                    'WAR MODE'


                    :


                    'STABLE'

        ];

    }

}