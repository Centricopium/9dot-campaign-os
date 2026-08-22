<?php

namespace App\Filament\Resources\Booths\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BoothsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort(
                fn (Builder $query): Builder => $query
                    ->orderByRaw('CAST(booths.booth_no AS UNSIGNED)')
            )

            ->columns([

                TextColumn::make('village.name')
                    ->label('Village')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('booth_no')
                    ->label('Booth No')
                    ->searchable()
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | Booth Name
                |--------------------------------------------------------------------------
                */

                TextColumn::make('booth_name')
                    ->label('Booth Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_voters')
                    ->label('Total Voters')
                    ->numeric(),

                TextColumn::make('male_voters')
                    ->label('Male Voters')
                    ->numeric(),

                TextColumn::make('female_voters')
                    ->label('Female Voters')
                    ->numeric(),

                TextColumn::make('other_voters')
                    ->label('Other Voters')
                    ->numeric(),

                IconColumn::make('is_active')
                    ->label('Is Active')
                    ->boolean(),

                TextColumn::make('category')
                    ->searchable(),

                TextColumn::make('gps_latitude')
                    ->label('GPS Latitude')
                    ->searchable(),

                TextColumn::make('gps_longitude')
                    ->label('GPS Longitude')
                    ->searchable(),

                TextColumn::make('google_map')
                    ->searchable(),

                TextColumn::make('president')
                    ->searchable(),

                TextColumn::make('worker')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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