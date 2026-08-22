<?php

namespace App\Services\House;

use App\Models\House;


class HouseIntelligenceService
{

    public function analyse(
        House $house
    ): array {


        $voters = $house->voters;


        $total = $voters->count();


        $support =
            $voters
                ->whereIn(
                    'support_level',
                    [
                        'Strong Support',
                        'Moderate Support',
                        'Leaning Support'
                    ]
                )
                ->count();



        $opposition =
            $voters
                ->whereIn(
                    'support_level',
                    [
                        'Strong Opposition',
                        'Moderate Opposition',
                        'Leaning Opposition'
                    ]
                )
                ->count();



        $neutral =
            $voters
                ->where(
                    'support_level',
                    'Neutral'
                )
                ->count();



        $score = 50;


        $score += ($support * 5);

        $score -= ($opposition * 5);



        $score = max(
            0,
            min(
                100,
                $score
            )
        );



        if($score >= 70){

            $type = 'Strong Support Family';

        }
        elseif($score >= 45){

            $type = 'Swing Family';

        }
        else{

            $type = 'Opposition Family';

        }



        return [

            'family_score' => $score,


            'family_type' => $type,


            'conversion_probability' =>
                100 - $score,


            'priority' =>
                $score < 60
                ? 'HIGH'
                : 'MEDIUM',



            'recommendation' =>

                match($type){

                    'Swing Family' =>
                        'Personal meeting + issue based discussion',

                    'Strong Support Family' =>
                        'Maintain relationship and activate family',

                    default =>
                        'Understand issues and improve connection',

                },

        ];

    }

}