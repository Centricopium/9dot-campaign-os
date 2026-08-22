<?php

namespace App\Filament\Resources\CandidateAssessments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CandidateAssessmentForm
{
    public static function configure(Schema $schema): Schema
    {
        $score = fn (string $field, string $label, string $weight): TextInput => TextInput::make($field)
            ->label($label)
            ->helperText('Weight: '.$weight)
            ->numeric()
            ->minValue(0)
            ->maxValue(100)
            ->default(0)
            ->required();

        return $schema->components([
            Section::make('Assessment Assignment')
                ->schema([
                    Select::make('candidate_id')
                        ->relationship('candidate', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('assessor_id')
                        ->relationship('assessor', 'name')
                        ->default(fn (): ?int => auth()->id())
                        ->disabled()
                        ->dehydrated()
                        ->required(),
                ]),
            Section::make('Candidate Strength Assessment')
                ->description('Score each parameter from 0 to 100. The weighted total is calculated automatically.')
                ->schema([
                    Grid::make(3)->schema([
                        $score('winnability_score', 'Winnability / Public Acceptance', '20%'),
                        $score('constituency_connect_score', 'Constituency & Ground Connect', '15%'),
                        $score('organisation_strength_score', 'Party Organisation Strength', '15%'),
                        $score('public_work_score', 'Public Work & Performance', '15%'),
                        $score('integrity_score', 'Integrity & Clean Image', '10%'),
                        $score('leadership_score', 'Leadership & Communication', '10%'),
                        $score('party_loyalty_score', 'Party Loyalty & Teamwork', '5%'),
                        $score('campaign_readiness_score', 'Campaign Readiness', '5%'),
                        $score('compliance_score', 'Legal & Financial Compliance', '5%'),
                    ]),
                ]),
            Section::make('Risk, Evidence & Submission')
                ->schema([
                    Grid::make(2)->schema([
                        $score('risk_score', 'Risk Score', '0 = Low, 100 = High'),
                        $score('confidence_score', 'Research Confidence', '0–100%'),
                    ]),
                    Textarea::make('evidence_notes')
                        ->label('Verified Evidence / Source Notes')
                        ->rows(4)
                        ->required()
                        ->columnSpanFull(),
                    Textarea::make('remarks')->label('Committee Remarks')->rows(4)->required()->columnSpanFull(),
                    Toggle::make('is_submitted')
                        ->label('Submit Assessment')
                        ->helperText('Submitted assessments are locked and included in the final score.')
                        ->default(false),
                ]),
        ]);
    }
}
