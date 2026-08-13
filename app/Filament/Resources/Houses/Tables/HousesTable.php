<?php

namespace App\Filament\Resources\Houses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HousesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('booth.village.name')
                    ->label('Village')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

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
                    ->label('Mobile')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('voters_count')
                    ->label('Family Members')
                    ->counts('voters')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([

            ])

            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}