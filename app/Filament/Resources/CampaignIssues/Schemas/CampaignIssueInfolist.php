<?php

namespace App\Filament\Resources\CampaignIssues\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CampaignIssueInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Issue Summary')->schema([
                Grid::make(4)->schema([
                    TextEntry::make('issue_code')->label('Issue ID')->badge()->color('primary'), TextEntry::make('status')->badge(), TextEntry::make('priority')->badge(), TextEntry::make('category')->badge(),
                ]),
                TextEntry::make('title')->weight('bold')->columnSpanFull(), TextEntry::make('description')->columnSpanFull(),
            ]),
            Section::make('Area & Responsibility')->schema([
                Grid::make(4)->schema([
                    TextEntry::make('constituency.name')->label('Assembly'), TextEntry::make('village.name')->placeholder('-'), TextEntry::make('booth.booth_name')->placeholder('-'), TextEntry::make('assignee.display_name')->label('Responsible Person')->placeholder('Unassigned'),
                    TextEntry::make('house.display_name')->label('House')->placeholder('-'), TextEntry::make('voter.display_name')->label('Voter')->placeholder('-'), TextEntry::make('due_at')->dateTime('d M Y, h:i A'), IconEntry::make('is_overdue')->boolean(),
                ]),
            ]),
            Section::make('Contact & Resolution')->schema([
                Grid::make(3)->schema([TextEntry::make('contact_name')->placeholder('-'), TextEntry::make('contact_mobile')->placeholder('-'), IconEntry::make('is_confidential')->boolean()]),
                TextEntry::make('resolution_notes')->placeholder('Resolution pending')->columnSpanFull(),
                TextEntry::make('resolver.display_name')->label('Resolved By')->placeholder('-'),
            ]),
        ]);
    }
}
