<?php

namespace App\Filament\Resources\PoliticalParties\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PoliticalPartiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')

            ->columns([

                TextColumn::make('name')
                    ->label('Party Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('short_name')
                    ->label('Short Name')
                    ->badge()
                    ->sortable(),

                TextColumn::make('symbol')
                    ->label('Symbol')
                    ->alignCenter(),

                ColorColumn::make('color')
                    ->label('Color'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
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