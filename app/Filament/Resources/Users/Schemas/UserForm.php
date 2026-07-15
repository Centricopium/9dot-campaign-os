<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Booth;
use App\Models\Constituency;
use App\Models\Village;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Spatie\Permission\Models\Role;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

    Section::make('Basic Information')
        ->schema([

            Grid::make(2)
                ->schema([

                    TextInput::make('name')
                        ->label('Full Name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Email Address')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true),

                    TextInput::make('mobile')
                        ->label('Mobile Number')
                        ->tel()
                        ->maxLength(15),

                    TextInput::make('designation')
                        ->label('Designation'),

                    TextInput::make('employee_code')
                        ->label('Employee Code'),

                    Toggle::make('is_active')
                        ->default(true),

                ]),

        ]),

    Section::make('Login')
        ->schema([

            Grid::make(2)
                ->schema([

                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $operation): bool => $operation === 'create'),

                    DateTimePicker::make('email_verified_at'),

                ]),
    Section::make('Area Assignment')
    ->schema([

        Grid::make(3)
            ->schema([

                Select::make('constituency_id')
                    ->label('Constituency')
                    ->relationship('constituency', 'name')
                    ->searchable()
                    ->preload()
                    ->live(),

                Select::make('village_id')
                    ->label('Village')
                    ->relationship('village', 'name')
                    ->searchable()
                    ->preload(),

                Select::make('booth_id')
    ->label('Booth')
    ->relationship('booth', 'booth_name')
    ->searchable()
    ->preload(),

        
                    ]),
                    Section::make('Role Assignment')
    ->schema([

        Select::make('roles')
            ->label('User Role')
            ->relationship('roles', 'name')
            ->multiple()
            ->preload()
            ->searchable(),

    ]),
    Section::make('Profile')
    ->schema([

        FileUpload::make('profile_photo')
            ->image()
            ->directory('users')
            ->imageEditor(),

        Textarea::make('notes')
            ->rows(4)
            ->columnSpanFull(),

    ]),

    ]),    
        ]),

]);
    }
}
