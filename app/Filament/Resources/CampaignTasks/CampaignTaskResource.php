<?php

namespace App\Filament\Resources\CampaignTasks;

use App\Filament\Resources\CampaignTasks\Pages\CreateCampaignTask;
use App\Filament\Resources\CampaignTasks\Pages\EditCampaignTask;
use App\Filament\Resources\CampaignTasks\Pages\ListCampaignTasks;
use App\Filament\Resources\CampaignTasks\Pages\ViewCampaignTask;
use App\Filament\Resources\CampaignTasks\RelationManagers\UpdatesRelationManager;
use App\Filament\Resources\CampaignTasks\Schemas\CampaignTaskForm;
use App\Filament\Resources\CampaignTasks\Schemas\CampaignTaskInfolist;
use App\Filament\Resources\CampaignTasks\Tables\CampaignTasksTable;
use App\Filament\Resources\Concerns\AuthorizesResourcePermissions;
use App\Models\CampaignTask;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CampaignTaskResource extends Resource
{
    use AuthorizesResourcePermissions;

    protected static string $permissionPrefix = 'campaign_task';

    protected static ?string $model = CampaignTask::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Campaign Operations';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Field Tasks';

    protected static ?string $modelLabel = 'Campaign Task';

    protected static ?string $pluralModelLabel = 'Campaign Tasks';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return CampaignTaskForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CampaignTaskInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CampaignTasksTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['constituency', 'village', 'booth', 'assignee']);
        $user = auth()->user();

        if (! $user || $user->isSuperAdmin() || $user->isAssemblyAdmin() || $user->can('campaign_task.assign')) {
            return $query;
        }

        return $query->where(function (Builder $scope) use ($user): void {
            $scope->where('assigned_to', $user->getKey());

            if ($user->booth_id) {
                $scope->orWhere('booth_id', $user->booth_id);
            } elseif ($user->village_id) {
                $scope->orWhere('village_id', $user->village_id);
            }
        });
    }

    public static function getRelations(): array
    {
        return [UpdatesRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCampaignTasks::route('/'),
            'create' => CreateCampaignTask::route('/create'),
            'view' => ViewCampaignTask::route('/{record}'),
            'edit' => EditCampaignTask::route('/{record}/edit'),
        ];
    }
}
