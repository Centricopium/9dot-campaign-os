<?php

namespace App\Filament\Pages;

use App\Jobs\ProcessVoterImport;
use App\Models\Constituency;
use App\Models\VoterImportBatch;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use UnitEnum;

class ImportCentre extends Page
{
    use WithFileUploads;

    protected string $view = 'filament.pages.import-centre';

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedArrowUpTray;

    protected static string|UnitEnum|null $navigationGroup = 'Data Management';

    protected static ?string $navigationLabel = 'Import Centre';

    protected static ?string $title = 'Import Centre';

    protected static ?int $navigationSort = 1;

    /*
    |--------------------------------------------------------------------------
    | Form State
    |--------------------------------------------------------------------------
    */

    public ?int $constituencyId = null;

    public $villageFile = null;

    public $boothFile = null;

    public $voterFile = null;

    /*
    |--------------------------------------------------------------------------
    | Lifecycle
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $user = Auth::user();

        if (! $user->isSuperAdmin()) {
            $this->constituencyId = $user->constituency_id;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isSuperAdmin(): bool
    {
        return Auth::user()->isSuperAdmin();
    }

    public function getConstituencies()
    {
        return Constituency::query()
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | Voter Import
    |--------------------------------------------------------------------------
    */

    public function importVoters(): void
    {
        if (
            ! Auth::user()->isSuperAdmin()
            && ! Auth::user()->can('import.voter')
        ) {
            Notification::make()
                ->title('Unauthorized')
                ->body('You do not have permission to import voters.')
                ->danger()
                ->send();

            return;
        }

        $this->validate([
            'constituencyId' => [
                'required',
                'exists:constituencies,id',
            ],
            'voterFile' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:51200',
            ],
        ]);

        try {
            $path = $this->voterFile->store('imports/voters', 'local');

            $batch = VoterImportBatch::create(['constituency_id' => (int) $this->constituencyId, 'user_id' => auth()->id(), 'file_name' => $this->voterFile->getClientOriginalName(), 'file_path' => $path, 'status' => 'Queued']);
            ProcessVoterImport::dispatch($path, (int) $this->constituencyId, $batch->id);

            Notification::make()
                ->title('Voter import queued')
                ->body('The file will be processed in the background. Keep a queue worker running to complete the import.')
                ->success()
                ->send();

            $this->reset('voterFile');

        } catch (\Throwable $e) {

            logger()->error(
                'Voter Import Failed',
                [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );

            Notification::make()
                ->title('Voter Import Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    */

    public static function canAccess(): bool
    {
        return auth()->user()?->canAny([
            'import.village',
            'import.booth',
            'import.voter',
        ]) ?? false;
    }
}
