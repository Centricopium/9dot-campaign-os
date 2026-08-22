<?php

namespace App\Filament\Resources\CampaignEvents\Pages;

use App\Filament\Resources\CampaignEvents\CampaignEventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCampaignEvent extends CreateRecord
{
    protected static string $resource = CampaignEventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
