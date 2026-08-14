<?php

namespace App\Filament\Resources\Voters\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VoterInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | VOTER 360° — PROFILE
                |--------------------------------------------------------------------------
                */

                Section::make('👤 Voter 360° Profile')
                    ->description('Complete voter profile and campaign intelligence')
                    ->schema([

                        Grid::make(4)
                            ->schema([

                                TextEntry::make('name')
                                    ->label('Voter Name')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->placeholder('-'),

                                TextEntry::make('epic_no')
                                    ->label('EPIC No.')
                                    ->badge()
                                    ->copyable()
                                    ->placeholder('-'),

                                TextEntry::make('gender')
                                    ->label('Gender')
                                    ->badge()
                                    ->placeholder('-'),

                                TextEntry::make('age')
                                    ->label('Age')
                                    ->numeric()
                                    ->placeholder('-'),

                            ]),

                        Grid::make(4)
                            ->schema([

                                TextEntry::make('father_husband_name')
                                    ->label('Father / Husband')
                                    ->placeholder('-'),

                                TextEntry::make('serial_no')
                                    ->label('Serial No.')
                                    ->placeholder('-'),

                                TextEntry::make('part_no')
                                    ->label('Part No.')
                                    ->placeholder('-'),

                                TextEntry::make('priority')
                                    ->label('Priority')
                                    ->badge()
                                    ->placeholder('-'),

                            ]),

                    ]),


                /*
                |--------------------------------------------------------------------------
                | ELECTORAL LOCATION
                |--------------------------------------------------------------------------
                */

                Section::make('📍 Electoral Location')
                    ->description('Constituency → Village → Booth → House')
                    ->schema([

                        Grid::make(4)
                            ->schema([

                                TextEntry::make(
                                    'house.booth.village.constituency.name'
                                )
                                    ->label('Constituency')
                                    ->weight('bold')
                                    ->placeholder('-'),

                                TextEntry::make(
                                    'house.booth.village.name'
                                )
                                    ->label('Village')
                                    ->weight('bold')
                                    ->placeholder('-'),

                                TextEntry::make(
                                    'house.booth.booth_no'
                                )
                                    ->label('Booth')
                                    ->badge()
                                    ->placeholder('-'),

                                TextEntry::make(
                                    'house.house_no'
                                )
                                    ->label('House')
                                    ->badge()
                                    ->placeholder('-'),

                            ]),

                    ]),


                /*
                |--------------------------------------------------------------------------
                | PERSONAL INFORMATION
                |--------------------------------------------------------------------------
                */

                Section::make('🧑 Personal Information')
                    ->schema([

                        Grid::make(4)
                            ->schema([

                                TextEntry::make('name')
                                    ->label('Name')
                                    ->placeholder('-'),

                                TextEntry::make('father_husband_name')
                                    ->label('Father / Husband')
                                    ->placeholder('-'),

                                TextEntry::make('dob')
                                    ->label('Date of Birth')
                                    ->date()
                                    ->placeholder('-'),

                                TextEntry::make('age')
                                    ->label('Age')
                                    ->numeric()
                                    ->placeholder('-'),

                                TextEntry::make('gender')
                                    ->label('Gender')
                                    ->badge()
                                    ->placeholder('-'),

                                TextEntry::make('caste')
                                    ->label('Caste')
                                    ->placeholder('-'),

                                TextEntry::make('religion')
                                    ->label('Religion')
                                    ->placeholder('-'),

                                TextEntry::make('category')
                                    ->label('Category')
                                    ->placeholder('-'),

                                TextEntry::make('occupation')
                                    ->label('Occupation')
                                    ->placeholder('-'),

                                TextEntry::make('education')
                                    ->label('Education')
                                    ->placeholder('-'),

                                TextEntry::make('blood_group')
                                    ->label('Blood Group')
                                    ->placeholder('-'),

                                TextEntry::make('voter_type')
                                    ->label('Voter Type')
                                    ->placeholder('-'),

                            ]),

                    ]),


                /*
                |--------------------------------------------------------------------------
                | CONTACT
                |--------------------------------------------------------------------------
                */

                Section::make('📞 Contact Information')
                    ->schema([

                        Grid::make(3)
                            ->schema([

                                TextEntry::make('mobile')
                                    ->label('Mobile')
                                    ->copyable()
                                    ->placeholder('-'),

                                TextEntry::make('whatsapp')
                                    ->label('WhatsApp')
                                    ->copyable()
                                    ->placeholder('-'),

                                TextEntry::make('email')
                                    ->label('Email')
                                    ->copyable()
                                    ->placeholder('-'),

                            ]),

                        TextEntry::make('address')
                            ->label('Address')
                            ->placeholder('-')
                            ->columnSpanFull(),

                    ]),


                /*
                |--------------------------------------------------------------------------
                | POLITICAL INTELLIGENCE
                |--------------------------------------------------------------------------
                */

                Section::make('🗳️ Political Intelligence')
                    ->description('Political preference and voter sentiment')
                    ->schema([

                        Grid::make(4)
                            ->schema([

                                TextEntry::make('politicalParty.name')
                                    ->label('Political Party')
                                    ->badge()
                                    ->placeholder('-'),

                                TextEntry::make('support_level')
                                    ->label('Support Level')
                                    ->badge()
                                    ->placeholder('-'),

                                TextEntry::make('party_preference')
                                    ->label('Political Remark')
                                    ->placeholder('-'),

                                TextEntry::make('priority')
                                    ->label('Priority')
                                    ->badge()
                                    ->placeholder('-'),

                            ]),

                    ]),


                /*
                |--------------------------------------------------------------------------
                | CAMPAIGN INTELLIGENCE
                |--------------------------------------------------------------------------
                */

                Section::make('🎯 Campaign Intelligence')
                    ->description('Campaign roles, engagement and follow-up')
                    ->schema([

                        Grid::make(4)
                            ->schema([

                                IconEntry::make('is_volunteer')
                                    ->label('Volunteer')
                                    ->boolean(),

                                IconEntry::make('is_influencer')
                                    ->label('Influencer')
                                    ->boolean(),

                                IconEntry::make('is_active')
                                    ->label('Active Voter')
                                    ->boolean(),

                                IconEntry::make('government_scheme')
                                    ->label('Government Scheme')
                                    ->boolean(),

                                TextEntry::make('government_scheme_name')
                                    ->label('Scheme Name')
                                    ->placeholder('-'),

                                TextEntry::make('last_contact_date')
                                    ->label('Last Contact')
                                    ->date()
                                    ->placeholder('-'),

                                TextEntry::make('next_followup')
                                    ->label('Next Follow-up')
                                    ->date()
                                    ->placeholder('-'),

                                IconEntry::make('disability')
                                    ->label('Disability')
                                    ->boolean(),

                            ]),

                    ]),


                /*
                |--------------------------------------------------------------------------
                | HOUSEHOLD
                |--------------------------------------------------------------------------
                */

                Section::make('🏠 Household Intelligence')
                    ->description('Family and household information')
                    ->schema([

                        Grid::make(4)
                            ->schema([

                                TextEntry::make('house.house_no')
                                    ->label('House No.')
                                    ->badge()
                                    ->placeholder('-'),

                                TextEntry::make('family_members')
                                    ->label('Family Members')
                                    ->numeric()
                                    ->placeholder('-'),

                                TextEntry::make('house.head_of_family')
                                    ->label('Head of Family')
                                    ->placeholder('-'),

                                TextEntry::make(
                                    'house.booth.village.name'
                                )
                                    ->label('Village')
                                    ->placeholder('-'),

                            ]),

                    ]),


                /*
                |--------------------------------------------------------------------------
                | ORGANISATION
                |--------------------------------------------------------------------------
                */

                Section::make('👥 Booth Organisation')
                    ->schema([

                        Grid::make(3)
                            ->schema([

                                TextEntry::make('booth_committee_role')
                                    ->label('Booth Committee Role')
                                    ->placeholder('-'),

                                TextEntry::make('panna_pramukh')
                                    ->label('Panna Pramukh')
                                    ->placeholder('-'),

                                TextEntry::make('polling_agent')
                                    ->label('Polling Agent')
                                    ->placeholder('-'),

                            ]),

                    ]),


                /*
                |--------------------------------------------------------------------------
                | SOCIAL MEDIA
                |--------------------------------------------------------------------------
                */

                Section::make('📱 Social Media')
                    ->schema([

                        Grid::make(3)
                            ->schema([

                                TextEntry::make('facebook')
                                    ->label('Facebook')
                                    ->placeholder('-'),

                                TextEntry::make('instagram')
                                    ->label('Instagram')
                                    ->placeholder('-'),

                                TextEntry::make('twitter')
                                    ->label('Twitter')
                                    ->placeholder('-'),

                            ]),

                    ]),


                /*
                |--------------------------------------------------------------------------
                | LOCATION
                |--------------------------------------------------------------------------
                */

                Section::make('📍 Location')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                TextEntry::make('latitude')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->placeholder('-'),

                                TextEntry::make('longitude')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->placeholder('-'),

                            ]),

                    ]),


                /*
                |--------------------------------------------------------------------------
                | NOTES
                |--------------------------------------------------------------------------
                */

                Section::make('📝 Campaign Notes')
                    ->schema([

                        TextEntry::make('internal_notes')
                            ->label('Internal Notes')
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('remarks')
                            ->label('Remarks')
                            ->placeholder('-')
                            ->columnSpanFull(),

                    ]),


                /*
                |--------------------------------------------------------------------------
                | SYSTEM INFORMATION
                |--------------------------------------------------------------------------
                */

                Section::make('⚙️ System Information')
                    ->schema([

                        Grid::make(3)
                            ->schema([

                                IconEntry::make('is_active')
                                    ->label('Active')
                                    ->boolean(),

                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime()
                                    ->placeholder('-'),

                                TextEntry::make('updated_at')
                                    ->label('Updated At')
                                    ->dateTime()
                                    ->placeholder('-'),

                            ]),

                    ]),

            ]);
    }
}