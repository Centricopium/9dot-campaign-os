<?php

namespace App\Filament\Resources\EmployeeAttendances;

use App\Filament\Resources\Concerns\AuthorizesResourcePermissions;
use App\Filament\Resources\EmployeeAttendances\Pages\CreateEmployeeAttendance;
use App\Filament\Resources\EmployeeAttendances\Pages\EditEmployeeAttendance;
use App\Filament\Resources\EmployeeAttendances\Pages\ListEmployeeAttendances;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class EmployeeAttendanceResource extends Resource
{
    use AuthorizesResourcePermissions;

    protected static string $permissionPrefix = 'hr_attendance';

    protected static ?string $model = EmployeeAttendance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|UnitEnum|null $navigationGroup = 'HR & Payroll';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Attendance';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Section::make('Daily Attendance')->schema([
            Grid::make(3)->schema([
                Select::make('employee_id')->options(fn (): array => Employee::query()->whereIn('status', ['Active', 'On Leave'])->orderBy('name')->pluck('name', 'id')->all())->searchable()->native(false)->required(),
                DatePicker::make('attendance_date')->default(today())->required(),
                Select::make('status')->options(EmployeeAttendance::STATUSES)->default('Present')->native(false)->required(),
                TimePicker::make('check_in')->seconds(false),
                TimePicker::make('check_out')->seconds(false),
                TextInput::make('overtime_hours')->numeric()->minValue(0)->maxValue(24)->suffix('hours')->default(0),
            ]),
            Textarea::make('remarks')->rows(3)->columnSpanFull(),
        ])]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('attendance_date', 'desc')->columns([
            TextColumn::make('attendance_date')->date('d M Y')->sortable(),
            TextColumn::make('employee.employee_code')->label('Employee ID')->badge()->searchable(),
            TextColumn::make('employee.name')->weight('bold')->searchable(),
            TextColumn::make('employee.constituency.name')->label('Assembly'),
            TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                'Present', 'Holiday', 'Weekly Off' => 'success', 'Half Day', 'Paid Leave' => 'warning', default => 'danger'
            }),
            TextColumn::make('check_in')->time('h:i A')->placeholder('-'),
            TextColumn::make('check_out')->time('h:i A')->placeholder('-'),
            TextColumn::make('overtime_hours')->suffix(' h')->toggleable(),
            TextColumn::make('marker.name')->label('Marked By')->placeholder('System')->toggleable(),
        ])->filters([
            SelectFilter::make('employee')->relationship('employee', 'name')->searchable(),
            SelectFilter::make('status')->options(EmployeeAttendance::STATUSES),
            Filter::make('today')->label('Today')->query(fn (Builder $query): Builder => $query->whereDate('attendance_date', today())),
        ])->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListEmployeeAttendances::route('/'), 'create' => CreateEmployeeAttendance::route('/create'), 'edit' => EditEmployeeAttendance::route('/{record}/edit')];
    }
}
