<?php

namespace App\Filament\Resources\Houses\Schemas;

use App\Models\Booth;
use App\Models\Constituency;
use App\Models\Village;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class HouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('constituency_id')
                    ->label('Constituency')
                    ->options(fn () => Constituency::pluck('name', 'id')->toArray())
                    ->live()
                    ->dehydrated(false)
                    ->afterStateUpdated(function (Set $set) {
                        $set('village_id', null);
                        $set('booth_id', null);
                    })
                    ->searchable()
                    ->required(),

                Select::make('village_id')
                    ->label('Village')
                    ->options(function (Get $get) {
                        if (! $get('constituency_id')) {
                            return [];
                        }

                        return Village::where('constituency_id', $get('constituency_id'))
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->live()
                    ->dehydrated(false)
                    ->afterStateUpdated(function (Set $set) {
                        $set('booth_id', null);
                    })
                    ->searchable()
                    ->required(),

                Select::make('booth_id')
                    ->label('Booth')
                    ->options(function (Get $get) {

                        if (! $get('village_id')) {
                            return [];
                        }

                        return Booth::where('village_id', $get('village_id'))
                            ->pluck('booth_name', 'id')
                            ->toArray();
                    })
                    ->live()
                    ->searchable()
                    ->required(),

                TextInput::make('house_no')
                    ->label('House No')
                    ->required(),

                TextInput::make('head_of_family')
                    ->label('Head of Family'),

                TextInput::make('mobile')
                    ->tel(),

                Textarea::make('address')
                    ->columnSpanFull(),

                TextInput::make('gps_latitude')
                    ->numeric(),

                TextInput::make('gps_longitude')
                    ->numeric(),

                Toggle::make('is_verified')
                    ->default(false),

                Toggle::make('is_active')
                    ->default(true),

            ]);
    }
}