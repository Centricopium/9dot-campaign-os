<?php

namespace App\Filament\Resources\EmployeeLeaves\Pages;

use App\Filament\Resources\EmployeeLeaves\EmployeeLeaveResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeLeave extends EditRecord
{
    protected static string $resource = EmployeeLeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
