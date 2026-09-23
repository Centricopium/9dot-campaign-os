<?php

namespace App\Filament\Resources\EmployeeLeaves\Pages;

use App\Filament\Resources\EmployeeLeaves\EmployeeLeaveResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployeeLeave extends CreateRecord
{
    protected static string $resource = EmployeeLeaveResource::class;
}
