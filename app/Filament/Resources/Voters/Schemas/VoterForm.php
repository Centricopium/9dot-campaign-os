<?php

namespace App\Filament\Resources\Voters\Schemas;

use App\Models\House;
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
                                fn (House $record): string => "{$record->house_no} - {$record->head_of_family}"
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->hiddenOn(
                                \App\Filament\Resources\Houses\RelationManagers\VotersRelationManager::class
                            ),

                        Grid::make(3)
                            ->schema([

                                TextInput::make('part_no')
                                    ->label('Part No.')
                                    ->required()
                                    ->maxLength(20),

                                TextInput::make('serial_no')
                                    ->label('Serial No.')
                                    ->required()
                                    ->maxLength(20),

                                TextInput::make('epic_no')
                                    ->label('EPIC No.')
                                    ->maxLength(20)
                                    ->dehydrateStateUsing(
                                        fn (?string $state): ?string => $state
                                            ? strtoupper($state)
                                            : null
                                    )
                                    ->unique(ignoreRecord: true),

                            ]),

                    ]),

                Section::make('Personal Information')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->maxLength(150),

                                TextInput::make('father_husband_name')
                                    ->label('Father / Husband Name')
                                    ->maxLength(150),

                                Select::make('gender')
                                    ->label('Gender')
                                    ->options([
                                        'Male' => 'Male',
                                        'Female' => 'Female',
                                        'Other' => 'Other',
                                    ])
                                    ->placeholder('Select Gender'),

                                TextInput::make('age')
                                    ->label('Age')
                                    ->numeric()
                                    ->minValue(18)
                                    ->maxValue(120),

                                DatePicker::make('dob')
                                    ->label('Date of Birth'),

                                FileUpload::make('photo')
                                    ->label('Photo')
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
                                    ->label('Mobile')
                                    ->tel()
                                    ->minLength(10)
                                    ->maxLength(15),

                                TextInput::make('whatsapp')
                                    ->label('WhatsApp')
                                    ->tel()
                                    ->minLength(10)
                                    ->maxLength(15),

                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255),

                            ]),

                    ]),

                Section::make('Address Information')
                    ->schema([

                        TextInput::make('house_no')
                            ->label('House No.')
                            ->maxLength(100),

                        Textarea::make('address')
                            ->label('Address')
                            ->rows(3)
                            ->columnSpanFull(),

                    ]),

                Section::make('Social Information')
                    ->schema([

                        Grid::make(3)
                            ->schema([

                                TextInput::make('religion')
                                    ->label('Religion'),

                                TextInput::make('caste')
                                    ->label('Caste'),

                                TextInput::make('category')
                                    ->label('Category'),

                                TextInput::make('occupation')
                                    ->label('Occupation')
                                    ->maxLength(150),

                                TextInput::make('education')
                                    ->label('Education')
                                    ->maxLength(150),

                                TextInput::make('blood_group')
                                    ->label('Blood Group')
                                    ->maxLength(10),

                            ]),

                    ]),

                Section::make('Political Information')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                Select::make('political_party_id')
                                    ->label('Political Party')
                                    ->relationship(
                                        name: 'politicalParty',
                                        titleAttribute: 'name',
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Select Political Party'),

                                Select::make('support_level')
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
                                    ])
                                    ->default('Neutral')
                                    ->required(),

                                TextInput::make('party_preference')
                                    ->label('Political Remark')
                                    ->columnSpanFull(),

                                Select::make('priority')
                                    ->label('Priority')
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
                                    ->label('Volunteer')
                                    ->default(false),

                                Toggle::make('is_influencer')
                                    ->label('Influencer')
                                    ->default(false),

                                Toggle::make('government_scheme')
                                    ->label('Government Scheme')
                                    ->default(false),

                                Toggle::make('disability')
                                    ->label('Disability')
                                    ->default(false),

                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),

                            ]),

                        TextInput::make('government_scheme_name')
                            ->label('Government Scheme Name'),

                        DatePicker::make('last_contact_date')
                            ->label('Last Contact Date'),

                        DatePicker::make('next_followup')
                            ->label('Next Follow-up'),

                    ]),

                Section::make('Organisation Information')
                    ->schema([

                        Grid::make(3)
                            ->schema([

                                TextInput::make('booth_committee_role')
                                    ->label('Booth Committee Role'),

                                TextInput::make('panna_pramukh')
                                    ->label('Panna Pramukh'),

                                TextInput::make('polling_agent')
                                    ->label('Polling Agent'),

                            ]),

                    ]),

                Section::make('Social Media')
                    ->schema([

                        Grid::make(3)
                            ->schema([

                                TextInput::make('facebook')
                                    ->label('Facebook'),

                                TextInput::make('instagram')
                                    ->label('Instagram'),

                                TextInput::make('twitter')
                                    ->label('Twitter'),

                            ]),

                    ]),

                Section::make('Location')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->step(0.000001),

                                TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->step(0.000001),

                            ]),

                    ]),

                Section::make('Notes')
                    ->schema([

                        Textarea::make('internal_notes')
                            ->label('Internal Notes')
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('remarks')
                            ->label('Remarks')
                            ->rows(4)
                            ->columnSpanFull(),

                    ]),

            ]);
    }
}