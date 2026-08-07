<?php

namespace App\Filament\Resources\Houses\RelationManagers;

use App\Filament\Resources\Voters\Schemas\VoterForm;
use App\Filament\Resources\Voters\Tables\VotersTable;
use App\Filament\Resources\Voters\VoterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class VotersRelationManager extends RelationManager
{
    protected static string $relationship = 'voters';

    protected static ?string $relatedResource = VoterResource::class;

    protected static ?string $title = 'Family Members';

    protected static ?string $modelLabel = 'Family Member';

    protected static ?string $pluralModelLabel = 'Family Members';

    public function form(Schema $schema): Schema
    {
        return VoterForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return VotersTable::configure($table)
            ->headerActions([
                CreateAction::make()
                    ->label('Add Family Member'),
            ]);
    }
}