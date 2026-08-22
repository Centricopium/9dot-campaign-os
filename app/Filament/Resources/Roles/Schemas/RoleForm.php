<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Role details')->schema([
                    TextInput::make('name')->required()->maxLength(255)->unique(ignoreRecord: true),
                    Hidden::make('guard_name')->default('web'),
                ]),
                Section::make('Permissions')->schema([
                    CheckboxList::make('permissions')
                        ->relationship('permissions', 'name')
                        ->columns(3)
                        ->gridDirection('row')
                        ->searchable()
                        ->bulkToggleable(),
                ]),
            ]);
    }
}
