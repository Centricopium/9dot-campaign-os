<?php

namespace App\Filament\Resources\Surveys\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SurveyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Survey Details')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50),

                            ]),

                        Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull(),

                    ]),

                Section::make('Survey Configuration')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                Select::make('type')
                                    ->options([
                                        'Door to Door' => 'Door to Door',
                                        'Membership' => 'Membership',
                                        'Issue' => 'Issue',
                                        'Election' => 'Election',
                                        'Government Scheme' => 'Government Scheme',
                                        'Custom' => 'Custom',
                                    ])
                                    ->required(),

                                Select::make('status')
                                    ->options([
                                        'Draft' => 'Draft',
                                        'Active' => 'Active',
                                        'Closed' => 'Closed',
                                    ])
                                    ->default('Draft')
                                    ->required(),

                                DatePicker::make('start_date'),

                                DatePicker::make('end_date'),

                            ]),

                    ]),

                

            ]);
    }
}