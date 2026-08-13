<?php

namespace App\Livewire;

use App\Filament\Schemas\BoothImportSchema;
use App\Imports\BoothsImport;
use App\Models\Constituency;
use App\Models\User;
use App\Services\Import\BoothImportService;
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

class BoothImportForm extends Component implements HasSchemas
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
        return BoothImportSchema::configure($schema)
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
            ! str_starts_with($excelFile, 'imports/booths/')
        ) {
            throw ValidationException::withMessages([
                'data.excel_file' => 'Please upload a valid Excel file.',
            ]);
        }

        try {
            $import = new BoothsImport(
                app(BoothImportService::class),
                $constituencyId,
            );

            Excel::import(
                $import,
                Storage::disk('local')->path($excelFile),
            );

            Storage::disk('local')->delete($excelFile);

            $this->form->fill($this->defaultFormData($user));

            if ($import->processedRows === 0) {
                Notification::make()
                    ->title('No booth rows imported')
                    ->body('Use the first Excel sheet and ensure Booth No is numeric.')
                    ->danger()
                    ->send();

                return;
            }

            Notification::make()
                ->title('Booth import completed')
                ->body("{$import->processedRows} booth record(s) imported successfully.")
                ->success()
                ->send();
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('Booth import failed')
                ->body('Check that every village in the Excel file exists in the selected constituency.')
                ->danger()
                ->persistent()
                ->send();
        }
    }

    public function render(): View
    {
        return view('livewire.booth-import-form');
    }

    private function authorizedUser(): User
    {
        $user = Auth::user();

        abort_unless(
            $user instanceof User &&
            ($user->isSuperAdmin() || $user->can('import.booth')),
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