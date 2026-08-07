<?php

namespace App\Filament\Resources\SurveyQuestions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SurveyQuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('survey_id')
    ->relationship('survey', 'name')
    ->searchable()
    ->preload()
    ->required()
    ->hiddenOn(
        \App\Filament\Resources\Surveys\RelationManagers\QuestionsRelationManager::class
    ),

                Textarea::make('question')
                    ->label('Question')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),

                Select::make('type')
                    ->options([
                        'text' => 'Text',
                        'textarea' => 'Textarea',
                        'number' => 'Number',
                        'radio' => 'Radio',
                        'checkbox' => 'Checkbox',
                        'select' => 'Dropdown',
                        'rating' => 'Rating',
                        'yes_no' => 'Yes / No',
                        'date' => 'Date',
                    ])
                    ->searchable()
                    ->live()
                    ->required(),

                Textarea::make('options')
                    ->label('Options')
                    ->helperText('Enter one option per line.')
                    ->rows(5)
                    ->visible(fn ($get) => in_array($get('type'), [
                        'radio',
                        'checkbox',
                        'select',
                    ]))
                    ->columnSpanFull(),

                Toggle::make('required')
                    ->default(true),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(1)
                    ->required(),
            ]);
    }
}