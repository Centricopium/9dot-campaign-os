<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected ?int $selectedRoleId = null;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['role_id'] = $this->record->roles()->value('roles.id');

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->selectedRoleId = isset($data['role_id']) ? (int) $data['role_id'] : null;
        unset($data['role_id']);

        $user = auth()->user();

        if ($user?->isAssemblyAdmin() && ! $user->isSuperAdmin()) {
            $data['constituency_id'] = $user->constituency_id;
            $data['is_super_admin'] = false;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->selectedRoleId) {
            $this->record->syncRoles([$this->selectedRoleId]);
        }
    }
}
