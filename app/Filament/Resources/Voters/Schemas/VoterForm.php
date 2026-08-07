<?php

namespace App\Filament\Resources\Voters\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VoterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Electoral Information')
                    ->description('Election Commission Details')
                    ->schema([

                       Select::make('house_id')
    ->label('House')
    ->relationship(
        name: 'house',
        titleAttribute: 'house_no',
    )
    ->getOptionLabelFromRecordUsing(
        fn (\App\Models\House $record): string => "{$record->house_no} - {$record->head_of_family}"
    )
    ->searchable()
    ->preload()
    ->required()
    ->hiddenOn(\App\Filament\Resources\Houses\RelationManagers\VotersRelationManager::class),

                        Grid::make(3)
                            ->schema([

                                TextInput::make('part_no')
                                    ->label('Part No')
                                    ->required()
                                    ->maxLength(20),

                                TextInput::make('serial_no')
                                    ->label('Serial No')
                                    ->required()
                                    ->maxLength(20),

                                TextInput::make('epic_no')
                                    ->label('EPIC No')
                                    ->maxLength(20)
                                    ->dehydrateStateUsing(fn (?string $state): ?string => $state ? strtoupper($state) : null)
                                    ->unique(ignoreRecord: true),

                            ]),

                    ]),

                Section::make('Personal Information')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(150),

                                TextInput::make('father_husband_name')
                                    ->label('Father / Husband Name')
                                    ->maxLength(150),

                                Select::make('gender')
                                    ->options([
                                        'Male' => 'Male',
                                        'Female' => 'Female',
                                        'Other' => 'Other',
                                    ]),

                                TextInput::make('age')
                                    ->numeric()
                                    ->minValue(18)
                                    ->maxValue(120),

                                DatePicker::make('dob'),

                                FileUpload::make('photo')
                                    ->image()
                                    ->directory('voters')
                                    ->imageEditor(),

                            ]),

                    ]),

                Section::make('Contact Information')
                    ->schema([

                        Grid::make(3)
                            ->schema([

                                TextInput::make('mobile')
                                    ->tel()
                                    ->minLength(10)
                                    ->maxLength(15),

                                TextInput::make('whatsapp')
                                    ->tel()
                                    ->minLength(10)
                                    ->maxLength(15),

                                TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),

                            ]),

                    ]),

                Section::make('Address Information')
                    ->schema([

                        TextInput::make('house_no')
                            ->maxLength(100),

                        Textarea::make('address')
                            ->rows(3)
                            ->columnSpanFull(),

                    ]),

                Section::make('Social Information')
                    ->schema([

                        Grid::make(3)
                            ->schema([

                                TextInput::make('religion'),

                                TextInput::make('caste'),

                                TextInput::make('category'),

                                TextInput::make('occupation')
                                    ->maxLength(150),

                                TextInput::make('education')
                                    ->maxLength(150),

                                TextInput::make('blood_group')
                                    ->maxLength(10),

                            ]),

                    ]),

                Section::make('Political Information')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                Select::make('political_party_id')
                                    ->label('Political Party')
                                    ->relationship('politicalParty', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Select Political Party'),

                                Select::make('support_level')
                                    ->label('Support Level')
                                    ->options([
                                        'Strong Support' => '⭐⭐⭐⭐⭐ Strong Support',
                                        'Support' => '⭐⭐⭐⭐ Support',
                                        'Leaning' => '⭐⭐⭐ Leaning',
                                        'Neutral' => '⭐⭐ Neutral',
                                        'Opposition Leaning' => '⭐ Opposition Leaning',
                                        'Strong Opposition' => '⭐⭐⭐⭐⭐ Strong Opposition',
                                        'Undecided' => '❓ Undecided',
                                    ])
                                    ->default('Neutral')
                                    ->required(),

                                TextInput::make('party_preference')
                                    ->label('Political Remark')
                                    ->columnSpanFull(),

                                Select::make('priority')
                                    ->options([
                                        'Low' => 'Low',
                                        'Medium' => 'Medium',
                                        'High' => 'High',
                                        'Critical' => 'Critical',
                                    ])
                                    ->default('Medium'),

                            ]),

                    ]),

                Section::make('Campaign Information')
                    ->schema([

                        Grid::make(3)
                            ->schema([

                                Toggle::make('is_volunteer')
                                    ->default(false),

                                Toggle::make('is_influencer')
                                    ->default(false),

                                Toggle::make('government_scheme')
                                    ->default(false),

                                Toggle::make('disability')
                                    ->default(false),

                                Toggle::make('is_active')
                                    ->default(true),

                            ]),

                        TextInput::make('government_scheme_name'),

                        DatePicker::make('last_contact_date'),

                        DatePicker::make('next_followup'),

                    ]),

                Section::make('Organisation Information')
                    ->schema([

                        Grid::make(3)
                            ->schema([

                                TextInput::make('booth_committee_role'),

                                TextInput::make('panna_pramukh'),

                                TextInput::make('polling_agent'),

                            ]),

                    ]),

                Section::make('Social Media')
                    ->schema([

                        Grid::make(3)
                            ->schema([

                                TextInput::make('facebook'),

                                TextInput::make('instagram'),

                                TextInput::make('twitter'),

                            ]),

                    ]),

                Section::make('Location')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                TextInput::make('latitude')
                                    ->numeric()
                                    ->step(0.000001),

                                TextInput::make('longitude')
                                    ->numeric()
                                    ->step(0.000001),

                            ]),

                    ]),

                Section::make('Notes')
                    ->schema([

                        Textarea::make('internal_notes')
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('remarks')
                            ->rows(4)
                            ->columnSpanFull(),

                    ]),

            ]);
    }
}