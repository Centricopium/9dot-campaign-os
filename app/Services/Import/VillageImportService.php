<?php

namespace App\Services\Import;

use App\Models\Village;

class VillageImportService
{
    public function import(array $row, int $constituencyId): Village
    {
        $data = VillageRowMapper::map($row);

        return Village::updateOrCreate(
            [
                'constituency_id' => $constituencyId,
                'name' => $data['name'],
            ],
            [
                'taluka' => $data['taluka'],
                'district' => $data['district'],
            ]
        );
    }
}