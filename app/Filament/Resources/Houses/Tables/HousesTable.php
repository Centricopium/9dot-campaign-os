<?php

namespace App\Filament\Resources\Houses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HousesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('booth.booth_name')
                    ->label('Booth')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('house_no')
                    ->label('House No')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('head_of_family')
                    ->label('Head of Family')
                    ->searchable(),

                TextColumn::make('mobile')
                    ->searchable(),

                TextColumn::make('voters_count')
                    ->label('Family Members')
                    ->counts('voters')
                    ->badge()
                    ->color('primary'),

                IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->date()
                    ->sortable(),

            ])

            ->filters([

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