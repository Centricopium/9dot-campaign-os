<?php

namespace App\Filament\Resources\Constituencies;

use App\Filament\Resources\Constituencies\Pages\CreateConstituency;
use App\Filament\Resources\Constituencies\Pages\EditConstituency;
use App\Filament\Resources\Constituencies\Pages\ListConstituencies;
use App\Filament\Resources\Constituencies\Schemas\ConstituencyForm;
use App\Filament\Resources\Constituencies\Tables\ConstituenciesTable;
use App\Models\Constituency;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ConstituencyResource extends Resource
{
    protected static ?string $model = Constituency::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ConstituencyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConstituenciesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConstituencies::route('/'),
            'create' => CreateConstituency::route('/create'),
            'edit' => EditConstituency::route('/{record}/edit'),
        ];
    }
}
