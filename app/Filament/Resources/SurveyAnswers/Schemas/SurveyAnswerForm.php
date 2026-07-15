<?php

namespace App\Filament\Resources\SurveyAnswers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SurveyAnswerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Survey Answer')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                Select::make('response_id')
                                    ->relationship('response', 'id')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('question_id')
                                    ->relationship('question', 'question')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                            ]),

                        Textarea::make('answer')
                            ->rows(5)
                            ->columnSpanFull()
                            ->required(),

                    ]),

            ]);
    }
}