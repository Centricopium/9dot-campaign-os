<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Models\Booth;
use App\Services\Dashboard\BoothDashboardService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class BoothDashboard extends Page
{
    use AuthorizesPagePermission;
    protected static string $requiredPermission = 'dashboard.view';
    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Campaign';

    protected static ?string $navigationLabel = 'Booth Dashboard';

    protected static ?string $title = 'Booth Dashboard';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.booth-dashboard';

    public ?int $boothId = null;

    public function getBoothsProperty()
    {
        return Booth::query()
            ->with('village')
            ->orderByRaw('CAST(booth_no AS UNSIGNED)')
            ->get();
    }

    public function getSelectedBoothProperty(): ?Booth
    {
        if (! $this->boothId) {
            return null;
        }

        return Booth::query()
            ->with('village')
            ->find($this->boothId);
    }

    public function getSummaryProperty(): array
    {
        if (! $this->boothId) {
            return [
                'houses' => 0,
                'voters' => 0,
                'male' => 0,
                'female' => 0,
                'other' => 0,
                'volunteers' => 0,
                'influencers' => 0,
                'active_voters' => 0,
                'neutral' => 0,
                'undecided' => 0,
                'parties' => [],
            ];
        }

        $booth = $this->selectedBooth;

        if (! $booth) {
            return [
                'houses' => 0,
                'voters' => 0,
                'male' => 0,
                'female' => 0,
                'other' => 0,
                'volunteers' => 0,
                'influencers' => 0,
                'active_voters' => 0,
                'neutral' => 0,
                'undecided' => 0,
                'parties' => [],
            ];
        }

        return app(BoothDashboardService::class)->summary($booth);
    }
}
