<?php

namespace App\Services\Import;

use App\Models\Booth;
use App\Models\House;
use RuntimeException;

class HouseResolver
{
    public function resolve(
        string|int|null $partNo,
        string|int|null $houseNo,
        ImportResult $result
    ): House {

        if (blank($partNo)) {
            throw new RuntimeException('PART_NO is missing.');
        }

        if (blank($houseNo)) {
            throw new RuntimeException('HOUSE_NO is missing.');
        }
        $houseNo = trim((string) $houseNo);
        $booth = Booth::query()
            ->where('booth_no', (string) $partNo)
            ->first();

        if (! $booth) {
            throw new RuntimeException("Booth not found for PART_NO: {$partNo}");
        }

        $house = House::query()
            ->where('booth_id', $booth->id)
            ->where('house_no', $houseNo)
            ->first();

        if ($house) {
            return $house;
        }

        $house = House::create([
            'booth_id'      => $booth->id,
            'house_no'      =>  $houseNo,
            'head_of_family'=> null,
            'mobile'        => null,
            'address'       => null,
            'is_verified'   => false,
            'is_active'     => true,
        ]);

        $result->newHouses++;

        return $house;
    }
}