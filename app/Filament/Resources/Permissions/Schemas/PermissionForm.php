<?php

namespace App\Filament\Resources\Permissions\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Permission details')->schema([
                    TextInput::make('name')
                        ->helperText('Use resource.action format, for example voter.view.')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    Hidden::make('guard_name')->default('web'),
                ]),
            ]);
    }
}
