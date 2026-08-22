<?php

namespace App\Filament\Resources\CampaignExpenses\Pages;

use App\Filament\Resources\CampaignExpenses\CampaignExpenseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCampaignExpenses extends ListRecords
{
    protected static string $resource = CampaignExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Submit Expense')];
    }
}
