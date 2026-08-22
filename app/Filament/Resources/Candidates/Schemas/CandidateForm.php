<?php

namespace App\Filament\Resources\Candidates\Schemas;

use App\Models\Candidate;
use App\Models\Constituency;
use App\Models\PoliticalParty;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CandidateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Selection Pool')
                ->description('Candidates with the same constituency, party and election are compared together.')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('constituency_id')
                            ->label('Assembly Constituency')
                            ->options(fn (): array => Constituency::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->native(true)
                            ->required(),
                        Select::make('political_party_id')
                            ->label('Party')
                            ->options(fn (): array => PoliticalParty::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->native(true)
                            ->required(),
                        TextInput::make('election_name')
                            ->default('Assembly Election')
                            ->required(),
                        TextInput::make('election_year')
                            ->numeric()
                            ->default((int) now()->year)
                            ->minValue(2000)
                            ->maxValue(2200)
                            ->required(),
                    ]),
                ]),
            Section::make('Candidate Profile')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')->required()->maxLength(255),
                        FileUpload::make('photo')->image()->directory('candidates'),
                        TextInput::make('mobile')->tel()->maxLength(20),
                        TextInput::make('email')->email()->maxLength(255),
                        TextInput::make('current_position')->maxLength(255),
                        TextInput::make('political_experience_years')
                            ->label('Political Experience (Years)')
                            ->numeric()->default(0)->minValue(0)->maxValue(100),
                    ]),
                    Textarea::make('profile_summary')->rows(3)->columnSpanFull(),
                    Textarea::make('public_work')->rows(3)->columnSpanFull(),
                    Textarea::make('strengths')->rows(3)->columnSpanFull(),
                    Textarea::make('concerns')->label('Weaknesses / Concerns')->rows(3)->columnSpanFull(),
                ]),
            Section::make('Selection Status')
                ->schema([
                    Select::make('status')
                        ->options(fn (?Candidate $record): array => array_filter([
                            'Under Assessment' => 'Under Assessment',
                            'Shortlisted' => 'Shortlisted',
                            'Recommended' => 'Recommended',
                            'Not Selected' => 'Not Selected',
                            'Final Selected' => $record?->is_final ? 'Final Selected' : null,
                            'Withdrawn' => 'Withdrawn',
                        ]))
                        ->native(true)
                        ->default('Under Assessment')
                        ->disabled(fn (?Candidate $record): bool => (bool) $record?->is_final)
                        ->dehydrated()
                        ->required(),
                    Textarea::make('final_decision_notes')
                        ->label('Committee Decision Notes')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
