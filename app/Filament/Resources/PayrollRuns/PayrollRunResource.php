<?php

namespace App\Filament\Resources\PayrollRuns;

use App\Filament\Resources\Concerns\AuthorizesResourcePermissions;
use App\Filament\Resources\PayrollRuns\Pages\CreatePayrollRun;
use App\Filament\Resources\PayrollRuns\Pages\EditPayrollRun;
use App\Filament\Resources\PayrollRuns\Pages\ListPayrollRuns;
use App\Filament\Resources\PayrollRuns\RelationManagers\ItemsRelationManager;
use App\Models\Constituency;
use App\Models\PayrollRun;
use App\Services\Payroll\PayrollCalculationService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PayrollRunResource extends Resource
{
    use AuthorizesResourcePermissions;

    protected static string $permissionPrefix = 'payroll';

    protected static ?string $model = PayrollRun::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'HR & Payroll';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Payroll Runs';

    protected static ?string $recordTitleAttribute = 'run_code';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Section::make('Payroll Period')->schema([
            Grid::make(3)->schema([
                Select::make('constituency_id')->label('Assembly Constituency')->options(fn (): array => Constituency::query()->orderBy('name')->pluck('name', 'id')->all())->default(fn () => auth()->user()?->isConstituencyScoped() ? auth()->user()?->constituency_id : null)->native(false)->required()->disabledOn('edit')->dehydrated(),
                Select::make('period_month')->options(collect(range(1, 12))->mapWithKeys(fn (int $month): array => [$month => now()->month($month)->format('F')])->all())->default(now()->month)->native(false)->required()->disabledOn('edit')->dehydrated(),
                Select::make('period_year')->options(collect(range(now()->year - 2, now()->year + 1))->mapWithKeys(fn (int $year): array => [$year => $year])->all())->default(now()->year)->native(false)->required()->disabledOn('edit')->dehydrated(),
                Select::make('status')->options(PayrollRun::STATUSES)->default('Draft')->native(false)->disabled()->dehydrated(),
            ]),
            Textarea::make('notes')->rows(4)->columnSpanFull(),
        ])]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('period_start', 'desc')->columns([
            TextColumn::make('run_code')->label('Payroll ID')->badge()->copyable()->searchable(),
            TextColumn::make('period_label')->label('Period')->weight('bold')->sortable(['period_year', 'period_month']),
            TextColumn::make('constituency.name')->label('Assembly')->sortable(),
            TextColumn::make('working_days')->label('Working Days'),
            TextColumn::make('items_count')->label('Employees')->numeric(),
            TextColumn::make('items_sum_gross_pay')->label('Gross Payroll')->money('INR'),
            TextColumn::make('items_sum_net_pay')->label('Net Payout')->money('INR')->weight('bold'),
            TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                'Approved' => 'info', 'Paid' => 'success', 'Processed' => 'warning', 'Cancelled' => 'danger', default => 'gray'
            }),
        ])->filters([SelectFilter::make('constituency')->relationship('constituency', 'name')->label('Assembly'), SelectFilter::make('status')->options(PayrollRun::STATUSES)])->recordActions([
            Action::make('process')->label(fn (PayrollRun $record): string => $record->status === 'Draft' ? 'Calculate' : 'Recalculate')->icon('heroicon-o-calculator')->color('warning')->requiresConfirmation()->visible(fn (PayrollRun $record): bool => (auth()->user()?->can('payroll.process') ?? false) && in_array($record->status, ['Draft', 'Processed'], true))->action(function (PayrollRun $record): void {
                app(PayrollCalculationService::class)->process($record);
                Notification::make()->title('Payroll calculated')->body('Attendance, leave and salary structure have been applied.')->success()->send();
            }),
            Action::make('approve')->icon('heroicon-o-check-badge')->color('success')->requiresConfirmation()->visible(fn (PayrollRun $record): bool => (auth()->user()?->can('payroll.approve') ?? false) && $record->status === 'Processed')->action(function (PayrollRun $record): void {
                abort_if($record->items()->doesntExist(), 422, 'Generate payroll items before approval.');
                $record->forceFill(['status' => 'Approved', 'approved_by' => auth()->id(), 'approved_at' => now()])->save();
                Notification::make()->title('Payroll approved')->success()->send();
            }),
            Action::make('markPaid')->label('Mark Paid')->icon('heroicon-o-banknotes')->color('success')->requiresConfirmation()->visible(fn (PayrollRun $record): bool => (auth()->user()?->can('payroll.pay') ?? false) && $record->status === 'Approved')->action(function (PayrollRun $record): void {
                $record->items()->where('payment_status', '!=', 'Paid')->update(['payment_status' => 'Paid', 'payment_date' => today(), 'updated_at' => now()]);
                $record->forceFill(['status' => 'Paid', 'paid_at' => now()])->save();
                Notification::make()->title('Payroll marked as paid')->success()->send();
            }),
            Action::make('register')->label('PDF Register')->icon('heroicon-o-document-arrow-down')->url(fn (PayrollRun $record): string => route('hr.payroll.register', $record))->openUrlInNewTab()->visible(fn (PayrollRun $record): bool => (auth()->user()?->can('payroll.export') ?? false) && $record->items()->exists()),
            EditAction::make(),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('items')->withSum('items', 'gross_pay')->withSum('items', 'net_pay');
    }

    public static function getRelations(): array
    {
        return [ItemsRelationManager::class];
    }

    public static function getPages(): array
    {
        return ['index' => ListPayrollRuns::route('/'), 'create' => CreatePayrollRun::route('/create'), 'edit' => EditPayrollRun::route('/{record}/edit')];
    }
}
