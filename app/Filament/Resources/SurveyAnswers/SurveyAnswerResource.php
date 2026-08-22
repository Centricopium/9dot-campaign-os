<?php

namespace App\Filament\Resources\SurveyAnswers;

use App\Filament\Resources\Concerns\AuthorizesResourcePermissions;
use App\Filament\Resources\SurveyAnswers\Pages\CreateSurveyAnswer;
use App\Filament\Resources\SurveyAnswers\Pages\EditSurveyAnswer;
use App\Filament\Resources\SurveyAnswers\Pages\ListSurveyAnswers;
use App\Filament\Resources\SurveyAnswers\Pages\ViewSurveyAnswer;
use App\Filament\Resources\SurveyAnswers\Schemas\SurveyAnswerForm;
use App\Filament\Resources\SurveyAnswers\Schemas\SurveyAnswerInfolist;
use App\Filament\Resources\SurveyAnswers\Tables\SurveyAnswersTable;
use App\Models\SurveyAnswer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SurveyAnswerResource extends Resource
{
    use AuthorizesResourcePermissions;
    protected static string $permissionPrefix = 'survey';
    protected static string|\UnitEnum|null $navigationGroup = 'Survey Management';
    protected static ?int $navigationSort = 2;

    protected static ?string $model = SurveyAnswer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'answer';

    public static function form(Schema $schema): Schema
    {
        return SurveyAnswerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SurveyAnswerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SurveyAnswersTable::configure($table);
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
            'index' => ListSurveyAnswers::route('/'),
            'create' => CreateSurveyAnswer::route('/create'),
            'view' => ViewSurveyAnswer::route('/{record}'),
            'edit' => EditSurveyAnswer::route('/{record}/edit'),
        ];
    }
}
