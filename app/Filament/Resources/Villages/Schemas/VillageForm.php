<?php

namespace App\Filament\Resources\Villages\Schemas;

use App\Models\Constituency;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VillageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('constituency_id')
                    ->relationship('constituency', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('name')
                    ->required(),

                TextInput::make('village_code'),

                TextInput::make('taluka')
                    ->required(),

                TextInput::make('district')
                    ->required(),

                TextInput::make('population')
                    ->numeric()
                    ->default(0),

                TextInput::make('total_voters')
                    ->numeric()
                    ->default(0),

                TextInput::make('total_booths')
                    ->numeric()
                    ->default(0),

                Select::make('category')
                    ->options([
                        'General' => 'General',
                        'SC' => 'SC',
                        'ST' => 'ST',
                    ])
                    ->default('General'),

                TextInput::make('latitude'),

                TextInput::make('longitude'),

                Toggle::make('is_active')
                    ->default(true),

            ]);
    }
}