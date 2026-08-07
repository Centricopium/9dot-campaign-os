<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | Profile
            |--------------------------------------------------------------------------
            */

            Section::make('👤 Profile')
                ->schema([

                    Grid::make(2)
                        ->schema([

                            ImageEntry::make('profile_photo_url')
                                ->label('Photo')
                                ->circular(),

                            TextEntry::make('name')
                                ->label('Full Name')
                                ->weight('bold'),

                            TextEntry::make('email')
                                ->label('Email'),

                            TextEntry::make('mobile')
                                ->label('Mobile')
                                ->placeholder('-'),

                            TextEntry::make('designation')
                                ->placeholder('-'),

                            TextEntry::make('employee_code')
                                ->placeholder('-'),

                        ]),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Role & Area
            |--------------------------------------------------------------------------
            */

            Section::make('🎭 Role & Area')
                ->schema([

                    Grid::make(2)
                        ->schema([

                            TextEntry::make('roles.name')
                                ->label('Role')
                                ->badge(),

                            TextEntry::make('constituency.name')
                                ->label('Constituency')
                                ->placeholder('-'),

                            TextEntry::make('village.name')
                                ->label('Village')
                                ->placeholder('-'),

                            TextEntry::make('booth.booth_name')
                                ->label('Booth')
                                ->placeholder('-'),

                        ]),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            Section::make('✅ Status')
                ->schema([

                    Grid::make(2)
                        ->schema([

                            IconEntry::make('is_super_admin')
                                ->label('Super Admin')
                                ->boolean(),

                            IconEntry::make('is_active')
                                ->label('Active')
                                ->boolean(),

                            TextEntry::make('last_login_at')
                                ->label('Last Login')
                                ->since()
                                ->placeholder('Never'),

                        ]),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            Section::make('📝 Notes')
                ->schema([

                    TextEntry::make('notes')
                        ->placeholder('-'),

                ]),

        ]);
    }
}