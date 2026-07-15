<?php

namespace App\Filament\Resources\Houses\Pages;

use App\Filament\Resources\Houses\HouseResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewHouse extends ViewRecord
{
    protected static string $resource = HouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
