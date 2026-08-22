<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Models\Booth;
use App\Models\Constituency;
use App\Models\Village;
use App\Services\Reports\CampaignReportService;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class ReportsCentre extends Page
{
    use AuthorizesPagePermission;

    protected static string $requiredPermission = 'report.view';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;
    protected static ?string $navigationLabel = 'Reports Centre';
    protected static string|UnitEnum|null $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'Campaign Reports Centre';
    protected string $view = 'filament.pages.reports-centre';

    public string $report = 'constituency_summary';
    public string $constituencyId = '';
    public string $taluka = '';
    public string $villageId = '';
    public string $boothId = '';
    public string $supportLevel = '';

    public function mount(): void
    {
        $user = auth()->user();

        if ($user?->isAssemblyAdmin() && ! $user->isSuperAdmin() && $user->constituency_id) {
            $this->constituencyId = (string) $user->constituency_id;
        }
    }

    public function updatedConstituencyId(): void
    {
        $this->villageId = '';
        $this->boothId = '';
        $this->taluka = '';
    }

    public function updatedTaluka(): void
    {
        $this->villageId = '';
        $this->boothId = '';
    }

    public function updatedVillageId(): void
    {
        $this->boothId = '';
    }

    public function resetFilters(): void
    {
        $user = auth()->user();
        $this->constituencyId = $user?->isAssemblyAdmin() && ! $user->isSuperAdmin()
            ? (string) ($user->constituency_id ?? '')
            : '';
        $this->villageId = '';
        $this->boothId = '';
        $this->taluka = '';
        $this->supportLevel = '';
    }

    public function getCatalogProperty(): array
    {
        return CampaignReportService::catalog();
    }

    public function getConstituenciesProperty()
    {
        return Constituency::query()->orderBy('name')->get(['id', 'name']);
    }

    public function getVillagesProperty()
    {
        return Village::query()
            ->when($this->constituencyId, fn ($query) => $query->where('constituency_id', $this->constituencyId))
            ->when($this->taluka, fn ($query) => $query->where('taluka', $this->taluka))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function getTalukasProperty()
    {
        return Village::query()
            ->when($this->constituencyId, fn ($query) => $query->where('constituency_id', $this->constituencyId))
            ->whereNotNull('taluka')
            ->where('taluka', '!=', '')
            ->distinct()
            ->orderBy('taluka')
            ->pluck('taluka');
    }

    public function getBoothsProperty()
    {
        return Booth::query()
            ->when($this->villageId, fn ($query) => $query->where('village_id', $this->villageId))
            ->when($this->taluka && ! $this->villageId, fn ($query) => $query->whereHas(
                'village',
                fn ($village) => $village->where('taluka', $this->taluka),
            ))
            ->when($this->constituencyId && ! $this->villageId, fn ($query) => $query->whereHas(
                'village',
                fn ($village) => $village->where('constituency_id', $this->constituencyId),
            ))
            ->orderByRaw('CAST(booth_no AS UNSIGNED)')
            ->get(['id', 'booth_no', 'booth_name']);
    }

    public function getResultProperty(): array
    {
        return app(CampaignReportService::class)->generate($this->report, $this->filters(), 100);
    }

    public function exportCsv(): StreamedResponse
    {
        $result = app(CampaignReportService::class)->generate($this->report, $this->filters(), 50000);
        $filename = $this->report . '-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($result): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $result['headers']);

            foreach ($result['rows'] as $row) {
                fputcsv($handle, array_values($row));
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportPdf()
    {
        $result = app(CampaignReportService::class)->generate($this->report, $this->filters(), 1000);
        $pdf = Pdf::loadView('reports.campaign-report-pdf', [
            'result' => $result,
            'filters' => $this->filters(),
        ])->setPaper('a4', count($result['headers']) > 7 ? 'landscape' : 'portrait');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $this->report . '-' . now()->format('Y-m-d-His') . '.pdf',
        );
    }

    private function filters(): array
    {
        return [
            'constituency_id' => $this->constituencyId ?: null,
            'taluka' => $this->taluka ?: null,
            'village_id' => $this->villageId ?: null,
            'booth_id' => $this->boothId ?: null,
            'support_level' => $this->supportLevel ?: null,
        ];
    }
}
