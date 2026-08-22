<?php

namespace App\Filament\Resources\CampaignCommunications\Pages;

use App\Filament\Resources\CampaignCommunications\CampaignCommunicationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCampaignCommunication extends CreateRecord
{
    protected static string $resource = CampaignCommunicationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
