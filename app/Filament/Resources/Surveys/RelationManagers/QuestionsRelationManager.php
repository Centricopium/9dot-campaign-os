<?php

namespace App\Filament\Resources\Surveys\RelationManagers;

use App\Filament\Resources\SurveyQuestions\Schemas\SurveyQuestionForm;
use App\Filament\Resources\SurveyQuestions\SurveyQuestionResource;
use App\Filament\Resources\SurveyQuestions\Tables\SurveyQuestionsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    protected static ?string $relatedResource = SurveyQuestionResource::class;

    protected static ?string $title = 'Questions';

    protected static ?string $modelLabel = 'Question';

    protected static ?string $pluralModelLabel = 'Questions';

    public function form(Schema $schema): Schema
    {
        return SurveyQuestionForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return SurveyQuestionsTable::configure($table);
    }
}