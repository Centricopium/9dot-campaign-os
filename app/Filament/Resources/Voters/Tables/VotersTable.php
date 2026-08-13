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

                TextColumn::make('serial_no')
                    ->label('Serial No.')
                    ->sortable()
                    ->searchable(),

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

                TextColumn::make('house.booth.village.name')
                    ->label('Village')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('booth.booth_no')
                    ->label('Booth')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('mobile')
                    ->label('Mobile')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('gender')
                    ->label('Gender')
                    ->badge(),

                TextColumn::make('age')
                    ->label('Age')
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
                    ->icon(
                        fn (?string $state): string => match ($state) {

                            'Strong Support' =>
                                'heroicon-m-hand-thumb-up',

                            'Moderate Support' =>
                                'heroicon-m-hand-thumb-up',

                            'Leaning Support' =>
                                'heroicon-m-arrow-trending-up',

                            'Neutral' =>
                                'heroicon-m-minus-circle',

                            'Undecided' =>
                                'heroicon-m-question-mark-circle',

                            'Leaning Opposition' =>
                                'heroicon-m-arrow-trending-down',

                            'Moderate Opposition' =>
                                'heroicon-m-arrow-trending-down',

                            'Strong Opposition' =>
                                'heroicon-m-hand-thumb-down',

                            default =>
                                'heroicon-m-minus-circle',
                        }
                    )
                    ->color(
                        fn (?string $state): string => match ($state) {

                            'Strong Support' =>
                                'success',

                            'Moderate Support' =>
                                'success',

                            'Leaning Support' =>
                                'info',

                            'Neutral' =>
                                'gray',

                            'Undecided' =>
                                'warning',

                            'Leaning Opposition' =>
                                'warning',

                            'Moderate Opposition' =>
                                'warning',

                            'Strong Opposition' =>
                                'danger',

                            default =>
                                'gray',
                        }
                    ),

                IconColumn::make('is_volunteer')
                    ->label('Volunteer')
                    ->boolean(),

                IconColumn::make('is_influencer')
                    ->label('Influencer')
                    ->boolean(),

                TextColumn::make('priority')
                    ->label('Priority')
                    ->badge()
                    ->color(
                        fn (?string $state): string => match ($state) {

                            'Critical' =>
                                'danger',

                            'High' =>
                                'warning',

                            'Medium' =>
                                'info',

                            'Low' =>
                                'gray',

                            default =>
                                'gray',
                        }
                    ),

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
                    ->relationship(
                        'politicalParty',
                        'name'
                    ),

                SelectFilter::make('support_level')
                    ->label('Support Level')
                    ->options([
                        'Strong Support' => 'Strong Support',
                        'Moderate Support' => 'Moderate Support',
                        'Leaning Support' => 'Leaning Support',
                        'Neutral' => 'Neutral',
                        'Undecided' => 'Undecided',
                        'Leaning Opposition' => 'Leaning Opposition',
                        'Moderate Opposition' => 'Moderate Opposition',
                        'Strong Opposition' => 'Strong Opposition',
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