<?php

namespace App\Filament\Pages;

use App\Imports\VotersImport;
use App\Models\Constituency;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use UnitEnum;

class ImportCentre extends Page
{
    use WithFileUploads;

    protected string $view = 'filament.pages.import-centre';

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedArrowUpTray;

    protected static string|UnitEnum|null $navigationGroup =
        'Data Management';

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
            $import = new VotersImport(
                $this->constituencyId
            );

            Excel::import(
                $import,
                $this->voterFile->getRealPath()
            );

            $message = "Imported: {$import->imported} voters";

            if ($import->skipped > 0) {
                $message .= " | Skipped: {$import->skipped}";
            }

            Notification::make()
                ->title('Voter Import Completed')
                ->body($message)
                ->success()
                ->send();

            if (! empty($import->errors)) {
                logger()->warning(
                    'Voter Import Errors',
                    $import->errors
                );
            }

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