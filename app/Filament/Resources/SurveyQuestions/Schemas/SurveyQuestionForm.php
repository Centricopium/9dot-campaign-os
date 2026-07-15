<?php

namespace App\Filament\Resources\SurveyQuestions\Schemas;

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
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->schema([

                                Select::make('type')
                                    ->label('Question Type')
                                    ->required()
                                    ->live()
                                    ->options([
                                        'text' => '📝 Short Text',
                                        'textarea' => '📄 Long Text',
                                        'number' => '🔢 Number',
                                        'date' => '📅 Date',
                                        'yes_no' => '✅ Yes / No',
                                        'radio' => '🔘 Radio Button',
                                        'checkbox' => '☑ Checkbox',
                                        'dropdown' => '📋 Dropdown',
                                        'rating' => '⭐ Rating',
                                    ]),

                                Toggle::make('is_required')
                                    ->label('Required')
                                    ->default(false),

                                TextInput::make('sort_order')
                                    ->label('Display Order')
                                    ->numeric()
                                    ->default(1),

                            ]),

                    ]),

                Section::make('Answer Options')
                    ->schema([

                        Repeater::make('options')
                            ->schema([

                                TextInput::make('value')
                                    ->label('Option')
                                    ->required(),

                            ])
                            ->defaultItems(2)
                            ->visible(fn ($get) => in_array(
                                $get('type'),
                                [
                                    'radio',
                                    'checkbox',
                                    'dropdown',
                                ]
                            )),

                    ]),

            ]);
    }
}