<?php

namespace App\Filament\Resources\Booths\Schemas;

use App\Models\Village;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BoothForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('village_id')
                    ->label('Village')
                    ->options(function () {
                        return Village::query()->pluck('name', 'id');
                    })
                ->searchable()
                ->required(),

                TextInput::make('booth_no')
                    ->required(),

                TextInput::make('booth_name')
                    ->required(),

                TextInput::make('total_voters')
                    ->numeric(),

                TextInput::make('male_voters')
                    ->numeric(),

                TextInput::make('female_voters')
                    ->numeric(),

                TextInput::make('other_voters')
                    ->numeric(),

                Select::make('category')
                    ->options([
                        'Urban' => 'Urban',
                        'Rural' => 'Rural',
                    ])
                    ->default('Rural'),

                TextInput::make('gps_latitude'),

                TextInput::make('gps_longitude'),

                TextInput::make('google_map'),

                TextInput::make('president'),

                TextInput::make('worker'),

                Toggle::make('is_active')
                    ->default(true),

            ]);
    }
}