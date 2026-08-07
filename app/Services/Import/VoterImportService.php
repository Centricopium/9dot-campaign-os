<?php

namespace App\Services\Import;

use App\Models\Voter;

class VoterImportService
{
    public function __construct(
        protected ExcelRowMapper $mapper,
        protected HouseResolver $houseResolver,
    ) {
    }

    public function import(array $row, ImportResult $result): void
    {
        try {

            $data = $this->mapper->map($row);

            $house = $this->houseResolver->resolve(
                $data['booth_no'],
                $data['house_no'],
                $result
            );

            /*
            |--------------------------------------------------------------------------
            | Auto Update House Details
            |--------------------------------------------------------------------------
            */

            if (
                blank($house->head_of_family) &&
                ! blank($data['name'])
            ) {
                $house->head_of_family = trim(
                    $data['name'] . ' ' . ($data['surname'] ?? '')
                );
            }

            if (
                blank($house->mobile) &&
                ! blank($data['mobile'])
            ) {
                $house->mobile = $data['mobile'];
            }

            if ($house->isDirty()) {
                $house->save();
            }

            /*
            |--------------------------------------------------------------------------
            | Existing Voter (Update)
            |--------------------------------------------------------------------------
            */

            if (! blank($data['epic_no'])) {

                $existingVoter = Voter::query()
                    ->where('epic_no', $data['epic_no'])
                    ->first();

                if ($existingVoter) {

                    $existingVoter->update([

                        'house_id' => $house->id,

                        'serial_no' => $data['serial_no'],
                        'part_no' => $data['booth_no'],

                        'name' => $data['name'],
                        'surname' => $data['surname'],
                        'father_husband_name' => $data['father_husband_name'],
                        'relation_type' => $data['relation_type'],

                        'gender' => $data['gender'],
                        'age' => $data['age'],

                        'mobile' => $data['mobile'],
                        'caste' => $data['caste'],

                        'is_active' => true,

                    ]);

                    $result->duplicateEpic++;
                    $result->updated++;

                    return;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Create New Voter
            |--------------------------------------------------------------------------
            */

            Voter::create([

                'house_id' => $house->id,

                'serial_no' => $data['serial_no'],
                'part_no' => $data['booth_no'],
                'epic_no' => $data['epic_no'],

                'name' => $data['name'],
                'surname' => $data['surname'],
                'father_husband_name' => $data['father_husband_name'],
                'relation_type' => $data['relation_type'],

                'gender' => $data['gender'],
                'age' => $data['age'],

                'mobile' => $data['mobile'],
                'caste' => $data['caste'],

                'is_active' => true,

            ]);

            $result->imported++;

        } catch (\Throwable $e) {

            $result->skipped++;

            $result->addError($e->getMessage());
        }
    }
}