<?php

namespace App\Filament\Resources\Surveys;

use App\Filament\Resources\Concerns\AuthorizesResourcePermissions;
use App\Filament\Resources\Surveys\Pages\CreateSurvey;
use App\Filament\Resources\Surveys\Pages\EditSurvey;
use App\Filament\Resources\Surveys\Pages\ListSurveys;
use App\Filament\Resources\Surveys\Pages\ViewSurvey;
use App\Filament\Resources\Surveys\Schemas\SurveyForm;
use App\Filament\Resources\Surveys\Schemas\SurveyInfolist;
use App\Filament\Resources\Surveys\Tables\SurveysTable;
use App\Filament\Resources\Surveys\RelationManagers\QuestionsRelationManager;
use App\Models\Survey;
use BackedEnum;
use UnitEnum;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SurveyResource extends Resource

{
    use AuthorizesResourcePermissions;
    protected static string $permissionPrefix = 'survey';
    protected static string|UnitEnum|null $navigationGroup = 'Masters';
    protected static ?int $navigationSort = 6;
    protected static ?string $model = Survey::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SurveyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SurveyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SurveysTable::configure($table);
    }

    public static function getRelations(): array
{
    return [
        QuestionsRelationManager::class,
    ];
}

    public static function getPages(): array
    {
        return [
            'index' => ListSurveys::route('/'),
            'create' => CreateSurvey::route('/create'),
            'view' => ViewSurvey::route('/{record}'),
            'edit' => EditSurvey::route('/{record}/edit'),
        ];
    }
}
