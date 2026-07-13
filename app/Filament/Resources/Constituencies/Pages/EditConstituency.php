<?php

namespace App\Filament\Resources\Constituencies\Pages;

use App\Filament\Resources\Constituencies\ConstituencyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditConstituency extends EditRecord
{
    protected static string $resource = ConstituencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
