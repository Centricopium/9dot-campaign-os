<?php

namespace App\Filament\Resources\CampaignEvents\Pages;

use App\Filament\Resources\CampaignEvents\CampaignEventResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCampaignEvent extends EditRecord
{
    protected static string $resource = CampaignEventResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
