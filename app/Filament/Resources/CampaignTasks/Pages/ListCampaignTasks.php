<?php

namespace App\Filament\Resources\CampaignTasks\Pages;

use App\Filament\Resources\CampaignTasks\CampaignTaskResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCampaignTasks extends ListRecords
{
    protected static string $resource = CampaignTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Create Field Task'),
        ];
    }
}
