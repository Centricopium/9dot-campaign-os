<?php

namespace App\Filament\Resources\Voters;

use App\Filament\Resources\Voters\Pages\CreateVoter;
use App\Filament\Resources\Voters\Pages\EditVoter;
use App\Filament\Resources\Voters\Pages\ListVoters;
use App\Filament\Resources\Voters\Pages\ViewVoter;
use App\Filament\Resources\Voters\Schemas\VoterForm;
use App\Filament\Resources\Voters\Schemas\VoterInfolist;
use App\Filament\Resources\Voters\Tables\VotersTable;
use App\Models\Voter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class VoterResource extends Resource
{
   
    protected static ?string $model = Voter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|UnitEnum|null $navigationGroup = 'Election Management';

    protected static ?string $navigationLabel = 'Voters';

    protected static ?string $modelLabel = 'Voter';

    protected static ?string $pluralModelLabel = 'Voters';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return VoterForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VoterInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VotersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->with('house.booth.village');
}

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'epic_no',
            'mobile',
            'serial_no',
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVoters::route('/'),
            'create' => CreateVoter::route('/create'),
            'view' => ViewVoter::route('/{record}'),
            'edit' => EditVoter::route('/{record}/edit'),
        ];
    }
}