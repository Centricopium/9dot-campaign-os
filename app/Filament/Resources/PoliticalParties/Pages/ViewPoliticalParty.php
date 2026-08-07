<?php

namespace App\Filament\Resources\PoliticalParties\Pages;

use App\Filament\Resources\PoliticalParties\PoliticalPartyResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPoliticalParty extends ViewRecord
{
    protected static string $resource = PoliticalPartyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
