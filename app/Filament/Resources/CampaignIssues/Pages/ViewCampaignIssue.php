<?php

namespace App\Filament\Resources\CampaignIssues\Pages;

use App\Filament\Resources\CampaignIssues\CampaignIssueResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCampaignIssue extends ViewRecord
{
    protected static string $resource = CampaignIssueResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
