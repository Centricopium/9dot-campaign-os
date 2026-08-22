<?php

namespace App\Filament\Resources\CampaignExpenses\Pages;

use App\Filament\Resources\CampaignExpenses\CampaignExpenseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCampaignExpense extends EditRecord
{
    protected static string $resource = CampaignExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
