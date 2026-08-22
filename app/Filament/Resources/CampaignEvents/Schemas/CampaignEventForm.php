<?php

namespace App\Filament\Resources\CampaignEvents\Schemas;

use App\Models\Booth;
use App\Models\CampaignEvent;
use App\Models\Candidate;
use App\Models\Constituency;
use App\Models\User;
use App\Models\Village;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class CampaignEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Event Schedule')->schema([Grid::make(3)->schema([Select::make('constituency_id')->label('Assembly')->options(fn () => Constituency::query()->orderBy('name')->pluck('name', 'id')->all())->default(fn () => auth()->user()?->isAssemblyAdmin() ? auth()->user()?->constituency_id : null)->live()->native(true)->afterStateUpdated(function (Set $set): void {
                $set('village_id', null);
                $set('booth_id', null);
                $set('candidate_id', null);
                $set('coordinator_id', null);
            })->required(), Select::make('village_id')->options(fn (Get $get) => $get('constituency_id') ? Village::query()->where('constituency_id', $get('constituency_id'))->orderBy('name')->pluck('name', 'id')->all() : [])->live()->native(true)->afterStateUpdated(fn (Set $set) => $set('booth_id', null)), Select::make('booth_id')->options(fn (Get $get) => $get('village_id') ? Booth::query()->where('village_id', $get('village_id'))->orderBy('booth_no')->pluck('booth_name', 'id')->all() : [])->native(true), TextInput::make('title')->required()->columnSpan(2), Select::make('event_type')->options(CampaignEvent::TYPES)->default('Public Meeting')->native(true)->required(), DateTimePicker::make('starts_at')->required(), DateTimePicker::make('ends_at')->minDate(fn (Get $get) => $get('starts_at')), Select::make('status')->options(CampaignEvent::STATUSES)->default('Planned')->native(true)->required()])]),
            Section::make('Venue & Leadership')->schema([Grid::make(2)->schema([TextInput::make('venue')->required(), TextInput::make('address'), Select::make('candidate_id')->label('Candidate / Leader')->options(fn (Get $get) => $get('constituency_id') ? Candidate::query()->where('constituency_id', $get('constituency_id'))->orderBy('name')->pluck('name', 'id')->all() : [])->native(true), Select::make('coordinator_id')->label('Event Coordinator')->options(fn (Get $get) => User::query()->where('is_active', true)->when($get('constituency_id'), fn ($q, $id) => $q->where('constituency_id', $id))->orderBy('name')->pluck('name', 'id')->all())->native(true)]), Textarea::make('description')->rows(3)->columnSpanFull()]),
            Section::make('Attendance, Budget & Permissions')->schema([Grid::make(4)->schema([TextInput::make('expected_attendance')->numeric()->minValue(0)->default(0), TextInput::make('actual_attendance')->numeric()->minValue(0)->default(0), TextInput::make('estimated_budget')->numeric()->minValue(0)->prefix('₹')->default(0), TextInput::make('actual_expense')->numeric()->minValue(0)->prefix('₹')->default(0), Select::make('permission_status')->options(CampaignEvent::PERMISSIONS)->default('Not Required')->native(true), TextInput::make('permission_reference')->columnSpan(3)]), CheckboxList::make('requirements')->options(CampaignEvent::REQUIREMENTS)->columns(3)->columnSpanFull(), FileUpload::make('attachments')->disk('local')->directory('private/campaign-events')->visibility('private')->multiple()->maxFiles(8)->columnSpanFull(), Textarea::make('outcome_notes')->rows(3)->columnSpanFull()])]);
    }
}
