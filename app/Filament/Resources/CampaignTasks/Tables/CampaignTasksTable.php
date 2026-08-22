<?php

namespace App\Filament\Resources\CampaignTasks\Tables;

use App\Models\CampaignTask;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CampaignTasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('due_at')
            ->columns([
                TextColumn::make('task_code')->label('Task ID')->badge()->color('primary')->searchable()->copyable(),
                TextColumn::make('title')->weight('bold')->searchable()->wrap()->limit(45),
                TextColumn::make('constituency.name')->label('Assembly')->searchable()->sortable()->toggleable(),
                TextColumn::make('village.name')->label('Village')->placeholder('All')->searchable()->toggleable(),
                TextColumn::make('booth.booth_no')->label('Booth')->placeholder('All')->toggleable(),
                TextColumn::make('assignee.name')->label('Assigned To')->placeholder('Unassigned')->searchable(),
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Critical' => 'danger',
                        'High' => 'warning',
                        'Medium' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Approved' => 'success',
                        'Completed' => 'info',
                        'Rejected' => 'danger',
                        'In Progress' => 'warning',
                        'Cancelled' => 'gray',
                        default => 'primary',
                    })
                    ->sortable(),
                TextColumn::make('completion_percent')->label('Progress')->suffix('%')->sortable(),
                TextColumn::make('due_at')
                    ->label('Due')
                    ->dateTime('d M, h:i A')
                    ->placeholder('No deadline')
                    ->color(fn (CampaignTask $record): string => $record->is_overdue ? 'danger' : 'gray')
                    ->sortable(),
                IconColumn::make('requires_proof')->label('Proof')->boolean()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('constituency')->relationship('constituency', 'name')->label('Assembly'),
                SelectFilter::make('village')->relationship('village', 'name'),
                SelectFilter::make('assigned_to')->relationship('assignee', 'name')->label('Assigned To'),
                SelectFilter::make('priority')->options(CampaignTask::PRIORITIES),
                SelectFilter::make('status')->options(CampaignTask::STATUSES),
                Filter::make('overdue')->label('Overdue Only')->query(fn (Builder $query): Builder => $query->overdue()),
            ])
            ->recordActions([
                Action::make('start')
                    ->label('Start')
                    ->icon('heroicon-o-play')
                    ->color('warning')
                    ->visible(fn (CampaignTask $record): bool => (auth()->user()?->can('campaign_task.update') ?? false)
                        && in_array($record->status, ['Pending', 'Assigned', 'Rejected', 'On Hold'], true))
                    ->action(function (CampaignTask $record): void {
                        $record->start(auth()->user());
                        Notification::make()->title('Task started')->success()->send();
                    }),
                Action::make('complete')
                    ->label('Complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (CampaignTask $record): bool => (auth()->user()?->can('campaign_task.update') ?? false)
                        && in_array($record->status, ['Assigned', 'In Progress', 'Rejected'], true))
                    ->schema([
                        Textarea::make('notes')->label('Completion Notes')->rows(4)->required(),
                        FileUpload::make('proof_files')
                            ->label('Completion Proof')
                            ->disk('public')
                            ->directory('campaign-tasks')
                            ->multiple()
                            ->maxFiles(8)
                            ->maxSize(10240),
                    ])
                    ->action(function (CampaignTask $record, array $data): void {
                        $record->complete(auth()->user(), $data['notes'] ?? null, $data['proof_files'] ?? []);
                        Notification::make()->title('Task submitted for review')->success()->send();
                    }),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-shield-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->schema([Textarea::make('notes')->label('Review Notes')->rows(3)])
                    ->visible(fn (CampaignTask $record): bool => (auth()->user()?->can('campaign_task.review') ?? false)
                        && $record->status === 'Completed')
                    ->action(function (CampaignTask $record, array $data): void {
                        $record->review(auth()->user(), true, $data['notes'] ?? null);
                        Notification::make()->title('Task approved')->success()->send();
                    }),
                Action::make('reject')
                    ->label('Send for Rework')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->schema([Textarea::make('notes')->label('Rework Instructions')->rows(3)->required()])
                    ->visible(fn (CampaignTask $record): bool => (auth()->user()?->can('campaign_task.review') ?? false)
                        && $record->status === 'Completed')
                    ->action(function (CampaignTask $record, array $data): void {
                        $record->review(auth()->user(), false, $data['notes']);
                        Notification::make()->title('Task returned for rework')->warning()->send();
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
