<?php

namespace App\Filament\Resources\CampaignCommunications\Pages;

use App\Filament\Resources\CampaignCommunications\CampaignCommunicationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCampaignCommunications extends ListRecords
{
    protected static string $resource = CampaignCommunicationResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Create Content')];
    }
}
