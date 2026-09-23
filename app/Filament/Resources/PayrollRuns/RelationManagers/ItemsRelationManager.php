<?php

namespace App\Filament\Resources\PayrollRuns\RelationManagers;

use App\Models\PayrollItem;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Employee Payouts';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->schema([
                TextInput::make('basic_pay')->numeric()->prefix('Rs.')->disabled()->dehydrated(),
                TextInput::make('allowances')->numeric()->minValue(0)->prefix('Rs.'),
                TextInput::make('incentives')->numeric()->minValue(0)->prefix('Rs.'),
                TextInput::make('overtime_pay')->numeric()->minValue(0)->prefix('Rs.'),
                TextInput::make('reimbursements')->numeric()->minValue(0)->prefix('Rs.'),
                TextInput::make('deductions')->numeric()->minValue(0)->prefix('Rs.'),
                TextInput::make('advances')->numeric()->minValue(0)->prefix('Rs.'),
                Select::make('payment_status')->options(PayrollItem::PAYMENT_STATUSES)->native(false),
                Select::make('payment_method')->options(PayrollItem::PAYMENT_METHODS)->native(false),
                TextInput::make('payment_reference'),
                DatePicker::make('payment_date'),
            ]),
            Textarea::make('remarks')->rows(3)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->defaultSort('employee_name_snapshot')->columns([
            TextColumn::make('employee_code_snapshot')->label('Employee ID')->badge()->searchable(),
            TextColumn::make('employee_name_snapshot')->label('Employee')->weight('bold')->searchable(),
            TextColumn::make('designation_snapshot')->label('Designation')->placeholder('-')->toggleable(),
            TextColumn::make('present_days')->label('Present')->numeric(decimalPlaces: 1),
            TextColumn::make('paid_leave_days')->label('Paid Leave')->numeric(decimalPlaces: 1)->toggleable(),
            TextColumn::make('unpaid_leave_days')->label('Unpaid Leave')->numeric(decimalPlaces: 1)->toggleable(),
            TextColumn::make('gross_pay')->money('INR'),
            TextColumn::make('deductions')->money('INR')->toggleable(),
            TextColumn::make('advances')->money('INR')->toggleable(),
            TextColumn::make('net_pay')->label('Net Payout')->money('INR')->weight('bold'),
            TextColumn::make('payment_status')->badge()->color(fn (string $state): string => $state === 'Paid' ? 'success' : ($state === 'Failed' ? 'danger' : 'warning')),
        ])->filters([SelectFilter::make('payment_status')->options(PayrollItem::PAYMENT_STATUSES)])->recordActions([
            Action::make('payslip')->label('Payslip')->icon('heroicon-o-document-arrow-down')->url(fn (PayrollItem $record): string => route('hr.payroll.payslip', $record))->openUrlInNewTab()->visible(fn (): bool => auth()->user()?->can('payroll.export') ?? false),
            Action::make('paid')->label('Mark Paid')->icon('heroicon-o-check')->color('success')->schema([Select::make('payment_method')->options(PayrollItem::PAYMENT_METHODS)->native(false)->required(), TextInput::make('payment_reference')])->visible(fn (PayrollItem $record): bool => (auth()->user()?->can('payroll.pay') ?? false) && $record->payment_status !== 'Paid')->action(function (PayrollItem $record, array $data): void {
                $record->forceFill(['payment_status' => 'Paid', 'payment_method' => $data['payment_method'], 'payment_reference' => $data['payment_reference'] ?? null, 'payment_date' => today()])->save();
                Notification::make()->title('Employee payout marked paid')->success()->send();
            }),
            EditAction::make()->visible(fn (): bool => (auth()->user()?->can('payroll.process') ?? false) && in_array($this->getOwnerRecord()->status, ['Draft', 'Processed'], true)),
        ]);
    }
}
