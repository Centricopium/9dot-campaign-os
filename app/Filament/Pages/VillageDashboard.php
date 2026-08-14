<?php

namespace App\Filament\Pages;

use App\Models\Village;
use App\Services\Dashboard\VillageDashboardService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class VillageDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedHomeModern;

    protected static string|UnitEnum|null $navigationGroup = 'Campaign';

    protected static ?string $navigationLabel = 'Village Dashboard';

    protected static ?string $title = 'Village Dashboard';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.village-dashboard';

    public ?int $villageId = null;

    public function getVillagesProperty()
    {
        return Village::query()
            ->with('constituency')
            ->orderBy('name')
            ->get();
    }

    public function getSelectedVillageProperty(): ?Village
    {
        if (! $this->villageId) {
            return null;
        }

        return Village::query()
            ->with('constituency')
            ->find($this->villageId);
    }

    public function getSummaryProperty(): array
    {
        if (! $this->villageId) {
            return $this->emptySummary();
        }

        $village = $this->selectedVillage;

        if (! $village) {
            return $this->emptySummary();
        }

        return app(VillageDashboardService::class)
            ->summary($village);
    }

    protected function emptySummary(): array
    {
        return [
            'houses' => 0,
            'booths' => 0,
            'voters' => 0,

            'male' => 0,
            'female' => 0,
            'other' => 0,

            'volunteers' => 0,
            'influencers' => 0,

            'active_voters' => 0,

            'strong_support' => 0,
            'moderate_support' => 0,
            'leaning_support' => 0,
            'neutral' => 0,
            'undecided' => 0,
            'leaning_opposition' => 0,
            'moderate_opposition' => 0,
            'strong_opposition' => 0,

            'parties' => [],
        ];
    }
}