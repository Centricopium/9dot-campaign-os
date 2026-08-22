<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\HasDashboardInternalMessages;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use HasDashboardInternalMessages;

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = '9Dot Campaign Command Center';

    protected string $view = 'filament.pages.campaign-dashboard';

    protected static ?int $navigationSort = -2;
}
