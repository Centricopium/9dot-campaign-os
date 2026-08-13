<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CampaignDashboard extends Page
{
    protected static ?string $navigationLabel = 'Campaign Dashboard';

    protected static ?string $title = 'Campaign Dashboard';

    protected static ?string $slug = '';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.campaign-dashboard';
}