<?php

namespace App\Filament\Resources\CampaignTasks\Pages;

use App\Filament\Resources\CampaignTasks\CampaignTaskResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCampaignTask extends EditRecord
{
    protected static string $resource = CampaignTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make(), DeleteAction::make()];
    }
}
