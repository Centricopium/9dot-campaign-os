<?php

namespace App\Filament\Resources\SurveyResponses\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SurveyResponseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Survey Information')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                Select::make('survey_id')
                                    ->relationship('survey', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload(),

                                Select::make('voter_id')
                                    ->relationship('voter', 'name')
                                    ->searchable()
                                    ->preload(),

                                Select::make('house_id')
                                    ->relationship('house', 'house_no')
                                    ->searchable()
                                    ->preload(),

                            ]),

                    ]),

                Section::make('Location')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                TextInput::make('latitude')
                                    ->numeric(),

                                TextInput::make('longitude')
                                    ->numeric(),

                            ]),

                    ]),

                Section::make('Submission')
                    ->schema([

                        DateTimePicker::make('submitted_at')
                            ->default(now()),

                    ]),

            ]);
    }
}