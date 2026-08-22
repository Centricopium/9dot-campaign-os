<?php

namespace App\Filament\Resources\CampaignIssues\Tables;

use App\Models\CampaignIssue;
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

class CampaignIssuesTable
{
    public static function configure(Table $table): Table
    {
        return $table->defaultSort('due_at')->columns([
            TextColumn::make('issue_code')->label('Issue ID')->badge()->color('primary')->searchable()->copyable(),
            TextColumn::make('title')->weight('bold')->searchable()->wrap()->limit(45),
            TextColumn::make('category')->badge()->sortable(),
            TextColumn::make('constituency.name')->label('Assembly')->sortable()->toggleable(),
            TextColumn::make('village.name')->label('Village')->placeholder('All')->toggleable(),
            TextColumn::make('assignee.name')->label('Responsible')->placeholder('Unassigned')->searchable(),
            TextColumn::make('priority')->badge()->color(fn (string $state): string => match ($state) {
                'Critical' => 'danger', 'High' => 'warning', 'Medium' => 'info', default => 'gray'
            }),
            TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                'Closed' => 'success', 'Resolved' => 'info', 'Escalated' => 'danger', 'In Progress' => 'warning', 'Rejected' => 'gray', default => 'primary'
            }),
            TextColumn::make('due_at')->label('SLA Due')->dateTime('d M, h:i A')->color(fn (CampaignIssue $record): string => $record->is_overdue ? 'danger' : 'gray')->sortable(),
            IconColumn::make('is_confidential')->label('Private')->boolean()->toggleable(),
        ])->filters([
            SelectFilter::make('constituency')->relationship('constituency', 'name')->label('Assembly'), SelectFilter::make('village')->relationship('village', 'name'), SelectFilter::make('category')->options(CampaignIssue::CATEGORIES), SelectFilter::make('priority')->options(CampaignIssue::PRIORITIES), SelectFilter::make('status')->options(CampaignIssue::STATUSES), Filter::make('overdue')->query(fn (Builder $query): Builder => $query->overdue()),
        ])->recordActions([
            Action::make('start')->icon('heroicon-o-play')->color('warning')->visible(fn (CampaignIssue $record): bool => (auth()->user()?->can('campaign_issue.update') ?? false) && in_array($record->status, ['Open', 'Assigned', 'Reopened', 'Waiting'], true))->action(function (CampaignIssue $record): void {
                $record->start(auth()->user());
                Notification::make()->title('Issue work started')->success()->send();
            }),
            Action::make('escalate')->icon('heroicon-o-arrow-trending-up')->color('danger')->schema([Textarea::make('notes')->required()])->visible(fn (CampaignIssue $record): bool => (auth()->user()?->can('campaign_issue.update') ?? false) && ! in_array($record->status, ['Closed', 'Rejected'], true))->action(function (CampaignIssue $record, array $data): void {
                $record->escalate(auth()->user(), $data['notes']);
                Notification::make()->title('Issue escalated')->warning()->send();
            }),
            Action::make('createTask')->label('Create Field Task')->icon('heroicon-o-clipboard-document-check')->visible(fn (CampaignIssue $record): bool => (auth()->user()?->can('campaign_task.create') ?? false) && ! $record->campaign_task_id)->action(function (CampaignIssue $record): void {
                $task = CampaignTask::create(['constituency_id' => $record->constituency_id, 'village_id' => $record->village_id, 'booth_id' => $record->booth_id, 'assigned_to' => $record->assigned_to, 'title' => 'Resolve: '.$record->title, 'category' => 'Issue Resolution', 'priority' => $record->priority, 'description' => $record->issue_code.' — '.$record->description, 'due_at' => $record->due_at, 'requires_proof' => true]);
                $record->forceFill(['campaign_task_id' => $task->id])->save();
                Notification::make()->title('Field task created')->success()->send();
            }),
            Action::make('resolve')->icon('heroicon-o-check-circle')->color('success')->schema([Textarea::make('notes')->label('Resolution')->required(), FileUpload::make('proof')->disk('local')->directory('private/campaign-issues/resolutions')->visibility('private')->multiple()->maxFiles(8)])->visible(fn (CampaignIssue $record): bool => (auth()->user()?->can('campaign_issue.resolve') ?? false) && ! in_array($record->status, ['Resolved', 'Closed', 'Rejected'], true))->action(function (CampaignIssue $record, array $data): void {
                $record->resolve(auth()->user(), $data['notes'], $data['proof'] ?? []);
                Notification::make()->title('Issue resolved; awaiting closure')->success()->send();
            }),
            Action::make('close')->icon('heroicon-o-lock-closed')->color('success')->requiresConfirmation()->visible(fn (CampaignIssue $record): bool => (auth()->user()?->can('campaign_issue.resolve') ?? false) && $record->status === 'Resolved')->action(function (CampaignIssue $record): void {
                $record->close(auth()->user());
                Notification::make()->title('Issue closed')->success()->send();
            }),
            Action::make('reopen')->icon('heroicon-o-arrow-path')->schema([Textarea::make('notes')->required()])->visible(fn (CampaignIssue $record): bool => (auth()->user()?->can('campaign_issue.resolve') ?? false) && $record->status === 'Closed')->action(function (CampaignIssue $record, array $data): void {
                $record->reopen(auth()->user(), $data['notes']);
                Notification::make()->title('Issue reopened')->warning()->send();
            }),
            ViewAction::make(), EditAction::make(),
        ])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
