<?php

namespace App\Services\Import;

class ExcelRowMapper
{
    /**
     * Convert one EC Excel row into Campaign OS format.
     */
    public function map(array $row): array
    {
        return [

            'booth_no' => $this->value($row, 'part_no'),

            'serial_no' => $this->value($row, 'slnoinpart'),

            'house_no' => $this->value($row, 'house_no'),

            'name' => $this->value($row, 'f_name'),

            'surname' => $this->value($row, 'surname'),

            'father_husband_name' => $this->value($row, 'm_name'),

            'relation_type' => $this->value($row, 'rln_type'),

            'epic_no' => strtoupper($this->value($row, 'idcard_no')),

            'gender' => strtoupper($this->value($row, 'sex')),

            'age' => $this->value($row, 'age'),

            'mobile' => $this->value($row, 'contactno'),

            'caste' => $this->value($row, 'ecast'),

        ];
    }

    protected function value(array $row, string $key): ?string
    {
        if (! array_key_exists($key, $row)) {
            return null;
        }

        $value = trim((string) $row[$key]);

        return $value === '' ? null : $value;
    }
}