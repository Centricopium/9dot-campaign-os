<?php

namespace App\Filament\Resources\SurveyResponses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SurveyResponsesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('submitted_at', 'desc')

            ->columns([

                TextColumn::make('survey.name')
                    ->label('Survey')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('voter.name')
                    ->label('Voter')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('house.house_no')
                    ->label('House')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Survey By')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('latitude')
                    ->label('Latitude')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('longitude')
                    ->label('Longitude')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('submitted_at')
                    ->label('Submitted')
                    ->boolean(fn ($record) => filled($record->submitted_at)),

                TextColumn::make('submitted_at')
                    ->label('Submitted At')
                    ->dateTime('d M Y h:i A')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y h:i A')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y h:i A')
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