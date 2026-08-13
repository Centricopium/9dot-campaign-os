<?php

namespace App\Livewire;

use App\Filament\Schemas\VillageImportSchema;
use App\Imports\VillagesImport;
use App\Models\Constituency;
use App\Models\User;
use App\Services\Import\VillageImportService;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class VillageImportForm extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $user = $this->authorizedUser();

        $this->form->fill($this->defaultFormData($user));
    }

    public function form(Schema $schema): Schema
    {
        return VillageImportSchema::configure($schema)
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $user = $this->authorizedUser();

        $constituencyId = $this->resolveConstituencyId($user, $data);
        $excelFile = $data['excel_file'] ?? null;

        if (
            ! is_string($excelFile) ||
            ! str_starts_with($excelFile, 'imports/villages/')
        ) {
            throw ValidationException::withMessages([
                'data.excel_file' => 'Please upload a valid Excel file.',
            ]);
        }

        try {
            Excel::import(
                new VillagesImport(
                    app(VillageImportService::class),
                    $constituencyId,
                ),
                Storage::disk('local')->path($excelFile),
            );

            Storage::disk('local')->delete($excelFile);

            $this->form->fill($this->defaultFormData($user));

            Notification::make()
                ->title('Village import completed')
                ->body('The Village Master Excel file was imported successfully.')
                ->success()
                ->send();
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('Village import failed')
                ->body('Please check the Excel file and try again.')
                ->danger()
                ->persistent()
                ->send();
        }
    }

    public function render(): View
    {
        return view('livewire.village-import-form');
    }

    private function authorizedUser(): User
    {
        $user = Auth::user();

        abort_unless(
            $user instanceof User &&
            ($user->isSuperAdmin() || $user->can('import.village')),
            403,
        );

        return $user;
    }

    private function defaultFormData(User $user): array
    {
        return [
            'constituency_id' => $user->isSuperAdmin()
                ? null
                : $user->constituency_id,
        ];
    }

    private function resolveConstituencyId(User $user, array $data): int
    {
        $constituencyId = $user->isSuperAdmin()
            ? ($data['constituency_id'] ?? null)
            : $user->constituency_id;

        if (
            blank($constituencyId) ||
            ! Constituency::query()->whereKey($constituencyId)->exists()
        ) {
            throw ValidationException::withMessages([
                'data.constituency_id' => 'Please select a valid constituency.',
            ]);
        }

        return (int) $constituencyId;
    }
}