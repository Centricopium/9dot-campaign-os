<?php

namespace App\Filament\Resources\SurveyQuestions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SurveyQuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')

            ->columns([
                TextColumn::make('survey.name')
                    ->label('Survey')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('question')
                    ->searchable()
                    ->limit(60)
                    ->wrap(),

                TextColumn::make('type')
                    ->badge()
                    ->sortable(),

                IconColumn::make('required')
                    ->label('Required')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->headerActions([
                CreateAction::make(),
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