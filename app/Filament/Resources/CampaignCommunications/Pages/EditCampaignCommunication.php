<?php

namespace App\Filament\Resources\CampaignCommunications\Pages;

use App\Filament\Resources\CampaignCommunications\CampaignCommunicationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCampaignCommunication extends EditRecord
{
    protected static string $resource = CampaignCommunicationResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
