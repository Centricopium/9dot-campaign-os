<?php

namespace App\Filament\Resources\Villages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VillagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('constituency.name')
                    ->label('Constituency')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Village')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('taluka')
                    ->searchable(),

                TextColumn::make('district')
                    ->searchable(),

                TextColumn::make('total_voters')
                    ->label('Total Voters')
                    ->numeric(),

                TextColumn::make('total_booths')
                    ->label('Total Booths')
                    ->numeric(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

            ])

            ->filters([])

            ->recordActions([
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}