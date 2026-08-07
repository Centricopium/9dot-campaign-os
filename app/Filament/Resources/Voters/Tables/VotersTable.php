<?php

namespace App\Filament\Resources\Voters\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class VotersTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('name')

            ->columns([

                TextColumn::make('epic_no')
                    ->label('EPIC')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('name')
                    ->label('Voter')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('house.display_name')
                    ->label('House')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('booth.booth_no')
                    ->label('Booth')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('mobile')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('gender')
                    ->badge(),

                TextColumn::make('age')
                    ->sortable(),

                TextColumn::make('politicalParty.short_name')
                    ->label('Party')
                    ->badge()
                    ->placeholder('-')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('support_level')
                    ->label('Support')
                    ->badge()
                    ->icon(fn (?string $state): string => match ($state) {

                        'Strong Support' => 'heroicon-m-hand-thumb-up',
                        'Support' => 'heroicon-m-hand-thumb-up',
                        'Leaning' => 'heroicon-m-arrow-trending-up',
                        'Neutral' => 'heroicon-m-minus-circle',
                        'Opposition Leaning' => 'heroicon-m-arrow-trending-down',
                        'Strong Opposition' => 'heroicon-m-hand-thumb-down',
                        'Undecided' => 'heroicon-m-question-mark-circle',

                        default => 'heroicon-m-minus-circle',
                    })
                    ->color(fn (?string $state): string => match ($state) {

                        'Strong Support' => 'success',
                        'Support' => 'success',
                        'Leaning' => 'info',
                        'Neutral' => 'gray',
                        'Opposition Leaning' => 'warning',
                        'Strong Opposition' => 'danger',
                        'Undecided' => 'warning',

                        default => 'gray',
                    }),

                IconColumn::make('is_volunteer')
                    ->label('Volunteer')
                    ->boolean(),

                IconColumn::make('is_influencer')
                    ->label('Influencer')
                    ->boolean(),

                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {

                        'Critical' => 'danger',
                        'High' => 'warning',
                        'Medium' => 'info',
                        'Low' => 'gray',

                        default => 'gray',
                    }),

                TextColumn::make('last_contact_date')
                    ->label('Last Contact')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

            ])

            ->filters([

                SelectFilter::make('gender')
                    ->options([
                        'Male' => 'Male',
                        'Female' => 'Female',
                        'Other' => 'Other',
                    ]),

                SelectFilter::make('political_party_id')
                    ->label('Political Party')
                    ->relationship('politicalParty', 'name'),

                SelectFilter::make('support_level')
                    ->label('Support Level')
                    ->options([
                        'Strong Support' => 'Strong Support',
                        'Support' => 'Support',
                        'Leaning' => 'Leaning',
                        'Neutral' => 'Neutral',
                        'Opposition Leaning' => 'Opposition Leaning',
                        'Strong Opposition' => 'Strong Opposition',
                        'Undecided' => 'Undecided',
                    ]),

                TernaryFilter::make('is_volunteer')
                    ->label('Volunteer'),

                TernaryFilter::make('is_influencer')
                    ->label('Influencer'),

                TernaryFilter::make('is_active')
                    ->label('Active'),

            ])

            ->recordActions([

                ViewAction::make(),

                EditAction::make(),

                DeleteAction::make(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                    ForceDeleteBulkAction::make(),

                    RestoreBulkAction::make(),

                ]),

            ]);
    }
}