<?php

namespace App\Filament\Pages;

use App\Models\Constituency;
use App\Services\Dashboard\ConstituencyDashboardService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ConstituencyDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Campaign';

    protected static ?string $navigationLabel = 'Constituency Dashboard';

    protected static ?string $title = 'Constituency Dashboard';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.constituency-dashboard';

    public ?int $constituencyId = null;

    public function getConstituenciesProperty()
    {
        return Constituency::query()
            ->orderBy('name')
            ->get();
    }

    public function getSelectedConstituencyProperty(): ?Constituency
    {
        if (! $this->constituencyId) {
            return null;
        }

        return Constituency::query()
            ->find($this->constituencyId);
    }

    public function getSummaryProperty(): array
    {
        if (! $this->constituencyId) {
            return $this->emptySummary();
        }

        $constituency = $this->selectedConstituency;

        if (! $constituency) {
            return $this->emptySummary();
        }

        return app(ConstituencyDashboardService::class)
            ->summary($constituency);
    }

    protected function emptySummary(): array
    {
        return [
            'villages' => 0,
            'booths' => 0,
            'houses' => 0,
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
            'villages_list' => [],
        ];
    }
}