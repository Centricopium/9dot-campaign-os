<?php

namespace App\Filament\Resources\EmployeeLeaves\Pages;

use App\Filament\Resources\EmployeeLeaves\EmployeeLeaveResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeLeaves extends ListRecords
{
    protected static string $resource = EmployeeLeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('New Leave Request')];
    }
}
