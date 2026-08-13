<?php

namespace App\Filament\Resources\Voters\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VoterInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | Electoral Location
                |--------------------------------------------------------------------------
                */

                TextEntry::make('house.booth.village.constituency.name')
                    ->label('Constituency')
                    ->placeholder('-'),

                TextEntry::make('house.booth.village.name')
                    ->label('Village')
                    ->placeholder('-'),

                TextEntry::make('house.booth.booth_no')
                    ->label('Booth')
                    ->placeholder('-'),

                TextEntry::make('house.house_no')
                    ->label('House')
                    ->placeholder('-'),

                TextEntry::make('serial_no')
                    ->label('Serial No.')
                    ->placeholder('-'),

                TextEntry::make('part_no')
                    ->label('Part No.')
                    ->placeholder('-'),

                TextEntry::make('epic_no')
                    ->label('EPIC No.')
                    ->placeholder('-'),

                /*
                |--------------------------------------------------------------------------
                | Personal Information
                |--------------------------------------------------------------------------
                */

                TextEntry::make('name')
                    ->label('Name')
                    ->placeholder('-'),

                TextEntry::make('father_husband_name')
                    ->label('Father / Husband Name')
                    ->placeholder('-'),

                TextEntry::make('gender')
                    ->label('Gender')
                    ->placeholder('-'),

                TextEntry::make('age')
                    ->label('Age')
                    ->numeric()
                    ->placeholder('-'),

                TextEntry::make('dob')
                    ->label('DOB')
                    ->date()
                    ->placeholder('-'),

                /*
                |--------------------------------------------------------------------------
                | Contact Information
                |--------------------------------------------------------------------------
                */

                TextEntry::make('mobile')
                    ->label('Mobile')
                    ->placeholder('-'),

                TextEntry::make('whatsapp')
                    ->label('WhatsApp')
                    ->placeholder('-'),

                TextEntry::make('email')
                    ->label('Email')
                    ->placeholder('-'),

                /*
                |--------------------------------------------------------------------------
                | Address Information
                |--------------------------------------------------------------------------
                */

                TextEntry::make('house_no')
                    ->label('House No.')
                    ->placeholder('-'),

                TextEntry::make('address')
                    ->label('Address')
                    ->placeholder('-')
                    ->columnSpanFull(),

                TextEntry::make('photo')
                    ->label('Photo')
                    ->placeholder('-'),

                /*
                |--------------------------------------------------------------------------
                | Social Information
                |--------------------------------------------------------------------------
                */

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

                /*
                |--------------------------------------------------------------------------
                | Political Information
                |--------------------------------------------------------------------------
                */

                TextEntry::make('support_level')
                    ->label('Support Level')
                    ->badge()
                    ->placeholder('-'),

                TextEntry::make('politicalParty.name')
                    ->label('Political Party')
                    ->badge()
                    ->placeholder('-'),

                TextEntry::make('party_preference')
                    ->label('Political Remark')
                    ->placeholder('-'),

                IconEntry::make('is_influencer')
                    ->label('Is Influencer')
                    ->boolean(),

                IconEntry::make('is_volunteer')
                    ->label('Is Volunteer')
                    ->boolean(),

                /*
                |--------------------------------------------------------------------------
                | Campaign Information
                |--------------------------------------------------------------------------
                */

                TextEntry::make('last_contact_date')
                    ->label('Last Contact Date')
                    ->date()
                    ->placeholder('-'),

                TextEntry::make('voter_type')
                    ->label('Voter Type')
                    ->placeholder('-'),

                TextEntry::make('blood_group')
                    ->label('Blood Group')
                    ->placeholder('-'),

                IconEntry::make('disability')
                    ->label('Disability')
                    ->boolean(),

                IconEntry::make('government_scheme')
                    ->label('Government Scheme')
                    ->boolean(),

                TextEntry::make('government_scheme_name')
                    ->label('Government Scheme Name')
                    ->placeholder('-'),

                /*
                |--------------------------------------------------------------------------
                | Social Media
                |--------------------------------------------------------------------------
                */

                TextEntry::make('facebook')
                    ->label('Facebook')
                    ->placeholder('-'),

                TextEntry::make('instagram')
                    ->label('Instagram')
                    ->placeholder('-'),

                TextEntry::make('twitter')
                    ->label('Twitter')
                    ->placeholder('-'),

                /*
                |--------------------------------------------------------------------------
                | Organisation Information
                |--------------------------------------------------------------------------
                */

                TextEntry::make('family_members')
                    ->label('Family Members')
                    ->numeric()
                    ->placeholder('-'),

                TextEntry::make('booth_committee_role')
                    ->label('Booth Committee Role')
                    ->placeholder('-'),

                TextEntry::make('panna_pramukh')
                    ->label('Panna Pramukh')
                    ->placeholder('-'),

                TextEntry::make('polling_agent')
                    ->label('Polling Agent')
                    ->placeholder('-'),

                TextEntry::make('priority')
                    ->label('Priority')
                    ->badge()
                    ->placeholder('-'),

                TextEntry::make('next_followup')
                    ->label('Next Follow-up')
                    ->date()
                    ->placeholder('-'),

                /*
                |--------------------------------------------------------------------------
                | Location / GPS
                |--------------------------------------------------------------------------
                */

                TextEntry::make('latitude')
                    ->label('Latitude')
                    ->numeric()
                    ->placeholder('-'),

                TextEntry::make('longitude')
                    ->label('Longitude')
                    ->numeric()
                    ->placeholder('-'),

                /*
                |--------------------------------------------------------------------------
                | Notes
                |--------------------------------------------------------------------------
                */

                TextEntry::make('internal_notes')
                    ->label('Internal Notes')
                    ->placeholder('-')
                    ->columnSpanFull(),

                TextEntry::make('remarks')
                    ->label('Remarks')
                    ->placeholder('-')
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | System Information
                |--------------------------------------------------------------------------
                */

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

            ]);
    }
}