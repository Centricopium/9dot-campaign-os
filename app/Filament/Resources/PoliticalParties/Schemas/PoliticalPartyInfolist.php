<?php

namespace App\Filament\Resources\PoliticalParties\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PoliticalPartyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('short_name'),
                TextEntry::make('symbol')
                    ->placeholder('-'),
                TextEntry::make('color'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('sort_order')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
