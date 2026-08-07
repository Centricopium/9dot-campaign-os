<?php

namespace App\Filament\Resources\PoliticalParties\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

use Filament\Schemas\Schema;

class PoliticalPartyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Political Party Details')

                    ->schema([

                        Grid::make(2)
                            ->schema([

                                TextInput::make('name')
                                    ->label('Party Name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('short_name')
                                    ->label('Short Name')
                                    ->required()
                                    ->maxLength(20),

                                TextInput::make('symbol')
                                    ->label('Election Symbol')
                                    ->placeholder('✋ / 🪷 / 🧹'),

                                TextInput::make('color')
                                    ->label('Color Code')
                                    ->default('#2563EB'),

                                TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(1),

                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),

                            ]),

                    ]),

            ]);
    }
}