<?php

namespace App\Filament\Resources\CampaignTasks\Pages;

use App\Filament\Resources\CampaignTasks\CampaignTaskResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCampaignTask extends CreateRecord
{
    protected static string $resource = CampaignTaskResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
