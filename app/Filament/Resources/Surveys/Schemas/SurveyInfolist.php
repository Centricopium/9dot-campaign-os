<?php

namespace App\Filament\Resources\Surveys\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SurveyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextEntry::make('name')
                    ->label('Survey Name'),

                TextEntry::make('description')
                    ->label('Description'),

                TextEntry::make('status')
                    ->badge(),

                TextEntry::make('start_date')
                    ->label('Start Date')
                    ->date(),

                TextEntry::make('end_date')
                    ->label('End Date')
                    ->date(),

                TextEntry::make('questions_count')
                    ->label('Total Questions')
                    ->state(fn ($record) => $record->questions()->count()),

                TextEntry::make('responses_count')
                    ->label('Survey Responses')
                    ->state(fn ($record) => $record->responses()->count()),

                TextEntry::make('created_at')
                    ->label('Created')
                    ->dateTime(),

                TextEntry::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime(),

            ]);
    }
}