<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ImportCentre extends Page
{
    protected string $view = 'filament.pages.import-centre';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static string|UnitEnum|null $navigationGroup = 'Data Management';

    protected static ?string $navigationLabel = 'Import Centre';

    protected static ?string $title = 'Import Centre';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->canAny([
            'import.village',
            'import.booth',
            'import.house',
            'import.voter',
        ]) ?? false;
    }
}