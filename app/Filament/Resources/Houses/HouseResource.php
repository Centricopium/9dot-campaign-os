<?php

namespace App\Filament\Resources\Houses;

use App\Filament\Resources\Concerns\AuthorizesResourcePermissions;
use App\Filament\Resources\Houses\Pages\CreateHouse;
use App\Filament\Resources\Houses\Pages\EditHouse;
use App\Filament\Resources\Houses\Pages\ListHouses;
use App\Filament\Resources\Houses\Pages\ViewHouse;
use App\Filament\Resources\Houses\Schemas\HouseForm;
use App\Filament\Resources\Houses\Schemas\HouseInfolist;
use App\Filament\Resources\Houses\Tables\HousesTable;
use App\Models\House;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use App\Filament\Resources\Houses\RelationManagers\VotersRelationManager;

class HouseResource extends Resource
{
    use AuthorizesResourcePermissions;
    protected static string $permissionPrefix = 'house';
    protected static ?string $model = House::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|UnitEnum|null $navigationGroup = 'Masters';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'display_name';

    public static function getNavigationLabel(): string
    {
        return 'Houses';
    }

    public static function getModelLabel(): string
    {
        return 'House';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Houses';
    }

    public static function form(Schema $schema): Schema
    {
        return HouseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return HouseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HousesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
             VotersRelationManager::class,
            // SurveysRelationManager::class,
            // IssuesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHouses::route('/'),
            'create' => CreateHouse::route('/create'),
            'view' => ViewHouse::route('/{record}'),
            'edit' => EditHouse::route('/{record}/edit'),
        ];
    }
    
}
