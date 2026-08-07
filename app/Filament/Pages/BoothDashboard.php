<?php

namespace App\Filament\Pages;

use App\Models\Booth;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class BoothDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Campaign';

    protected static ?string $navigationLabel = 'Booth Dashboard';

    protected static ?string $title = 'Booth Dashboard';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.booth-dashboard';

    public ?int $boothId = null;

    protected function getHeaderWidgets(): array
{
    return [

        \App\Filament\Widgets\BoothSummaryStats::class,

    ];
}

    public function getBoothsProperty()
    {
        return Booth::orderBy('booth_no')->get();
    }
}