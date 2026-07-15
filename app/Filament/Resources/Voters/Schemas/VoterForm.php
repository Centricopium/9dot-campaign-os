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
                            ->relationship('house', 'house_no')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Grid::make(3)
                            ->schema([

                                TextInput::make('part_no')
                                    ->required(),

                                TextInput::make('serial_no')
                                    ->required(),

                                TextInput::make('epic_no')
                                    ->unique(ignoreRecord: true),

                            ]),

                    ]),

                Section::make('Personal Information')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                TextInput::make('name')
                                    ->required(),

                                TextInput::make('father_husband_name'),

                                Select::make('gender')
                                    ->options([
                                        'Male' => 'Male',
                                        'Female' => 'Female',
                                        'Other' => 'Other',
                                    ]),

                                TextInput::make('age')
                                    ->numeric(),

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
                                    ->maxLength(15),

                                TextInput::make('whatsapp')
                                    ->tel()
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

                                TextInput::make('occupation'),

                                TextInput::make('education'),

                                TextInput::make('blood_group'),

                            ]),

                    ]),

                Section::make('Political Information')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                Select::make('support_level')
                                    ->options([
                                        'Strong Congress' => 'Strong Congress',
                                        'Congress Leaning' => 'Congress Leaning',
                                        'Neutral' => 'Neutral',
                                        'Undecided' => 'Undecided',
                                        'BJP Leaning' => 'BJP Leaning',
                                        'Strong BJP' => 'Strong BJP',
                                        'Other' => 'Other',
                                    ])
                                    ->default('Neutral'),

                                TextInput::make('party_preference'),

                                TextInput::make('party_support'),

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
                                    ->numeric(),

                                TextInput::make('longitude')
                                    ->numeric(),

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