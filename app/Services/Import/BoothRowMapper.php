<?php

namespace App\Services\Import;

class BoothRowMapper
{
    public static function map(array $row): array
    {
        return [

            'booth_no' => (int) ($row[0] ?? 0),

            'booth_name' => trim((string) ($row[1] ?? '')),

            'village' => trim((string) ($row[2] ?? '')),

            'area' => trim((string) ($row[3] ?? '')),
        ];
    }
}