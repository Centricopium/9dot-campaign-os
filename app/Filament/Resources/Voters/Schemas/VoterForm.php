<?php

namespace App\Filament\Resources\Voters\Schemas;

use App\Models\Booth;
use App\Models\Constituency;
use App\Models\Village;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class VoterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('constituency_id')
                    ->label('Constituency')
                    ->options(Constituency::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('village_id', null);
                        $set('booth_id', null);
                    }),

                Select::make('village_id')
                    ->label('Village')
                    ->options(function (Get $get) {
                        $constituencyId = $get('constituency_id');

                        if (blank($constituencyId)) {
                            return [];
                        }

                        return Village::where('constituency_id', $constituencyId)
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('booth_id', null);
                    }),

                Select::make('booth_id')
                    ->label('Booth')
                    ->options(function (Get $get) {

                        $villageId = $get('village_id');

                        if (blank($villageId)) {
                            return [];
                        }

                        return Booth::where('village_id', $villageId)
                            ->pluck('booth_name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

            ]);
    }
}