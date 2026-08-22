<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Filament\Pages\Concerns\HasDashboardInternalMessages;
use Filament\Pages\Page;

class CampaignDashboard extends Page
{
    use AuthorizesPagePermission;
    use HasDashboardInternalMessages;

    protected static string $requiredPermission = 'dashboard.view';

    protected static ?string $navigationLabel = 'Campaign Dashboard';

    protected static ?string $title = 'Campaign Dashboard';

    protected static ?string $slug = '';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.campaign-dashboard';
}
