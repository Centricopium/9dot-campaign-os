<?php

namespace App\Filament\Resources\SurveyQuestions\Tables;

use Filament\Actions\BulkActionGroup;
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
            ->columns([

                TextColumn::make('survey.name')
                    ->label('Survey')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('question')
                    ->limit(80)
                    ->searchable(),

                TextColumn::make('type')
                    ->badge()
                    ->sortable(),

                IconColumn::make('required')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->date('d M Y')
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