<?php

namespace App\Filament\Resources\CampaignExpenses;

use App\Filament\Resources\CampaignExpenses\Pages\CreateCampaignExpense;
use App\Filament\Resources\CampaignExpenses\Pages\EditCampaignExpense;
use App\Filament\Resources\CampaignExpenses\Pages\ListCampaignExpenses;
use App\Filament\Resources\Concerns\AuthorizesResourcePermissions;
use App\Models\CampaignBudget;
use App\Models\CampaignEvent;
use App\Models\CampaignExpense;
use App\Models\Constituency;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CampaignExpenseResource extends Resource
{
    use AuthorizesResourcePermissions;

    protected static string $permissionPrefix = 'campaign_finance';

    protected static ?string $model = CampaignExpense::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static ?string $navigationLabel = 'Campaign Expenses';

    protected static ?string $recordTitleAttribute = 'expense_code';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Expense Details')->schema([
                Grid::make(3)->schema([
                    Select::make('constituency_id')->label('Assembly Constituency')->options(fn (): array => Constituency::query()->orderBy('name')->pluck('name', 'id')->all())->default(fn () => auth()->user()?->isAssemblyAdmin() ? auth()->user()?->constituency_id : null)->live()->native(true)->afterStateUpdated(function (Set $set): void {
                        $set('campaign_budget_id', null);
                        $set('campaign_event_id', null);
                    })->required(),
                    Select::make('campaign_budget_id')->label('Budget Head')->options(fn (Get $get): array => CampaignBudget::query()->when($get('constituency_id'), fn ($query, $id) => $query->where('constituency_id', $id))->where('status', 'Active')->orderBy('title')->pluck('title', 'id')->all())->searchable()->native(true),
                    Select::make('campaign_event_id')->label('Related Event')->options(fn (Get $get): array => CampaignEvent::query()->when($get('constituency_id'), fn ($query, $id) => $query->where('constituency_id', $id))->latest('starts_at')->limit(100)->pluck('title', 'id')->all())->searchable()->native(true),
                    DatePicker::make('expense_date')->default(today())->required(),
                    Select::make('category')->options(CampaignBudget::CATEGORIES)->native(true)->required(),
                    TextInput::make('amount')->numeric()->minValue(0.01)->prefix('Rs.')->required(),
                    TextInput::make('description')->required()->maxLength(255)->columnSpan(2),
                    TextInput::make('vendor')->maxLength(255),
                    Select::make('payment_method')->options(CampaignExpense::PAYMENT_METHODS)->default('Cash')->native(true)->required(),
                    Select::make('payment_status')->options(CampaignExpense::PAYMENT_STATUSES)->default('Pending')->native(true)->required(),
                    Select::make('approval_status')->options(fn (): array => auth()->user()?->can('campaign_finance.approve') ? CampaignExpense::APPROVAL_STATUSES : ['Draft' => 'Draft', 'Submitted' => 'Submitted'])->default('Submitted')->native(true)->required(),
                    TextInput::make('invoice_reference')->label('Invoice / Reference No.')->maxLength(255),
                ]),
                FileUpload::make('receipt_path')->label('Receipt / Invoice')->disk('local')->directory('private/campaign-finance/receipts')->visibility('private')->maxSize(10240)->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])->columnSpanFull(),
                Textarea::make('notes')->rows(4)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('expense_date', 'desc')->columns([
            TextColumn::make('expense_code')->label('Expense ID')->badge()->copyable()->searchable(),
            TextColumn::make('expense_date')->date('d M Y')->sortable(),
            TextColumn::make('description')->weight('bold')->searchable()->wrap()->limit(40),
            TextColumn::make('constituency.name')->label('Assembly')->sortable(),
            TextColumn::make('budget.title')->label('Budget Head')->placeholder('Unallocated')->toggleable(),
            TextColumn::make('category')->badge(),
            TextColumn::make('vendor')->placeholder('-')->searchable()->toggleable(),
            TextColumn::make('amount')->money('INR')->sortable(),
            TextColumn::make('approval_status')->badge()->color(fn (string $state): string => match ($state) {
                'Approved' => 'success', 'Rejected' => 'danger', 'Submitted' => 'warning', default => 'gray'
            }),
            TextColumn::make('payment_status')->badge()->color(fn (string $state): string => $state === 'Paid' ? 'success' : ($state === 'Cancelled' ? 'danger' : 'warning')),
        ])->filters([
            SelectFilter::make('constituency')->relationship('constituency', 'name')->label('Assembly'),
            SelectFilter::make('category')->options(CampaignBudget::CATEGORIES),
            SelectFilter::make('approval_status')->options(CampaignExpense::APPROVAL_STATUSES),
            SelectFilter::make('payment_status')->options(CampaignExpense::PAYMENT_STATUSES),
        ])->recordActions([
            Action::make('approve')->icon('heroicon-o-check-circle')->color('success')->requiresConfirmation()->visible(fn (CampaignExpense $record): bool => (auth()->user()?->can('campaign_finance.approve') ?? false) && $record->approval_status !== 'Approved')->action(function (CampaignExpense $record): void {
                $record->approve(auth()->user());
                Notification::make()->title('Expense approved')->success()->send();
            }),
            Action::make('reject')->icon('heroicon-o-x-circle')->color('danger')->schema([Textarea::make('notes')->label('Rejection reason')->required()])->visible(fn (CampaignExpense $record): bool => (auth()->user()?->can('campaign_finance.approve') ?? false) && ! in_array($record->approval_status, ['Approved', 'Rejected'], true))->action(function (CampaignExpense $record, array $data): void {
                $record->reject(auth()->user(), $data['notes']);
                Notification::make()->title('Expense rejected')->danger()->send();
            }),
            EditAction::make(),
        ])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCampaignExpenses::route('/'),
            'create' => CreateCampaignExpense::route('/create'),
            'edit' => EditCampaignExpense::route('/{record}/edit'),
        ];
    }
}
