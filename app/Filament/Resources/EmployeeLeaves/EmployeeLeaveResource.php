<?php

namespace App\Filament\Resources\EmployeeLeaves;

use App\Filament\Resources\Concerns\AuthorizesResourcePermissions;
use App\Filament\Resources\EmployeeLeaves\Pages\CreateEmployeeLeave;
use App\Filament\Resources\EmployeeLeaves\Pages\EditEmployeeLeave;
use App\Filament\Resources\EmployeeLeaves\Pages\ListEmployeeLeaves;
use App\Models\Employee;
use App\Models\EmployeeLeave;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class EmployeeLeaveResource extends Resource
{
    use AuthorizesResourcePermissions;

    protected static string $permissionPrefix = 'hr_leave';

    protected static ?string $model = EmployeeLeave::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDateRange;

    protected static string|UnitEnum|null $navigationGroup = 'HR & Payroll';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Leave Requests';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Section::make('Leave Request')->schema([
            Grid::make(3)->schema([
                Select::make('employee_id')->options(fn (): array => Employee::query()->orderBy('name')->pluck('name', 'id')->all())->searchable()->native(false)->required(),
                Select::make('leave_type')->options(EmployeeLeave::TYPES)->default('Casual')->native(false)->required(),
                Toggle::make('is_paid')->label('Paid Leave')->default(false),
                DatePicker::make('starts_on')->default(today())->required(),
                DatePicker::make('ends_on')->default(today())->afterOrEqual('starts_on')->required(),
                Select::make('status')->options(EmployeeLeave::STATUSES)->default('Pending')->native(false)->disabled(fn (): bool => ! (auth()->user()?->can('hr_leave.approve') ?? false))->dehydrated(),
            ]),
            Textarea::make('reason')->rows(3)->columnSpanFull(),
            Textarea::make('approval_notes')->rows(3)->columnSpanFull()->visible(fn (): bool => auth()->user()?->can('hr_leave.approve') ?? false),
        ])]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('starts_on', 'desc')->columns([
            TextColumn::make('employee.employee_code')->label('Employee ID')->badge()->searchable(),
            TextColumn::make('employee.name')->weight('bold')->searchable(),
            TextColumn::make('leave_type')->badge(),
            TextColumn::make('starts_on')->date('d M Y')->sortable(),
            TextColumn::make('ends_on')->date('d M Y'),
            TextColumn::make('days')->numeric(decimalPlaces: 1),
            IconColumn::make('is_paid')->label('Paid')->boolean(),
            TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                'Approved' => 'success', 'Rejected', 'Cancelled' => 'danger', default => 'warning'
            }),
        ])->filters([SelectFilter::make('status')->options(EmployeeLeave::STATUSES), SelectFilter::make('leave_type')->options(EmployeeLeave::TYPES)])->recordActions([
            Action::make('approve')->icon('heroicon-o-check')->color('success')->requiresConfirmation()->visible(fn (EmployeeLeave $record): bool => (auth()->user()?->can('hr_leave.approve') ?? false) && $record->status === 'Pending')->action(function (EmployeeLeave $record): void {
                $record->forceFill(['status' => 'Approved', 'approved_by' => auth()->id(), 'approved_at' => now()])->save();
                Notification::make()->title('Leave approved')->success()->send();
            }),
            Action::make('reject')->icon('heroicon-o-x-mark')->color('danger')->schema([Textarea::make('approval_notes')->required()])->visible(fn (EmployeeLeave $record): bool => (auth()->user()?->can('hr_leave.approve') ?? false) && $record->status === 'Pending')->action(function (EmployeeLeave $record, array $data): void {
                $record->forceFill(['status' => 'Rejected', 'approved_by' => auth()->id(), 'approved_at' => now(), 'approval_notes' => $data['approval_notes']])->save();
                Notification::make()->title('Leave rejected')->danger()->send();
            }),
            EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListEmployeeLeaves::route('/'), 'create' => CreateEmployeeLeave::route('/create'), 'edit' => EditEmployeeLeave::route('/{record}/edit')];
    }
}
