<?php

namespace App\Livewire;

use App\Filament\Schemas\VoterImportSchema;
use App\Imports\VotersImport;
use App\Models\Constituency;
use App\Models\User;
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

class VoterImportForm extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $user = $this->authorizedUser();

        $this->form->fill(
            $this->defaultFormData($user)
        );
    }

    public function form(Schema $schema): Schema
    {
        return VoterImportSchema::configure($schema)
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $user = $this->authorizedUser();

        $constituencyId = $this->resolveConstituencyId(
            $user,
            $data
        );

        $excelFile = $data['excel_file'] ?? null;

        if (
            ! is_string($excelFile) ||
            ! str_starts_with($excelFile, 'imports/voters/')
        ) {
            throw ValidationException::withMessages([
                'data.excel_file' => 'Please upload a valid Excel file.',
            ]);
        }

        try {
            $import = new VotersImport(
                $constituencyId
            );

            Excel::import(
                $import,
                Storage::disk('local')->path($excelFile),
            );

            Storage::disk('local')->delete($excelFile);

            $this->form->fill(
                $this->defaultFormData($user)
            );

            $message = "{$import->imported} voter record(s) imported successfully.";

            if ($import->skipped > 0) {
                $message .= " {$import->skipped} row(s) skipped.";
            }

            Notification::make()
                ->title('Voter import completed')
                ->body($message)
                ->success()
                ->send();

            if (! empty($import->errors)) {
                logger()->warning(
                    'Voter Import Errors',
                    $import->errors
                );
            }
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('Voter import failed')
                ->body(
                    'Check the Excel file, selected constituency, PART_NO and voter data.'
                )
                ->danger()
                ->persistent()
                ->send();
        }
    }

    public function render(): View
    {
        return view('livewire.voter-import-form');
    }

    private function authorizedUser(): User
    {
        $user = Auth::user();

        abort_unless(
            $user instanceof User &&
            (
                $user->isSuperAdmin()
                || $user->can('import.voter')
            ),
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

    private function resolveConstituencyId(
        User $user,
        array $data
    ): int {
        $constituencyId = $user->isSuperAdmin()
            ? ($data['constituency_id'] ?? null)
            : $user->constituency_id;

        if (
            blank($constituencyId) ||
            ! Constituency::query()
                ->whereKey($constituencyId)
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'data.constituency_id' =>
                    'Please select a valid constituency.',
            ]);
        }

        return (int) $constituencyId;
    }
}