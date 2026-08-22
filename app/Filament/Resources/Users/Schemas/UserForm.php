<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Booth;
use App\Models\Constituency;
use App\Models\Role;
use App\Models\Village;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Basic Information')
                ->schema([

                    Grid::make(2)
                        ->schema([

                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('email')
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true),

                            TextInput::make('mobile')
                                ->tel()
                                ->maxLength(10),

                            TextInput::make('employee_code')
                                ->unique(ignoreRecord: true),

                            TextInput::make('designation'),

                            FileUpload::make('profile_photo')
                                ->directory('users')
                                ->image(),

                        ]),

                ]),

            Section::make('Login')
                ->schema([

                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn (?string $state): bool => filled($state)),

                ]),

            Section::make('Role & Access')
                ->schema([

                    Select::make('role_id')
                        ->label('Role')
                        ->options(function (): array {
                            return Role::query()
                                ->where('guard_name', 'web')
                                ->when(
                                    ! auth()->user()?->isSuperAdmin(),
                                    fn ($query) => $query->where('name', '!=', 'Super Admin'),
                                )
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all();
                        })
                        ->native(true)
                        ->required(),

                ]),

            Section::make('Area Assignment')
                ->schema([

                    Grid::make(3)
                        ->schema([

                            Select::make('constituency_id')
                                ->label('Constituency')
                                ->options(fn () => Constituency::pluck('name', 'id')->toArray())
                                ->live()
                                ->searchable()
                                ->afterStateUpdated(function (Set $set) {
                                    $set('village_id', null);
                                    $set('booth_id', null);
                                }),

                            Select::make('village_id')
                                ->label('Village')
                                ->options(function (Get $get) {
                                    if (! $get('constituency_id')) {
                                        return [];
                                    }

                                    return Village::where('constituency_id', $get('constituency_id'))
                                        ->pluck('name', 'id')
                                        ->toArray();
                                })
                                ->live()
                                ->searchable()
                                ->afterStateUpdated(function (Set $set) {
                                    $set('booth_id', null);
                                }),

                            Select::make('booth_id')
                                ->label('Booth')
                                ->options(function (Get $get) {
                                    if (! $get('village_id')) {
                                        return [];
                                    }

                                    return Booth::where('village_id', $get('village_id'))
                                        ->pluck('booth_name', 'id')
                                        ->toArray();
                                })
                                ->searchable(),

                        ]),

                ]),

            Section::make('Status')
                ->schema([

                    Toggle::make('is_super_admin')
                        ->label('Super Admin')
                        ->hidden(fn () => ! auth()->user()?->isSuperAdmin())
                        ->default(false),

                    Toggle::make('is_active')
                        ->default(true),

                    Textarea::make('notes')
                        ->rows(4)
                        ->columnSpanFull(),

                ]),

        ]);
    }
}
