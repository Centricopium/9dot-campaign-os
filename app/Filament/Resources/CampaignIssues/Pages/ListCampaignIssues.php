<?php

namespace App\Filament\Resources\CampaignIssues\Pages;

use App\Filament\Resources\CampaignIssues\CampaignIssueResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCampaignIssues extends ListRecords
{
    protected static string $resource = CampaignIssueResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Register Public Issue')];
    }
}
