<?php

namespace App\Filament\Resources\CampaignTasks\Pages;

use App\Filament\Resources\CampaignTasks\CampaignTaskResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCampaignTask extends ViewRecord
{
    protected static string $resource = CampaignTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
