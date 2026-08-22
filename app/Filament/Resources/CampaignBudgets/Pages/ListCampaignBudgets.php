<?php

namespace App\Filament\Resources\CampaignBudgets\Pages;

use App\Filament\Resources\CampaignBudgets\CampaignBudgetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCampaignBudgets extends ListRecords
{
    protected static string $resource = CampaignBudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Create Budget')];
    }
}
