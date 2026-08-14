<?php

namespace App\Services\Survey;

use App\Models\Voter;
use App\Models\PoliticalParty;
use Illuminate\Support\Collection;

class PoliticalAnalyticsService
{

    /*
    |--------------------------------------------------------------------------
    | Overview
    |--------------------------------------------------------------------------
    */

    public function getOverview(): array
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


        return [

            'total_voters' => $total,

            'supporters' => $supporters,

            'undecided' => Voter::where(
                'support_level',
                'Undecided'
            )->count(),


            'opposition' => Voter::whereIn(
                'support_level',
                [
                    'Leaning Opposition',
                    'Moderate Opposition',
                    'Strong Opposition',
                ]
            )->count(),


            'volunteers' => Voter::where(
                'is_volunteer',
                true
            )->count(),


            'influencers' => Voter::where(
                'is_influencer',
                true
            )->count(),

        ];
    }



    /*
    |--------------------------------------------------------------------------
    | Support Analysis
    |--------------------------------------------------------------------------
    */

    public function getSupportAnalysis(): Collection
    {
        return Voter::query()

            ->selectRaw(
                "IFNULL(support_level,'Not Assigned') as support_level, COUNT(*) as total"
            )

            ->groupBy('support_level')

            ->orderByDesc('total')

            ->get();
    }




    /*
    |--------------------------------------------------------------------------
    | Party Analysis
    |--------------------------------------------------------------------------
    */

    public function getPartyAnalysis(): Collection
    {

        return PoliticalParty::query()

            ->where('is_active', true)

            ->with('voters')

            ->get()

            ->map(function ($party) {


                $voters = $party->voters;


                return [

                    'id' => $party->id,

                    'name' => $party->name,

                    'short_name' => $party->short_name,

                    'symbol' => $party->symbol,


                    'total' => $voters->count(),


                    'strong_support' =>
                        $voters
                            ->where('support_level','Strong Support')
                            ->count(),


                    'moderate_support' =>
                        $voters
                            ->where('support_level','Moderate Support')
                            ->count(),


                    'neutral' =>
                        $voters
                            ->where('support_level','Neutral')
                            ->count(),


                    'undecided' =>
                        $voters
                            ->where('support_level','Undecided')
                            ->count(),

                ];

            })

            ->filter(fn ($party) => $party['total'] > 0)

            ->values();

    }




    /*
    |--------------------------------------------------------------------------
    | Booth Intelligence
    |--------------------------------------------------------------------------
    */

    public function getBoothAnalysis(): Collection
    {

        return Voter::query()

            ->with([
                'house.booth'
            ])

            ->get()

            ->filter(function ($voter){

                return $voter->house?->booth;

            })

            ->groupBy(function ($voter){

                return $voter->house->booth->id;

            })

            ->map(function ($voters){


                $booth = $voters->first()
                    ->house
                    ->booth;


                return [

                    'booth_id' => $booth->id,

                    'booth' =>
                        $booth->booth_name
                        ?? 'Unknown Booth',


                    'total_voters' =>
                        $voters->count(),


                    'supporters' =>
                        $voters->whereIn(
                            'support_level',
                            [
                                'Strong Support',
                                'Moderate Support',
                                'Leaning Support',
                            ]
                        )->count(),


                    'undecided' =>
                        $voters->where(
                            'support_level',
                            'Undecided'
                        )->count(),


                    'opposition' =>
                        $voters->whereIn(
                            'support_level',
                            [
                                'Strong Opposition',
                                'Moderate Opposition',
                                'Leaning Opposition',
                            ]
                        )->count(),

                ];


            })

            ->sortByDesc('total_voters')

            ->values();

    }




    /*
    |--------------------------------------------------------------------------
    | Village Intelligence
    |--------------------------------------------------------------------------
    */

    public function getVillageAnalysis(): Collection
    {

        return Voter::query()

            ->with([
                'house.booth.village'
            ])

            ->get()

            ->filter(function ($voter){

                return $voter->house?->booth?->village;

            })

            ->groupBy(function ($voter){

                return $voter
                    ->house
                    ->booth
                    ->village
                    ->id;

            })

            ->map(function ($voters){


                $village = $voters->first()
                    ->house
                    ->booth
                    ->village;


                return [

                    'village_id' => $village->id,


                    'village' =>
                        $village->name,


                    'taluka' =>
                        $village->taluka,


                    'district' =>
                        $village->district,


                    'total_voters' =>
                        $voters->count(),


                    'supporters' =>
                        $voters->whereIn(
                            'support_level',
                            [
                                'Strong Support',
                                'Moderate Support',
                                'Leaning Support',
                            ]
                        )->count(),


                    'neutral' =>
                        $voters->where(
                            'support_level',
                            'Neutral'
                        )->count(),


                    'undecided' =>
                        $voters->where(
                            'support_level',
                            'Undecided'
                        )->count(),


                    'opposition' =>
                        $voters->whereIn(
                            'support_level',
                            [
                                'Strong Opposition',
                                'Moderate Opposition',
                                'Leaning Opposition',
                            ]
                        )->count(),

                ];

            })

            ->sortByDesc('total_voters')

            ->values();

    }

}