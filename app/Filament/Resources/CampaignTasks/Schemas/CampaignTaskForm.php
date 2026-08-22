<?php

namespace App\Filament\Resources\CampaignTasks\Schemas;

use App\Models\Booth;
use App\Models\CampaignTask;
use App\Models\Constituency;
use App\Models\User;
use App\Models\Village;
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

class CampaignTaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Task Area & Assignment')
                ->description('Assign the task to an assembly, village, booth and field team member.')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('constituency_id')
                            ->label('Assembly Constituency')
                            ->options(fn (): array => Constituency::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->default(fn (): ?int => auth()->user()?->isAssemblyAdmin() ? auth()->user()?->constituency_id : null)
                            ->live()
                            ->native(true)
                            ->afterStateUpdated(function (Set $set): void {
                                $set('village_id', null);
                                $set('booth_id', null);
                                $set('assigned_to', null);
                            })
                            ->required(),
                        Select::make('village_id')
                            ->options(fn (Get $get): array => $get('constituency_id')
                                ? Village::query()->where('constituency_id', $get('constituency_id'))->orderBy('name')->pluck('name', 'id')->all()
                                : [])
                            ->live()
                            ->native(true)
                            ->afterStateUpdated(function (Set $set): void {
                                $set('booth_id', null);
                            }),
                        Select::make('booth_id')
                            ->label('Booth')
                            ->options(fn (Get $get): array => $get('village_id')
                                ? Booth::query()->where('village_id', $get('village_id'))->orderBy('booth_no')->get()->mapWithKeys(
                                    fn (Booth $booth): array => [$booth->id => trim(($booth->booth_no ? $booth->booth_no.' - ' : '').($booth->booth_name ?: 'Booth'))],
                                )->all()
                                : [])
                            ->native(true),
                        Select::make('assigned_to')
                            ->label('Assign To')
                            ->options(function (Get $get): array {
                                $query = User::query()->where('is_active', true);
                                $constituencyId = $get('constituency_id');

                                if ($constituencyId) {
                                    $query->where('constituency_id', $constituencyId);
                                } elseif (auth()->user()?->isAssemblyAdmin()) {
                                    $query->where('constituency_id', auth()->user()?->constituency_id);
                                }

                                return $query->orderBy('name')->get()->mapWithKeys(
                                    fn (User $user): array => [$user->id => $user->display_name],
                                )->all();
                            })
                            ->searchable()
                            ->native(true),
                    ]),
                ]),
            Section::make('Task Details')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('title')->required()->maxLength(255)->columnSpan(2),
                        Select::make('category')->options(CampaignTask::CATEGORIES)->default('Field Visit')->native(true)->required(),
                        Select::make('priority')->options(CampaignTask::PRIORITIES)->default('Medium')->native(true)->required(),
                        Select::make('status')
                            ->options(fn (): array => (auth()->user()?->can('campaign_task.review') ?? false)
                                ? CampaignTask::STATUSES
                                : array_diff_key(CampaignTask::STATUSES, array_flip(['Approved', 'Rejected'])))
                            ->default('Pending')->native(true)->required(),
                        TextInput::make('completion_percent')->numeric()->minValue(0)->maxValue(100)->default(0)->suffix('%'),
                    ]),
                    Textarea::make('description')->rows(4)->columnSpanFull(),
                ]),
            Section::make('Schedule & Completion Rules')
                ->schema([
                    Grid::make(3)->schema([
                        DateTimePicker::make('starts_at')->label('Start Date & Time'),
                        DateTimePicker::make('due_at')->label('Due Date & Time')->minDate(fn (Get $get) => $get('starts_at')),
                        Toggle::make('requires_proof')->label('Completion Proof Required')->default(false),
                    ]),
                ]),
            Section::make('Field Progress')
                ->description('The field worker can add notes, proof and location while completing the task.')
                ->schema([
                    Textarea::make('field_notes')->rows(3)->columnSpanFull(),
                    Textarea::make('completion_notes')->rows(3)->columnSpanFull(),
                    FileUpload::make('proof_files')
                        ->label('Photos / Documents')
                        ->disk('public')
                        ->directory('campaign-tasks')
                        ->multiple()
                        ->maxFiles(8)
                        ->maxSize(10240)
                        ->downloadable()
                        ->openable()
                        ->columnSpanFull(),
                    Grid::make(2)->schema([
                        TextInput::make('latitude')->numeric()->minValue(-90)->maxValue(90),
                        TextInput::make('longitude')->numeric()->minValue(-180)->maxValue(180),
                    ]),
                ]),
            Section::make('Supervisor Review')
                ->visible(fn (?CampaignTask $record): bool => (auth()->user()?->can('campaign_task.review') ?? false)
                    && in_array($record?->status, ['Completed', 'Approved', 'Rejected'], true))
                ->schema([
                    Textarea::make('review_notes')->rows(3)->columnSpanFull(),
                ]),
        ]);
    }
}
