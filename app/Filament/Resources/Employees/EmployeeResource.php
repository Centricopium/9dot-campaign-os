<?php

namespace App\Filament\Resources\Employees;

use App\Filament\Resources\Concerns\AuthorizesResourcePermissions;
use App\Filament\Resources\Employees\Pages\CreateEmployee;
use App\Filament\Resources\Employees\Pages\EditEmployee;
use App\Filament\Resources\Employees\Pages\ListEmployees;
use App\Models\Constituency;
use App\Models\Employee;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class EmployeeResource extends Resource
{
    use AuthorizesResourcePermissions;

    protected static string $permissionPrefix = 'hr_employee';

    protected static ?string $model = Employee::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static string|UnitEnum|null $navigationGroup = 'HR & Payroll';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Employees';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Employee Profile')->schema([
                Grid::make(3)->schema([
                    Select::make('constituency_id')->label('Assembly Constituency')->options(fn (): array => Constituency::query()->orderBy('name')->pluck('name', 'id')->all())->default(fn () => auth()->user()?->isConstituencyScoped() ? auth()->user()?->constituency_id : null)->native(false)->required(),
                    TextInput::make('employee_code')->maxLength(40)->unique(ignoreRecord: true)->placeholder('Auto-generated if blank'),
                    Select::make('user_id')->label('Linked Login User')->options(fn (): array => User::query()
                        ->where('is_active', true)
                        ->when(auth()->user()?->isConstituencyScoped(), fn ($query) => $query->where('constituency_id', auth()->user()?->constituency_id))
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())->searchable()->native(false)->unique(ignoreRecord: true),
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('mobile')->tel()->maxLength(20),
                    TextInput::make('email')->email()->maxLength(255),
                    DatePicker::make('date_of_birth'),
                    Select::make('gender')->options(['Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other'])->native(false),
                    TextInput::make('department')->maxLength(255),
                    TextInput::make('designation')->maxLength(255),
                    Select::make('employment_type')->options(Employee::EMPLOYMENT_TYPES)->default('Full Time')->native(false)->required(),
                    Select::make('status')->options(Employee::STATUSES)->default('Active')->native(false)->required(),
                    DatePicker::make('joining_date')->default(today())->required(),
                    DatePicker::make('leaving_date')->afterOrEqual('joining_date'),
                ]),
                Textarea::make('address')->rows(3)->columnSpanFull(),
                FileUpload::make('photo_path')->label('Profile Photo')->disk('local')->directory('private/hr/photos')->image()->maxSize(5120),
            ]),
            Section::make('Salary Structure')->schema([
                Grid::make(3)->schema([
                    Select::make('salary_type')->options(Employee::SALARY_TYPES)->default('Monthly')->live()->native(false)->required(),
                    TextInput::make('base_salary')->label(fn (Get $get): string => $get('salary_type') === 'Contract' ? 'Contract Amount' : 'Monthly Basic Salary')->numeric()->minValue(0)->prefix('Rs.')->visible(fn (Get $get): bool => in_array($get('salary_type'), ['Monthly', 'Contract'], true))->required(fn (Get $get): bool => in_array($get('salary_type'), ['Monthly', 'Contract'], true)),
                    TextInput::make('daily_rate')->numeric()->minValue(0)->prefix('Rs.')->visible(fn (Get $get): bool => $get('salary_type') === 'Daily')->required(fn (Get $get): bool => $get('salary_type') === 'Daily'),
                    TextInput::make('hourly_rate')->numeric()->minValue(0)->prefix('Rs.')->visible(fn (Get $get): bool => $get('salary_type') === 'Hourly')->required(fn (Get $get): bool => $get('salary_type') === 'Hourly'),
                    TextInput::make('default_allowance')->label('Default Monthly Allowance')->numeric()->minValue(0)->prefix('Rs.')->default(0),
                    TextInput::make('default_deduction')->label('Default Monthly Deduction')->numeric()->minValue(0)->prefix('Rs.')->default(0),
                ]),
            ]),
            Section::make('Payment & Identity (Encrypted)')->schema([
                Grid::make(3)->schema([
                    TextInput::make('account_holder_name'),
                    TextInput::make('bank_name'),
                    TextInput::make('bank_account_number')->autocomplete(false),
                    TextInput::make('ifsc_code')->maxLength(20),
                    TextInput::make('upi_id'),
                    Select::make('identity_type')->options(['Aadhaar' => 'Aadhaar', 'PAN' => 'PAN', 'Voter ID' => 'Voter ID', 'Driving Licence' => 'Driving Licence', 'Other' => 'Other'])->native(false),
                    TextInput::make('identity_number')->autocomplete(false),
                    FileUpload::make('identity_document_path')->label('Identity Document')->disk('local')->directory('private/hr/identity')->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])->maxSize(10240),
                ]),
                Textarea::make('notes')->rows(3)->columnSpanFull(),
            ])->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('name')->columns([
            TextColumn::make('employee_code')->label('Employee ID')->badge()->searchable()->copyable(),
            TextColumn::make('name')->weight('bold')->searchable()->sortable(),
            TextColumn::make('constituency.name')->label('Assembly')->sortable(),
            TextColumn::make('department')->placeholder('-')->searchable()->toggleable(),
            TextColumn::make('designation')->placeholder('-')->searchable(),
            TextColumn::make('mobile')->placeholder('-')->searchable()->toggleable(),
            TextColumn::make('employment_type')->badge(),
            TextColumn::make('salary_type')->badge(),
            TextColumn::make('base_salary')->label('Base / Contract')->money('INR')->toggleable(),
            TextColumn::make('joining_date')->date('d M Y')->sortable()->toggleable(),
            TextColumn::make('status')->badge()->color(fn (string $state): string => $state === 'Active' ? 'success' : ($state === 'On Leave' ? 'warning' : 'gray')),
        ])->filters([
            SelectFilter::make('constituency')->relationship('constituency', 'name')->label('Assembly'),
            SelectFilter::make('department')->options(fn (): array => Employee::query()->whereNotNull('department')->distinct()->orderBy('department')->pluck('department', 'department')->all()),
            SelectFilter::make('employment_type')->options(Employee::EMPLOYMENT_TYPES),
            SelectFilter::make('salary_type')->options(Employee::SALARY_TYPES),
            SelectFilter::make('status')->options(Employee::STATUSES),
        ])->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListEmployees::route('/'), 'create' => CreateEmployee::route('/create'), 'edit' => EditEmployee::route('/{record}/edit')];
    }
}
