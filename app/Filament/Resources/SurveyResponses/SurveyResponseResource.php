<?php

namespace App\Filament\Resources\SurveyResponses;

use App\Filament\Resources\SurveyResponses\Pages\CreateSurveyResponse;
use App\Filament\Resources\SurveyResponses\Pages\EditSurveyResponse;
use App\Filament\Resources\SurveyResponses\Pages\ListSurveyResponses;
use App\Filament\Resources\SurveyResponses\Pages\ViewSurveyResponse;
use App\Filament\Resources\SurveyResponses\Schemas\SurveyResponseForm;
use App\Filament\Resources\SurveyResponses\Schemas\SurveyResponseInfolist;
use App\Filament\Resources\SurveyResponses\Tables\SurveyResponsesTable;
use App\Models\SurveyResponse;
use BackedEnum;
use UnitEnum;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SurveyResponseResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Masters';
    protected static ?int $navigationSort = 3;
    protected static ?string $model = SurveyResponse::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return SurveyResponseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SurveyResponseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SurveyResponsesTable::configure($table);
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
            'index' => ListSurveyResponses::route('/'),
            'create' => CreateSurveyResponse::route('/create'),
            'view' => ViewSurveyResponse::route('/{record}'),
            'edit' => EditSurveyResponse::route('/{record}/edit'),
        ];
    }
}
