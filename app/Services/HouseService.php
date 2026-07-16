<?php

namespace App\Services;

use App\Models\House;

class HouseService
{
    public function getHouse(int $houseId): ?House
    {
       return House::with([
            'booth.village.constituency',
            'voters',
        ])->find($houseId);
    }

    public function getPoliticalSummary(House $house): array
{
    return [

        'congress' => $house->voters()
            ->whereIn('support_level', [
                'Strong Congress',
                'Congress Leaning',
            ])->count(),

        'bjp' => $house->voters()
            ->whereIn('support_level', [
                'Strong BJP',
                'BJP Leaning',
            ])->count(),

        'neutral' => $house->voters()
            ->where('support_level', 'Neutral')
            ->count(),

        'undecided' => $house->voters()
            ->where('support_level', 'Undecided')
            ->count(),

    ];
}

    public function getFamilyCount(House $house): int
    {
        return $house->voters()->count();
    }
    
    
}