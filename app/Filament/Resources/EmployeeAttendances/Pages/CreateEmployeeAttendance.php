<?php

namespace App\Filament\Resources\EmployeeAttendances\Pages;

use App\Filament\Resources\EmployeeAttendances\EmployeeAttendanceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployeeAttendance extends CreateRecord
{
    protected static string $resource = EmployeeAttendanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['marked_by'] = auth()->id();

        return $data;
    }
}
