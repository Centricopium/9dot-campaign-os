<?php

namespace App\Filament\Resources\CampaignEvents\Tables;

use App\Models\CampaignEvent;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CampaignEventsTable
{
    public static function configure(Table $table): Table
    {
        return $table->defaultSort('starts_at')->columns([TextColumn::make('event_code')->label('ID')->badge()->searchable(), TextColumn::make('title')->weight('bold')->searchable()->wrap(), TextColumn::make('event_type')->badge(), TextColumn::make('constituency.name')->label('Assembly'), TextColumn::make('candidate.name')->placeholder('—'), TextColumn::make('starts_at')->dateTime('d M Y, h:i A')->sortable(), TextColumn::make('venue')->searchable(), TextColumn::make('coordinator.name')->placeholder('Unassigned'), TextColumn::make('expected_attendance')->label('Expected'), TextColumn::make('actual_attendance')->label('Actual'), TextColumn::make('status')->badge()])->filters([SelectFilter::make('constituency')->relationship('constituency', 'name')->label('Assembly'), SelectFilter::make('event_type')->options(CampaignEvent::TYPES), SelectFilter::make('status')->options(CampaignEvent::STATUSES), SelectFilter::make('permission_status')->options(CampaignEvent::PERMISSIONS)])->recordActions([Action::make('complete')->color('success')->icon('heroicon-o-check-circle')->schema([TextInput::make('attendance')->numeric()->minValue(0)->required(), TextInput::make('expense')->numeric()->minValue(0)->prefix('₹'), Textarea::make('outcome')->required()])->visible(fn (CampaignEvent $r) => (auth()->user()?->can('campaign_event.update') ?? false) && ! in_array($r->status, ['Completed', 'Cancelled'], true))->action(function (CampaignEvent $r, array $d): void {
            $r->update(['status' => 'Completed', 'actual_attendance' => $d['attendance'], 'actual_expense' => $d['expense'] ?? 0, 'outcome_notes' => $d['outcome']]);
            Notification::make()->title('Event completed')->success()->send();
        }), EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
