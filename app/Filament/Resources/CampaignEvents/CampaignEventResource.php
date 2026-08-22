<?php

namespace App\Filament\Resources\CampaignEvents;

use App\Filament\Resources\CampaignEvents\Pages\CreateCampaignEvent;
use App\Filament\Resources\CampaignEvents\Pages\EditCampaignEvent;
use App\Filament\Resources\CampaignEvents\Pages\ListCampaignEvents;
use App\Filament\Resources\CampaignEvents\RelationManagers\TeamMembersRelationManager;
use App\Filament\Resources\CampaignEvents\Schemas\CampaignEventForm;
use App\Filament\Resources\CampaignEvents\Tables\CampaignEventsTable;
use App\Filament\Resources\Concerns\AuthorizesResourcePermissions;
use App\Models\CampaignEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CampaignEventResource extends Resource
{
    use AuthorizesResourcePermissions;

    protected static string $permissionPrefix = 'campaign_event';

    protected static ?string $model = CampaignEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Campaign Operations';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Events & Tours';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return CampaignEventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CampaignEventsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [TeamMembersRelationManager::class];
    }

    public static function getPages(): array
    {
        return ['index' => ListCampaignEvents::route('/'), 'create' => CreateCampaignEvent::route('/create'), 'edit' => EditCampaignEvent::route('/{record}/edit')];
    }
}
