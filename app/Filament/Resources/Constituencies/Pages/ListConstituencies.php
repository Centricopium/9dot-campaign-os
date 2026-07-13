<?php

namespace App\Filament\Resources\Constituencies\Pages;

use App\Filament\Resources\Constituencies\ConstituencyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConstituencies extends ListRecords
{
    protected static string $resource = ConstituencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
