<?php

namespace App\Filament\Resources\CampaignBudgets\Pages;

use App\Filament\Resources\CampaignBudgets\CampaignBudgetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCampaignBudget extends EditRecord
{
    protected static string $resource = CampaignBudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
