<?php

namespace App\Filament\Resources\Houses\Schemas;

use App\Models\Booth;
use App\Models\Constituency;
use App\Models\Village;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class HouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Select::make('constituency_id')
                ->label('Constituency')
                ->options(fn () => Constituency::orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->live()
                ->dehydrated(false)
                ->afterStateUpdated(function (Set $set) {
                    $set('village_id', null);
                    $set('booth_id', null);
                })
                ->required(),

            Select::make('village_id')
                ->label('Village')
                ->options(function (Get $get) {
                    if (! $get('constituency_id')) {
                        return [];
                    }

                    return Village::where('constituency_id', $get('constituency_id'))
                        ->orderBy('name')
                        ->pluck('name', 'id');
                })
                ->searchable()
                ->live()
                ->dehydrated(false)
                ->afterStateUpdated(function (Set $set) {
                    $set('booth_id', null);
                })
                ->required(),

            Select::make('booth_id')
                ->label('Booth')
                ->options(function (Get $get) {
                    if (! $get('village_id')) {
                        return [];
                    }

                    return Booth::where('village_id', $get('village_id'))
                        ->orderBy('booth_name')
                        ->pluck('booth_name', 'id');
                })
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('house_no')
                ->label('House No')
                ->required()
                ->maxLength(100),

            TextInput::make('head_of_family')
                ->label('Head of Family')
                ->maxLength(150),

            TextInput::make('mobile')
                ->label('Mobile')
                ->tel()
                ->maxLength(20),

            Textarea::make('address')
                ->label('Address')
                ->rows(3)
                ->columnSpanFull(),

            TextInput::make('gps_latitude')
                ->label('Latitude')
                ->numeric()
                ->step('any'),

            TextInput::make('gps_longitude')
                ->label('Longitude')
                ->numeric()
                ->step('any'),

            Toggle::make('is_verified')
                ->label('Verified')
                ->default(false),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),

        ]);
    }
}