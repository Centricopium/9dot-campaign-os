<?php

namespace App\Services\Import;

use App\Models\Booth;
use App\Models\Village;
use App\Models\VillageAlias;
use Exception;

class BoothImportService
{
    public function import(array $row, int $constituencyId): Booth
    {
        $data = BoothRowMapper::map($row);

        $villageName = trim(
            preg_replace('/\s+/u', ' ', $data['village'])
        );

        // 1. Exact Village Match
        $village = Village::where('constituency_id', $constituencyId)
            ->where('name', $villageName)
            ->first();

        // 2. Alias Match
        if (! $village) {

            $alias = VillageAlias::where('alias', $villageName)->first();

            if ($alias) {
                $village = $alias->village;
            }
        }

        // 3. Still not found
        if (! $village) {
            throw new Exception("Village not found : {$villageName}");
        }

        return Booth::updateOrCreate(
            [
                'village_id' => $village->id,
                'booth_no'   => $data['booth_no'],
            ],
            [
                'part_no'    => $data['booth_no'],
                'booth_name' => $data['booth_name'],
                'is_active'  => true,
            ]
        );
    }
}