<?php

namespace App\Filament\Resources\Voters\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VotersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

    TextColumn::make('epic_no')
        ->label('EPIC')
        ->searchable()
        ->sortable()
        ->copyable(),

    TextColumn::make('name')
        ->searchable()
        ->sortable()
        ->weight('bold'),

    TextColumn::make('house.house_no')
        ->label('House')
        ->sortable(),

    TextColumn::make('mobile')
        ->searchable()
        ->copyable(),

    TextColumn::make('gender')
        ->badge(),

    TextColumn::make('age')
        ->sortable(),

    TextColumn::make('support_level')
        ->badge()
        ->colors([
            'success' => 'Strong Congress',
            'info' => 'Congress Leaning',
            'gray' => 'Neutral',
            'warning' => 'BJP Leaning',
            'danger' => 'Strong BJP',
        ]),

    IconColumn::make('is_volunteer')
        ->label('Volunteer')
        ->boolean(),

    TextColumn::make('priority')
        ->badge(),

    IconColumn::make('is_active')
        ->label('Active')
        ->boolean(),

])
            ->filters([
                //
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
