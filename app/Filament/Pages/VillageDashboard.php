<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Models\Village;
use App\Services\Dashboard\VillageDashboardService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class VillageDashboard extends Page
{
    use AuthorizesPagePermission;
    protected static string $requiredPermission = 'dashboard.view';
    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedHomeModern;

    protected static string|UnitEnum|null $navigationGroup =
        'Campaign';

    protected static ?string $navigationLabel =
        'Village Dashboard';

    protected static ?string $title =
        'Village Dashboard';

    protected static ?int $navigationSort =
        5;

    protected string $view =
        'filament.pages.village-dashboard';


    /**
     * Currently selected village.
     */
    public ?int $villageId = null;


    /**
     * Get all villages.
     */
    public function getVillagesProperty()
    {
        return Village::query()
            ->with('constituency')
            ->orderBy('name')
            ->get();
    }


    /**
     * Get selected village.
     */
    public function getSelectedVillageProperty(): ?Village
    {
        if (! $this->villageId) {
            return null;
        }

        return Village::query()
            ->with('constituency')
            ->find($this->villageId);
    }


    /**
     * Get complete village summary.
     */
    public function getSummaryProperty(): array
    {
        if (! $this->villageId) {
            return $this->emptySummary();
        }

        $village = $this->selectedVillage;

        if (! $village) {
            return $this->emptySummary();
        }

        return app(
            VillageDashboardService::class
        )->summary($village);
    }


    /**
     * Get booth-wise intelligence for selected village.
     *
     * This uses the same BoothIntelligenceService
     * used by the Booth Intelligence page.
     */
    public function getBoothIntelligenceProperty()
    {
        if (! $this->villageId) {
            return collect();
        }

        $village = $this->selectedVillage;

        if (! $village) {
            return collect();
        }

        return app(
            VillageDashboardService::class
        )->boothIntelligence($village);
    }


    /**
     * Empty summary structure.
     */
    protected function emptySummary(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Basic Village Statistics
            |--------------------------------------------------------------------------
            */

            'houses' => 0,

            'booths' => 0,

            'voters' => 0,


            /*
            |--------------------------------------------------------------------------
            | Gender
            |--------------------------------------------------------------------------
            */

            'male' => 0,

            'female' => 0,

            'other' => 0,


            /*
            |--------------------------------------------------------------------------
            | Campaign People
            |--------------------------------------------------------------------------
            */

            'volunteers' => 0,

            'influencers' => 0,


            /*
            |--------------------------------------------------------------------------
            | Voter Status
            |--------------------------------------------------------------------------
            */

            'active_voters' => 0,


            /*
            |--------------------------------------------------------------------------
            | Political Support
            |--------------------------------------------------------------------------
            */

            'strong_support' => 0,

            'moderate_support' => 0,

            'leaning_support' => 0,

            'neutral' => 0,

            'undecided' => 0,

            'leaning_opposition' => 0,

            'moderate_opposition' => 0,

            'strong_opposition' => 0,


            /*
            |--------------------------------------------------------------------------
            | Political Parties
            |--------------------------------------------------------------------------
            */

            'parties' => [],


            /*
            |--------------------------------------------------------------------------
            | Booth Intelligence
            |--------------------------------------------------------------------------
            |
            | This is populated by VillageDashboardService.
            |
            */

            'booth_intelligence' => [],
        ];
    }
}
