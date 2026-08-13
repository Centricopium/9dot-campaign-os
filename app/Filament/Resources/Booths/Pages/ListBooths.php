<?php

namespace App\Filament\Resources\Booths\Pages;

use App\Filament\Resources\Booths\BoothResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBooths extends ListRecords
{
    protected static string $resource = BoothResource::class;

    protected ?string $defaultTableSortColumn = 'booth_no';

    protected ?string $defaultTableSortDirection = 'asc';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
