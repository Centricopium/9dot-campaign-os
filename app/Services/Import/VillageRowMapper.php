<?php

namespace App\Services\Import;

class VillageRowMapper
{
    public static function map(array $row): array
    {
        return [

            'name' => trim((string) ($row[0] ?? '')),

            'taluka' => trim((string) ($row[1] ?? '')),

            'district' => trim((string) ($row[2] ?? '')),

        ];
    }
}