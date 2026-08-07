<?php

namespace App\Filament\Resources\PoliticalParties\Pages;

use App\Filament\Resources\PoliticalParties\PoliticalPartyResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPoliticalParty extends EditRecord
{
    protected static string $resource = PoliticalPartyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
