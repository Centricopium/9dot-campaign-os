<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SurveyRunner extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Survey Runner';

    protected static string|UnitEnum|null $navigationGroup = 'Campaign';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Survey Runner';

    protected string $view = 'filament.pages.survey-runner';
}