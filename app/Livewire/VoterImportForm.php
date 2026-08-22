<?php

namespace App\Livewire;

use App\Filament\Schemas\VoterImportSchema;
use App\Jobs\ProcessVoterImport;
use App\Models\Constituency;
use App\Models\User;
use App\Models\VoterImportBatch;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
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
            $batch = VoterImportBatch::create(['constituency_id' => $constituencyId, 'user_id' => $user->id, 'file_name' => basename($excelFile), 'file_path' => $excelFile, 'status' => 'Queued']);
            ProcessVoterImport::dispatch($excelFile, $constituencyId, $batch->id);

            $this->form->fill(
                $this->defaultFormData($user)
            );

            Notification::make()
                ->title('Voter import queued')
                ->body('The file will be processed in the background. Keep a queue worker running to complete the import.')
                ->success()
                ->send();
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
                'data.constituency_id' => 'Please select a valid constituency.',
            ]);
        }

        return (int) $constituencyId;
    }
}
