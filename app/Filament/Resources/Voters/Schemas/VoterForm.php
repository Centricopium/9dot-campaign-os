<?php

namespace App\Filament\Resources\Voters\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VoterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('house_id')
                    ->numeric(),
                TextInput::make('part_no'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('photo'),
                TextInput::make('category'),
                TextInput::make('party_preference'),
                Toggle::make('is_volunteer')
                    ->required(),
                DatePicker::make('last_contact_date'),
            ]);
    }
}
