<?php

namespace App\Filament\Resources\Houses\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HouseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('🏠 House Information')
                ->schema([

                    Grid::make(2)
                        ->schema([

                            TextEntry::make('house_no')
                                ->label('House No'),

                            TextEntry::make('head_of_family')
                                ->label('Head of Family'),

                            TextEntry::make('mobile')
                                ->label('Mobile')
                                ->placeholder('-'),

                            TextEntry::make('booth.village.name')
                                ->label('Village'),

                            TextEntry::make('booth.booth_name')
                                ->label('Booth'),

                            TextEntry::make('address')
                                ->label('Address')
                                ->placeholder('-')
                                ->columnSpanFull(),

                        ]),

                ]),

            Section::make('📍 GPS Information')
                ->schema([

                    Grid::make(2)
                        ->schema([

                            TextEntry::make('gps_latitude')
                                ->label('Latitude')
                                ->placeholder('-'),

                            TextEntry::make('gps_longitude')
                                ->label('Longitude')
                                ->placeholder('-'),

                        ]),

                ]),

            Section::make('👨‍👩‍👧 Family Summary')
                ->schema([

                    Grid::make(4)
                        ->schema([

                            TextEntry::make('total_voters')
                                ->label('Family Members')
                                ->badge()
                                ->color('primary'),

                            TextEntry::make('active_voters')
                                ->label('Active')
                                ->badge()
                                ->color('success'),

                            TextEntry::make('volunteer_count')
                                ->label('Volunteers')
                                ->badge()
                                ->color('warning'),

                            TextEntry::make('influencer_count')
                                ->label('Influencers')
                                ->badge()
                                ->color('info'),

                            TextEntry::make('supporter_count')
                                ->label('Supporters')
                                ->badge()
                                ->color('success'),

                            TextEntry::make('opposition_count')
                                ->label('Opposition')
                                ->badge()
                                ->color('danger'),

                            TextEntry::make('undecided_count')
                                ->label('Undecided')
                                ->badge()
                                ->color('gray'),

                        ]),

                ]),

            Section::make('✅ Status')
                ->schema([

                    Grid::make(2)
                        ->schema([

                            IconEntry::make('is_verified')
                                ->label('Verified')
                                ->boolean(),

                            IconEntry::make('is_active')
                                ->label('Active')
                                ->boolean(),

                        ]),

                ]),
        ]);
    }
}