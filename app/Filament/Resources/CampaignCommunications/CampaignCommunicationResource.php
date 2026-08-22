<?php

namespace App\Filament\Resources\CampaignCommunications;

use App\Filament\Resources\CampaignCommunications\Pages\CreateCampaignCommunication;
use App\Filament\Resources\CampaignCommunications\Pages\EditCampaignCommunication;
use App\Filament\Resources\CampaignCommunications\Pages\ListCampaignCommunications;
use App\Filament\Resources\Concerns\AuthorizesResourcePermissions;
use App\Models\CampaignCommunication;
use App\Models\Constituency;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CampaignCommunicationResource extends Resource
{
    use AuthorizesResourcePermissions;

    protected static string $permissionPrefix = 'campaign_communication';

    protected static ?string $model = CampaignCommunication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static ?string $navigationLabel = 'Content Register';

    protected static ?string $recordTitleAttribute = 'title';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Campaign Message')->schema([
                Grid::make(3)->schema([
                    Select::make('constituency_id')->label('Assembly Constituency')->options(function (): array {
                        $user = auth()->user();

                        return Constituency::query()
                            ->when($user?->constituency_id && ! $user->isSuperAdmin() && ! $user->can('campaign_communication.approve'), fn (Builder $query): Builder => $query->whereKey($user->constituency_id))
                            ->orderBy('name')->pluck('name', 'id')->all();
                    })->default(fn () => auth()->user()?->constituency_id)->live()->native(true)->afterStateUpdated(fn (Set $set) => $set('owner_id', null))->required(),
                    TextInput::make('title')->required()->maxLength(255)->columnSpan(2),
                    Select::make('channel')->options(CampaignCommunication::CHANNELS)->native(true)->required(),
                    Select::make('content_type')->options(CampaignCommunication::CONTENT_TYPES)->default('Post')->native(true)->required(),
                    Select::make('audience')->options(CampaignCommunication::AUDIENCES)->default('General Voters')->native(true)->required(),
                    Select::make('language')->options(CampaignCommunication::LANGUAGES)->default('Gujarati')->native(true)->required(),
                    Select::make('priority')->options(CampaignCommunication::PRIORITIES)->default('Normal')->native(true)->required(),
                    Select::make('status')->options(fn (): array => auth()->user()?->can('campaign_communication.approve') ? CampaignCommunication::STATUSES : array_intersect_key(CampaignCommunication::STATUSES, array_flip(['Draft', 'Pending Approval'])))->default('Draft')->native(true)->required(),
                    Select::make('owner_id')->label('Content Owner')->options(fn (Get $get): array => User::query()->where('is_active', true)->when($get('constituency_id'), fn (Builder $query, $id): Builder => $query->where('constituency_id', $id))->orderBy('name')->get()->mapWithKeys(fn (User $user): array => [$user->id => $user->display_name])->all())->searchable()->native(true),
                    DateTimePicker::make('scheduled_at')->label('Scheduled Publish Date & Time')->seconds(false),
                    TextInput::make('planned_reach')->numeric()->minValue(0)->default(0),
                ]),
                Textarea::make('message')->label('Message / Caption / Content Draft')->rows(8)->columnSpanFull(),
                Grid::make(2)->schema([
                    TextInput::make('call_to_action')->maxLength(255),
                    TextInput::make('estimated_cost')->numeric()->minValue(0)->prefix('Rs.'),
                ]),
                FileUpload::make('asset_path')->label('Creative / Video / Document')->disk('local')->directory('private/campaign-communications')->visibility('private')->maxSize(51200)->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'video/mp4', 'application/pdf'])->columnSpanFull(),
            ]),
            Section::make('Publishing & Performance')->visible(fn (?CampaignCommunication $record): bool => $record !== null)->schema([
                Grid::make(4)->schema([
                    DateTimePicker::make('published_at')->seconds(false),
                    TextInput::make('published_url')->url()->maxLength(255),
                    TextInput::make('actual_reach')->numeric()->minValue(0),
                    TextInput::make('engagement_count')->numeric()->minValue(0),
                    TextInput::make('actual_cost')->numeric()->minValue(0)->prefix('Rs.'),
                ]),
                Textarea::make('approval_notes')->rows(3)->columnSpanFull(),
                Textarea::make('notes')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('scheduled_at')->columns([
            TextColumn::make('communication_code')->label('Content ID')->badge()->copyable()->searchable(),
            TextColumn::make('title')->weight('bold')->searchable()->wrap()->limit(45),
            TextColumn::make('channel')->badge()->sortable(),
            TextColumn::make('content_type')->label('Type')->badge()->toggleable(),
            TextColumn::make('constituency.name')->label('Assembly')->sortable(),
            TextColumn::make('audience')->toggleable(),
            TextColumn::make('owner.name')->label('Owner')->placeholder('Unassigned')->searchable(),
            TextColumn::make('scheduled_at')->label('Schedule')->dateTime('d M Y, h:i A')->placeholder('Not scheduled')->sortable(),
            TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                'Published' => 'success', 'Rejected', 'Cancelled' => 'danger', 'Pending Approval' => 'warning', 'Scheduled' => 'info', default => 'primary'
            }),
            TextColumn::make('actual_reach')->label('Reach')->numeric()->toggleable(),
            TextColumn::make('engagement_rate')->label('Engagement')->suffix('%')->toggleable(),
            IconColumn::make('is_overdue')->label('Overdue')->boolean()->trueColor('danger')->toggleable(),
        ])->filters([
            SelectFilter::make('constituency')->relationship('constituency', 'name')->label('Assembly'),
            SelectFilter::make('channel')->options(CampaignCommunication::CHANNELS),
            SelectFilter::make('status')->options(CampaignCommunication::STATUSES),
            SelectFilter::make('audience')->options(CampaignCommunication::AUDIENCES),
            Filter::make('overdue')->query(fn (Builder $query): Builder => $query->whereNotNull('scheduled_at')->where('scheduled_at', '<', now())->whereNotIn('status', ['Published', 'Cancelled', 'Rejected'])),
        ])->recordActions([
            Action::make('submit')->label('Submit for Approval')->icon('heroicon-o-paper-airplane')->color('warning')->requiresConfirmation()->visible(fn (CampaignCommunication $record): bool => (auth()->user()?->can('campaign_communication.update') ?? false) && in_array($record->status, ['Draft', 'Rejected'], true))->action(function (CampaignCommunication $record): void {
                $record->update(['status' => 'Pending Approval']);
                Notification::make()->title('Content submitted for approval')->success()->send();
            }),
            Action::make('approve')->icon('heroicon-o-check-circle')->color('success')->schema([Textarea::make('notes')->label('Approval notes')])->visible(fn (CampaignCommunication $record): bool => (auth()->user()?->can('campaign_communication.approve') ?? false) && in_array($record->status, ['Draft', 'Pending Approval', 'Rejected'], true))->action(function (CampaignCommunication $record, array $data): void {
                $record->approve(auth()->user(), $data['notes'] ?? null);
                Notification::make()->title('Content approved')->success()->send();
            }),
            Action::make('reject')->icon('heroicon-o-x-circle')->color('danger')->schema([Textarea::make('notes')->label('Rejection reason')->required()])->visible(fn (CampaignCommunication $record): bool => (auth()->user()?->can('campaign_communication.approve') ?? false) && in_array($record->status, ['Draft', 'Pending Approval'], true))->action(function (CampaignCommunication $record, array $data): void {
                $record->reject(auth()->user(), $data['notes']);
                Notification::make()->title('Content rejected')->danger()->send();
            }),
            Action::make('publish')->label('Mark Published')->icon('heroicon-o-signal')->color('success')->schema([
                TextInput::make('url')->label('Published URL')->url(),
                TextInput::make('reach')->numeric()->minValue(0)->default(0),
                TextInput::make('engagement')->numeric()->minValue(0)->default(0),
            ])->visible(fn (CampaignCommunication $record): bool => (auth()->user()?->can('campaign_communication.publish') ?? false) && in_array($record->status, ['Approved', 'Scheduled'], true))->action(function (CampaignCommunication $record, array $data): void {
                $record->markPublished($data['url'] ?? null, (int) ($data['reach'] ?? 0), (int) ($data['engagement'] ?? 0));
                Notification::make()->title('Content marked as published')->success()->send();
            }),
            EditAction::make(),
        ])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user?->constituency_id && ! $user->isSuperAdmin() && ! $user->can('campaign_communication.approve')) {
            $query->where('constituency_id', $user->constituency_id);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCampaignCommunications::route('/'),
            'create' => CreateCampaignCommunication::route('/create'),
            'edit' => EditCampaignCommunication::route('/{record}/edit'),
        ];
    }
}
