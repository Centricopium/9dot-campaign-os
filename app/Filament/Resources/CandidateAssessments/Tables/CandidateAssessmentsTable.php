<?php

namespace App\Filament\Resources\CandidateAssessments\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CandidateAssessmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('candidate.name')->label('Candidate')->weight('bold')->searchable()->sortable(),
                TextColumn::make('candidate.constituency.name')->label('Constituency'),
                TextColumn::make('candidate.politicalParty.short_name')->label('Party')->badge(),
                TextColumn::make('assessor.name')->label('Assessor')->searchable(),
                TextColumn::make('overall_score')->label('Strength')->suffix('/100')->badge()->sortable(),
                TextColumn::make('risk_score')->label('Risk')->suffix('/100')->badge()->sortable(),
                TextColumn::make('confidence_score')->label('Confidence')->suffix('%')->sortable(),
                IconColumn::make('is_submitted')->label('Submitted')->boolean(),
                TextColumn::make('submitted_at')->label('Submitted At')->dateTime('d M Y, h:i A')->placeholder('Draft'),
            ])
            ->filters([
                SelectFilter::make('candidate')->relationship('candidate', 'name'),
                SelectFilter::make('is_submitted')->options([1 => 'Submitted', 0 => 'Draft']),
            ])
            ->recordActions([EditAction::make()]);
    }
}
