<?php

namespace App\Filament\Resources\CampaignIssues;

use App\Filament\Resources\CampaignIssues\Pages\CreateCampaignIssue;
use App\Filament\Resources\CampaignIssues\Pages\EditCampaignIssue;
use App\Filament\Resources\CampaignIssues\Pages\ListCampaignIssues;
use App\Filament\Resources\CampaignIssues\Pages\ViewCampaignIssue;
use App\Filament\Resources\CampaignIssues\RelationManagers\UpdatesRelationManager;
use App\Filament\Resources\CampaignIssues\Schemas\CampaignIssueForm;
use App\Filament\Resources\CampaignIssues\Schemas\CampaignIssueInfolist;
use App\Filament\Resources\CampaignIssues\Tables\CampaignIssuesTable;
use App\Filament\Resources\Concerns\AuthorizesResourcePermissions;
use App\Models\CampaignIssue;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CampaignIssueResource extends Resource
{
    use AuthorizesResourcePermissions;

    protected static string $permissionPrefix = 'campaign_issue';

    protected static ?string $model = CampaignIssue::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'Campaign Operations';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Issue Register';

    protected static ?string $modelLabel = 'Public Issue';

    protected static ?string $pluralModelLabel = 'Public Issues';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return CampaignIssueForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CampaignIssueInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CampaignIssuesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['constituency', 'village', 'booth', 'assignee']);
        $user = auth()->user();

        if (! $user || $user->isSuperAdmin() || $user->isAssemblyAdmin() || $user->can('campaign_issue.assign')) {
            return $query;
        }

        return $query->where(function (Builder $scope) use ($user): void {
            $scope->where('assigned_to', $user->id)->orWhere('reported_by', $user->id)->orWhere('is_confidential', false);
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
        return ['index' => ListCampaignIssues::route('/'), 'create' => CreateCampaignIssue::route('/create'), 'view' => ViewCampaignIssue::route('/{record}'), 'edit' => EditCampaignIssue::route('/{record}/edit')];
    }
}
