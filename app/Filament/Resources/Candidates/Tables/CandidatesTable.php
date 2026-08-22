<?php

namespace App\Filament\Resources\Candidates\Tables;

use App\Models\Candidate;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CandidatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('average_score', 'desc')
            ->columns([
                ImageColumn::make('photo')->circular()->label(''),
                TextColumn::make('name')->weight('bold')->searchable()->sortable(),
                TextColumn::make('constituency.name')->label('Constituency')->searchable()->sortable(),
                TextColumn::make('politicalParty.short_name')->label('Party')->badge(),
                TextColumn::make('election_year')->label('Election')->sortable(),
                TextColumn::make('average_score')
                    ->label('Strength Score')
                    ->formatStateUsing(fn ($state): string => $state === null ? 'Pending' : number_format((float) $state, 1).'/100')
                    ->badge()
                    ->sortable(),
                TextColumn::make('average_risk_score')
                    ->label('Risk')
                    ->formatStateUsing(fn ($state): string => $state === null ? 'Pending' : number_format((float) $state, 1).'/100')
                    ->badge(),
                TextColumn::make('average_confidence_score')
                    ->label('Confidence')
                    ->formatStateUsing(fn ($state): string => $state === null ? 'Pending' : number_format((float) $state, 1).'%'),
                TextColumn::make('submitted_assessments_count')->label('Assessments')->alignCenter(),
                TextColumn::make('system_recommendation')->label('System Recommendation')->badge(),
                TextColumn::make('status')->badge()->sortable(),
                IconColumn::make('is_final')->label('Final')->boolean(),
            ])
            ->filters([
                SelectFilter::make('constituency')->relationship('constituency', 'name'),
                SelectFilter::make('politicalParty')->relationship('politicalParty', 'name')->label('Party'),
                SelectFilter::make('status')->options([
                    'Under Assessment' => 'Under Assessment',
                    'Shortlisted' => 'Shortlisted',
                    'Recommended' => 'Recommended',
                    'Final Selected' => 'Final Selected',
                    'Not Selected' => 'Not Selected',
                ]),
            ])
            ->recordActions([
                Action::make('selectFinal')
                    ->label('Select Final')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Confirm Final Candidate')
                    ->modalDescription('This candidate will be marked final and other candidates in the same selection pool will be marked Not Selected.')
                    ->visible(fn (Candidate $record): bool => (auth()->user()?->can('candidate.approve') ?? false) && ! $record->is_final)
                    ->action(function (Candidate $record): void {
                        $record->selectAsFinal(auth()->user(), $record->final_decision_notes);
                        Notification::make()->title($record->name.' selected as final candidate')->success()->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
