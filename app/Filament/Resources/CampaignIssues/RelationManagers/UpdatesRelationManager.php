<?php

namespace App\Filament\Resources\CampaignIssues\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UpdatesRelationManager extends RelationManager
{
    protected static string $relationship = 'updates';

    protected static ?string $title = 'Issue Timeline';

    public function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([TextColumn::make('created_at')->dateTime('d M Y, h:i A'), TextColumn::make('user.name')->placeholder('System'), TextColumn::make('new_status')->badge(), TextColumn::make('notes')->wrap()->placeholder('-')]);
    }
}
