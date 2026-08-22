<?php

namespace App\Filament\Resources\CampaignIssues\Pages;

use App\Filament\Resources\CampaignIssues\CampaignIssueResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCampaignIssue extends CreateRecord
{
    protected static string $resource = CampaignIssueResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['reported_by'] = auth()->id();

        return $data;
    }
}
