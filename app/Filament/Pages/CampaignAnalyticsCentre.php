<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Models\Booth;
use App\Models\Constituency;
use App\Models\Village;
use App\Services\Analytics\CampaignAnalyticsService;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class CampaignAnalyticsCentre extends Page
{
    use AuthorizesPagePermission;

    protected static string $requiredPermission = 'report.view';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;
    protected static ?string $navigationLabel = 'Analytics Centre';
    protected static string|UnitEnum|null $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 2;
    protected static ?string $title = 'Campaign Analytics Centre';
    protected string $view = 'filament.pages.campaign-analytics-centre';

    public string $tab = 'overview';
    public string $constituencyId = '';
    public string $taluka = '';
    public string $villageId = '';
    public string $boothId = '';

    public function mount(): void
    {
        $user = auth()->user();
        if ($user?->isAssemblyAdmin() && ! $user->isSuperAdmin() && $user->constituency_id) {
            $this->constituencyId = (string) $user->constituency_id;
        }
    }

    public function updatedConstituencyId(): void { $this->taluka = $this->villageId = $this->boothId = ''; }
    public function updatedTaluka(): void { $this->villageId = $this->boothId = ''; }
    public function updatedVillageId(): void { $this->boothId = ''; }

    public function getConstituenciesProperty() { return Constituency::query()->orderBy('name')->get(['id', 'name']); }
    public function getTalukasProperty() { return Village::query()->when($this->constituencyId, fn ($q, $id) => $q->where('constituency_id', $id))->whereNotNull('taluka')->where('taluka', '!=', '')->distinct()->orderBy('taluka')->pluck('taluka'); }
    public function getVillagesProperty() { return Village::query()->when($this->constituencyId, fn ($q, $id) => $q->where('constituency_id', $id))->when($this->taluka, fn ($q, $taluka) => $q->where('taluka', $taluka))->orderBy('name')->get(['id', 'name']); }
    public function getBoothsProperty() { return Booth::query()->when($this->villageId, fn ($q, $id) => $q->where('village_id', $id))->when($this->taluka && ! $this->villageId, fn ($q) => $q->whereHas('village', fn ($v) => $v->where('taluka', $this->taluka)))->when($this->constituencyId && ! $this->villageId, fn ($q) => $q->whereHas('village', fn ($v) => $v->where('constituency_id', $this->constituencyId)))->orderByRaw('CAST(booth_no AS UNSIGNED)')->get(['id', 'booth_no', 'booth_name']); }
    public function getChartsProperty(): array { return app(CampaignAnalyticsService::class)->generate($this->filters()); }

    public function exportCurrentTabPdf()
    {
        $groups = $this->charts;

        return $this->downloadPdf(
            [$this->tab => $groups[$this->tab] ?? []],
            'campaign-analytics-' . $this->tab,
        );
    }

    public function exportAllChartsPdf()
    {
        return $this->downloadPdf($this->charts, 'campaign-analytics-complete');
    }

    private function downloadPdf(array $charts, string $filename)
    {
        $pdf = Pdf::loadView('reports.campaign-analytics-pdf', [
            'charts' => $charts,
            'filters' => $this->filterLabels(),
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename . '-' . now()->format('Y-m-d-His') . '.pdf',
        );
    }

    private function filterLabels(): array
    {
        $booth = $this->booths->firstWhere('id', (int) $this->boothId);

        return [
            'Constituency' => $this->constituencies->firstWhere('id', (int) $this->constituencyId)?->name ?? 'All',
            'Taluka' => $this->taluka ?: 'All',
            'Village' => $this->villages->firstWhere('id', (int) $this->villageId)?->name ?? 'All',
            'Booth' => $booth ? $booth->booth_no . ' — ' . $booth->booth_name : 'All',
        ];
    }

    private function filters(): array
    {
        return ['constituency_id' => $this->constituencyId ?: null, 'taluka' => $this->taluka ?: null, 'village_id' => $this->villageId ?: null, 'booth_id' => $this->boothId ?: null, 'support_level' => null];
    }
}
