<?php

namespace App\Filament\Resources\PoliticalParties\Pages;

use App\Filament\Resources\PoliticalParties\PoliticalPartyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPoliticalParties extends ListRecords
{
    protected static string $resource = PoliticalPartyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
