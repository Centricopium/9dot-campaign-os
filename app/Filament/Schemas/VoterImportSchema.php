<?php

namespace App\Filament\Schemas;

use App\Models\Constituency;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class VoterImportSchema
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Voter Excel Import')
                ->description('Upload a Voter Electoral Roll Excel file.')
                ->schema([
                    Grid::make(2)->schema([

                        Select::make('constituency_id')
                            ->label('Constituency')
                            ->options(fn (): array => Constituency::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(
                                fn (): bool =>
                                    Auth::user()?->isSuperAdmin() ?? false
                            ),

                        FileUpload::make('excel_file')
                            ->label('Excel File')
                            ->acceptedFileTypes([
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->disk('local')
                            ->directory('imports/voters')
                            ->maxSize(65536)
                            ->required(),

                    ]),
                ]),
        ]);
    }
}
