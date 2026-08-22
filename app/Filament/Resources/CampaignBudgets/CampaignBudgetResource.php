<?php

namespace App\Filament\Resources\CampaignBudgets;

use App\Filament\Resources\CampaignBudgets\Pages\CreateCampaignBudget;
use App\Filament\Resources\CampaignBudgets\Pages\EditCampaignBudget;
use App\Filament\Resources\CampaignBudgets\Pages\ListCampaignBudgets;
use App\Filament\Resources\Concerns\AuthorizesResourcePermissions;
use App\Models\CampaignBudget;
use App\Models\Constituency;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CampaignBudgetResource extends Resource
{
    use AuthorizesResourcePermissions;

    protected static string $permissionPrefix = 'campaign_finance';

    protected static ?string $model = CampaignBudget::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Campaign Budgets';

    protected static ?string $recordTitleAttribute = 'title';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Budget Allocation')->schema([
                Grid::make(3)->schema([
                    Select::make('constituency_id')->label('Assembly Constituency')->options(fn (): array => Constituency::query()->orderBy('name')->pluck('name', 'id')->all())->default(fn () => auth()->user()?->isAssemblyAdmin() ? auth()->user()?->constituency_id : null)->native(true)->required(),
                    TextInput::make('title')->required()->maxLength(255)->columnSpan(2),
                    Select::make('category')->options(CampaignBudget::CATEGORIES)->native(true)->required(),
                    TextInput::make('allocated_amount')->label('Allocated Amount')->numeric()->minValue(0)->prefix('Rs.')->required(),
                    Select::make('status')->options(CampaignBudget::STATUSES)->default('Active')->native(true)->required(),
                    DatePicker::make('starts_on')->label('Budget Starts'),
                    DatePicker::make('ends_on')->label('Budget Ends')->afterOrEqual('starts_on'),
                ]),
                Textarea::make('notes')->rows(4)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([
            TextColumn::make('title')->weight('bold')->searchable()->wrap(),
            TextColumn::make('constituency.name')->label('Assembly')->sortable(),
            TextColumn::make('category')->badge()->sortable(),
            TextColumn::make('allocated_amount')->label('Allocated')->money('INR')->sortable(),
            TextColumn::make('approved_spent')->label('Approved Spend')->money('INR'),
            TextColumn::make('available_amount')->label('Available')->money('INR'),
            TextColumn::make('utilization_percent')->label('Used')->suffix('%')->badge()->color(fn (int $state): string => $state >= 90 ? 'danger' : ($state >= 70 ? 'warning' : 'success')),
            TextColumn::make('status')->badge(),
        ])->filters([
            SelectFilter::make('constituency')->relationship('constituency', 'name')->label('Assembly'),
            SelectFilter::make('category')->options(CampaignBudget::CATEGORIES),
            SelectFilter::make('status')->options(CampaignBudget::STATUSES),
        ])->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withSum([
            'expenses as expenses_sum_amount' => fn (Builder $query): Builder => $query->where('approval_status', 'Approved'),
        ], 'amount');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCampaignBudgets::route('/'),
            'create' => CreateCampaignBudget::route('/create'),
            'edit' => EditCampaignBudget::route('/{record}/edit'),
        ];
    }
}
