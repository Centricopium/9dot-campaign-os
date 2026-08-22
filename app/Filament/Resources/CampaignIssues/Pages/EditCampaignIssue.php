<?php

namespace App\Filament\Resources\CampaignIssues\Pages;

use App\Filament\Resources\CampaignIssues\CampaignIssueResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCampaignIssue extends EditRecord
{
    protected static string $resource = CampaignIssueResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make(), DeleteAction::make()];
    }
}
