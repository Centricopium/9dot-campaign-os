<?php

namespace App\Filament\Resources\CampaignIssues\Schemas;

use App\Models\Booth;
use App\Models\CampaignIssue;
use App\Models\Constituency;
use App\Models\House;
use App\Models\User;
use App\Models\Village;
use App\Models\Voter;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class CampaignIssueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Issue Area')->schema([
                Grid::make(3)->schema([
                    Select::make('constituency_id')->label('Assembly Constituency')->options(fn (): array => Constituency::query()->orderBy('name')->pluck('name', 'id')->all())->default(fn () => auth()->user()?->isAssemblyAdmin() ? auth()->user()?->constituency_id : null)->live()->native(true)->afterStateUpdated(function (Set $set): void {
                        $set('village_id', null);
                        $set('booth_id', null);
                        $set('house_id', null);
                        $set('voter_id', null);
                    })->required(),
                    Select::make('village_id')->options(fn (Get $get): array => $get('constituency_id') ? Village::query()->where('constituency_id', $get('constituency_id'))->orderBy('name')->pluck('name', 'id')->all() : [])->live()->native(true)->afterStateUpdated(function (Set $set): void {
                        $set('booth_id', null);
                        $set('house_id', null);
                        $set('voter_id', null);
                    }),
                    Select::make('booth_id')->options(fn (Get $get): array => $get('village_id') ? Booth::query()->where('village_id', $get('village_id'))->orderBy('booth_no')->get()->mapWithKeys(fn (Booth $booth): array => [$booth->id => ($booth->booth_no ?: 'Booth').' - '.($booth->booth_name ?: '')])->all() : [])->live()->native(true)->afterStateUpdated(function (Set $set): void {
                        $set('house_id', null);
                        $set('voter_id', null);
                    }),
                    Select::make('house_id')->label('House (search)')->searchable()->getSearchResultsUsing(fn (string $search, Get $get): array => House::query()->when($get('booth_id'), fn ($query, $id) => $query->where('booth_id', $id))->where(function ($query) use ($search): void {
                        $query->where('house_no', 'like', "%{$search}%")->orWhere('head_of_family', 'like', "%{$search}%");
                    })->limit(30)->get()->mapWithKeys(fn (House $house): array => [$house->id => $house->display_name])->all())->getOptionLabelUsing(fn ($value): ?string => House::find($value)?->display_name),
                    Select::make('voter_id')->label('Voter (name/EPIC search)')->searchable()->getSearchResultsUsing(fn (string $search, Get $get): array => Voter::query()->when($get('house_id'), fn ($query, $id) => $query->where('house_id', $id))->where(function ($query) use ($search): void {
                        $query->where('name', 'like', "%{$search}%")->orWhere('epic_no', 'like', "%{$search}%")->orWhere('mobile', 'like', "%{$search}%");
                    })->limit(30)->get()->mapWithKeys(fn (Voter $voter): array => [$voter->id => $voter->display_name])->all())->getOptionLabelUsing(fn ($value): ?string => Voter::find($value)?->display_name),
                    Select::make('assigned_to')->label('Responsible Person')->options(function (Get $get): array {
                        return User::query()->where('is_active', true)->when($get('constituency_id'), fn ($query, $id) => $query->where('constituency_id', $id))->orderBy('name')->get()->mapWithKeys(fn (User $user): array => [$user->id => $user->display_name])->all();
                    })->searchable()->native(true),
                ]),
            ]),
            Section::make('Complaint / Public Issue')->schema([
                Grid::make(3)->schema([
                    TextInput::make('title')->required()->maxLength(255)->columnSpan(2),
                    Select::make('category')->options(CampaignIssue::CATEGORIES)->default('Other')->native(true)->required(),
                    Select::make('source')->options(CampaignIssue::SOURCES)->default('Field Visit')->native(true)->required(),
                    Select::make('priority')->options(CampaignIssue::PRIORITIES)->default('Medium')->native(true)->required(),
                    Select::make('status')->options(fn (): array => (auth()->user()?->can('campaign_issue.resolve') ?? false) ? CampaignIssue::STATUSES : array_diff_key(CampaignIssue::STATUSES, array_flip(['Resolved', 'Closed'])))->default('Open')->native(true)->required(),
                ]),
                Textarea::make('description')->rows(5)->required()->columnSpanFull(),
                Grid::make(3)->schema([
                    TextInput::make('contact_name')->maxLength(255),
                    TextInput::make('contact_mobile')->tel()->maxLength(20),
                    DateTimePicker::make('due_at')->label('SLA Due Date')->helperText('If blank, deadline is calculated automatically from priority.'),
                ]),
                Toggle::make('is_confidential')->label('Confidential Issue')->helperText('Restricted to authorized campaign users.'),
                FileUpload::make('attachments')->disk('local')->directory('private/campaign-issues')->visibility('private')->multiple()->maxFiles(8)->maxSize(10240)->columnSpanFull(),
            ]),
            Section::make('Resolution')->visible(fn (?CampaignIssue $record): bool => $record !== null)->schema([
                Textarea::make('resolution_notes')->rows(4)->columnSpanFull(),
                FileUpload::make('resolution_proof')->disk('local')->directory('private/campaign-issues/resolutions')->visibility('private')->multiple()->maxFiles(8)->maxSize(10240)->columnSpanFull(),
            ]),
        ]);
    }
}
