<?php

namespace App\Filament\Resources\SurveyQuestions\Schemas;

use App\Models\Survey;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SurveyQuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Question Details')
                    ->schema([

                        Select::make('survey_id')
                            ->label('Survey')
                            ->relationship('survey', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Textarea::make('question')
                            ->label('Question')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->schema([

                                Select::make('type')
                                    ->required()
                                    ->options([
                                        'text' => 'Text',
                                        'textarea' => 'Long Text',
                                        'number' => 'Number',
                                        'dropdown' => 'Dropdown',
                                        'radio' => 'Radio Button',
                                        'checkbox' => 'Checkbox',
                                        'yes_no' => 'Yes / No',
                                        'rating' => 'Rating',
                                        'date' => 'Date',
                                    ])
                                    ->live(),

                                Toggle::make('required')
                                    ->default(false),

                                TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(1),

                            ]),

                    ]),

                Section::make('Options')
                    ->schema([

                        Repeater::make('options')
                            ->schema([

                                TextInput::make('value')
                                    ->required(),

                            ])
                            ->visible(fn ($get) => in_array($get('type'), [
                                'dropdown',
                                'radio',
                                'checkbox',
                            ]))
                            ->defaultItems(2)
                            ->columnSpanFull(),

                    ]),

            ]);
    }
}