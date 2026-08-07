<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('name')

            ->columns([

                ImageColumn::make('profile_photo')
                    ->label('')
                    ->circular(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('constituency.name')
                    ->label('Constituency')
                    ->searchable(),

                TextColumn::make('village.name')
                    ->label('Village')
                    ->toggleable(),

                TextColumn::make('booth.booth_name')
                    ->label('Booth')
                    ->toggleable(),

                TextColumn::make('mobile')
                    ->searchable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->since()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([

                SelectFilter::make('role')
                    ->relationship('roles', 'name')
                    ->options(Role::pluck('name', 'name')),

                SelectFilter::make('constituency')
                    ->relationship('constituency', 'name'),

                SelectFilter::make('is_active')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ]),

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