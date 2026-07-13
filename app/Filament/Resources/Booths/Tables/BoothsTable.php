<?php

namespace App\Filament\Resources\Booths\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BoothsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('village.name')
                    ->label('Village')
                    ->searchable()
                    ->sortable(),        
                TextColumn::make('booth_no')
                    ->label('Booth No')
                    ->searchable(),
                TextColumn::make('booth_name')
                    ->label('Booth Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_voters')
                    ->label('Total Voters')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),    
                TextColumn::make('male_voters')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('female_voters')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('other_voters')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('category')
                    ->searchable(),
                TextColumn::make('gps_latitude')
                    ->searchable(),
                TextColumn::make('gps_longitude')
                    ->searchable(),
                TextColumn::make('google_map')
                    ->searchable(),
                TextColumn::make('president')
                    ->searchable(),
                TextColumn::make('worker')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
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
