<?php

namespace App\Filament\Resources\CampaignTasks\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UpdatesRelationManager extends RelationManager
{
    protected static string $relationship = 'updates';

    protected static ?string $title = 'Task Timeline';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Updated At')->dateTime('d M Y, h:i A')->sortable(),
                TextColumn::make('user.name')->label('Updated By')->placeholder('System'),
                TextColumn::make('new_status')->label('Status')->badge(),
                TextColumn::make('completion_percent')->label('Progress')->suffix('%'),
                TextColumn::make('notes')->wrap()->placeholder('-')->limit(100),
            ]);
    }
}
