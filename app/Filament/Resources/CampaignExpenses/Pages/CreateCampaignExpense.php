<?php

namespace App\Filament\Resources\CampaignExpenses\Pages;

use App\Filament\Resources\CampaignExpenses\CampaignExpenseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCampaignExpense extends CreateRecord
{
    protected static string $resource = CampaignExpenseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['submitted_by'] = auth()->id();

        return $data;
    }
}
