<?php

namespace App\Filament\Resources\CampaignTasks\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CampaignTaskInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Task Summary')->schema([
                Grid::make(4)->schema([
                    TextEntry::make('task_code')->label('Task ID')->badge()->color('primary'),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('priority')->badge(),
                    TextEntry::make('completion_percent')->suffix('%'),
                ]),
                TextEntry::make('title')->weight('bold')->columnSpanFull(),
                TextEntry::make('description')->placeholder('No description')->columnSpanFull(),
            ]),
            Section::make('Area & Team')->schema([
                Grid::make(4)->schema([
                    TextEntry::make('constituency.name')->label('Assembly'),
                    TextEntry::make('village.name')->placeholder('All villages'),
                    TextEntry::make('booth.booth_name')->placeholder('All booths'),
                    TextEntry::make('assignee.display_name')->label('Assigned To')->placeholder('Unassigned'),
                ]),
            ]),
            Section::make('Schedule & Progress')->schema([
                Grid::make(4)->schema([
                    TextEntry::make('category')->badge(),
                    TextEntry::make('starts_at')->dateTime('d M Y, h:i A')->placeholder('-'),
                    TextEntry::make('due_at')->dateTime('d M Y, h:i A')->placeholder('-'),
                    IconEntry::make('is_overdue')->label('Overdue')->boolean(),
                ]),
                TextEntry::make('field_notes')->placeholder('No field notes')->columnSpanFull(),
                TextEntry::make('completion_notes')->placeholder('No completion note')->columnSpanFull(),
                TextEntry::make('proof_files')
                    ->label('Proof Files')
                    ->formatStateUsing(fn ($state): string => empty($state) ? 'No proof uploaded' : count((array) $state).' file(s) uploaded'),
            ]),
            Section::make('Supervisor Review')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('reviewer.display_name')->label('Reviewed By')->placeholder('-'),
                    TextEntry::make('reviewed_at')->dateTime('d M Y, h:i A')->placeholder('-'),
                    TextEntry::make('review_notes')->placeholder('No review note'),
                ]),
            ])->visible(fn ($record): bool => filled($record?->reviewed_at)),
        ]);
    }
}
