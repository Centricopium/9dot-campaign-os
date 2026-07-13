<?php

namespace App\Filament\Resources\Constituencies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ConstituencyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Constituency Name')
                    ->required(),

                TextInput::make('state')
                    ->default('Punjab')
                    ->required(),

                TextInput::make('district')
                    ->required(),

                TextInput::make('total_voters')
                    ->numeric()
                    ->default(0),

                TextInput::make('total_booths')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}