<?php

namespace App\Filament\Resources\Surveys\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SurveysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->searchable(),

                BadgeColumn::make('type')
                    ->colors([
                        'primary',
                    ]),

                BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'Draft',
                        'success' => 'Active',
                        'danger' => 'Closed',
                    ]),

                TextColumn::make('questions_count')
                    ->label('Questions')
                    ->counts('questions')
                    ->sortable(),

                TextColumn::make('responses_count')
                    ->label('Responses')
                    ->counts('responses')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable(),

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