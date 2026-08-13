<?php

namespace App\Filament\Resources\PoliticalParties;

use App\Filament\Resources\PoliticalParties\Pages\CreatePoliticalParty;
use App\Filament\Resources\PoliticalParties\Pages\EditPoliticalParty;
use App\Filament\Resources\PoliticalParties\Pages\ListPoliticalParties;
use App\Filament\Resources\PoliticalParties\Pages\ViewPoliticalParty;
use App\Filament\Resources\PoliticalParties\Schemas\PoliticalPartyForm;
use App\Filament\Resources\PoliticalParties\Schemas\PoliticalPartyInfolist;
use App\Filament\Resources\PoliticalParties\Tables\PoliticalPartiesTable;
use App\Models\PoliticalParty;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PoliticalPartyResource extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = 'Masters';
    protected static ?int $navigationSort = 7;

    protected static ?string $model = PoliticalParty::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PoliticalPartyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PoliticalPartyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PoliticalPartiesTable::configure($table);
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
            'index' => ListPoliticalParties::route('/'),
            'create' => CreatePoliticalParty::route('/create'),
            'view' => ViewPoliticalParty::route('/{record}'),
            'edit' => EditPoliticalParty::route('/{record}/edit'),
        ];
    }
}
