<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class House360 extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|UnitEnum|null $navigationGroup = 'Campaign';

    protected static ?string $navigationLabel = 'House 360';

    protected static ?string $title = 'House 360';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.house360';
}