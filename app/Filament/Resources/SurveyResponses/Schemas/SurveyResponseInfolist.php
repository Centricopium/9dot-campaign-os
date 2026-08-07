<?php

namespace App\Filament\Resources\SurveyResponses\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SurveyResponseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextEntry::make('survey.name')
                    ->label('Survey'),

                TextEntry::make('user.name')
                    ->label('Survey By')
                    ->placeholder('-'),

                TextEntry::make('voter.name')
                    ->label('Voter')
                    ->placeholder('-'),

                TextEntry::make('house.house_no')
                    ->label('House')
                    ->placeholder('-'),

                TextEntry::make('latitude')
                    ->label('Latitude')
                    ->numeric()
                    ->placeholder('-'),

                TextEntry::make('longitude')
                    ->label('Longitude')
                    ->numeric()
                    ->placeholder('-'),

                TextEntry::make('submitted_at')
                    ->label('Submitted At')
                    ->dateTime('d M Y h:i A')
                    ->placeholder('-'),

                TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y h:i A')
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('d M Y h:i A')
                    ->placeholder('-'),

            ]);
    }
}