<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Models\Candidate;
use App\Models\Constituency;
use App\Models\PoliticalParty;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use UnitEnum;

class CandidateStrategy extends Page
{
    use AuthorizesPagePermission;

    protected static string $requiredPermission = 'candidate.view';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static string|UnitEnum|null $navigationGroup = 'Candidate Strategy';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Strategy Dashboard';

    protected static ?string $title = 'Candidate Strategy Dashboard';

    protected string $view = 'filament.pages.candidate-strategy';

    public string $constituencyId = '';

    public string $partyId = '';

    public string $electionYear = '';

    public function mount(): void
    {
        $user = auth()->user();

        if ($user?->isAssemblyAdmin() && ! $user->isSuperAdmin() && $user->constituency_id) {
            $this->constituencyId = (string) $user->constituency_id;
        }
    }

    public function updatedConstituencyId(): void
    {
        $this->partyId = '';
        $this->electionYear = '';
    }

    public function resetFilters(): void
    {
        $user = auth()->user();
        $this->constituencyId = $user?->isAssemblyAdmin() && ! $user->isSuperAdmin()
            ? (string) ($user->constituency_id ?? '')
            : '';
        $this->partyId = '';
        $this->electionYear = '';
    }

    public function getConstituenciesProperty(): Collection
    {
        return Constituency::query()->orderBy('name')->get(['id', 'name']);
    }

    public function getPartiesProperty(): Collection
    {
        return PoliticalParty::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'short_name']);
    }

    public function getElectionYearsProperty(): Collection
    {
        if (! $this->constituencyId) {
            return collect();
        }

        return Candidate::query()
            ->where('constituency_id', $this->constituencyId)
            ->distinct()
            ->orderByDesc('election_year')
            ->pluck('election_year');
    }

    public function getCandidatesProperty(): Collection
    {
        if (! $this->constituencyId) {
            return collect();
        }

        return Candidate::query()
            ->with(['constituency', 'politicalParty', 'finalisedBy'])
            ->withAvg('submittedAssessments as average_score', 'overall_score')
            ->withAvg('submittedAssessments as average_risk_score', 'risk_score')
            ->withAvg('submittedAssessments as average_confidence_score', 'confidence_score')
            ->withCount('submittedAssessments')
            ->where('constituency_id', $this->constituencyId)
            ->when($this->partyId, fn ($query, $id) => $query->where('political_party_id', $id))
            ->when($this->electionYear, fn ($query, $year) => $query->where('election_year', $year))
            ->orderByDesc('is_final')
            ->orderByDesc('average_score')
            ->orderBy('name')
            ->get();
    }
}
