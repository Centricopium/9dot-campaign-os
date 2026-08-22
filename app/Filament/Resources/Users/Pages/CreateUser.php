<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected ?int $selectedRoleId = null;

    protected function mutateFormDataBeforeCreate(array $data): array
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

    protected function afterCreate(): void
    {
        if ($this->selectedRoleId) {
            $this->record->syncRoles([$this->selectedRoleId]);
        }
    }
}
