<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesPagePermission;
use App\Filament\Resources\CampaignBudgets\CampaignBudgetResource;
use App\Filament\Resources\CampaignExpenses\CampaignExpenseResource;
use App\Models\CampaignBudget;
use App\Models\CampaignExpense;
use App\Models\Constituency;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use UnitEnum;

class CampaignFinanceCentre extends Page
{
    use AuthorizesPagePermission;

    protected static string $requiredPermission = 'campaign_finance.view';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Campaign Operations';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Finance Centre';

    protected static ?string $title = 'Campaign Budget & Expense Centre';

    protected string $view = 'filament.pages.campaign-finance-centre';

    public string $constituencyId = '';

    public string $category = '';

    public function mount(): void
    {
        $user = auth()->user();

        if ($user?->isAssemblyAdmin() && ! $user->isSuperAdmin() && $user->constituency_id) {
            $this->constituencyId = (string) $user->constituency_id;
        }
    }

    public function getConstituenciesProperty(): Collection
    {
        return Constituency::query()->orderBy('name')->get(['id', 'name']);
    }

    public function getStatsProperty(): array
    {
        $budget = $this->budgetQuery();
        $expense = $this->expenseQuery();
        $allocated = (float) (clone $budget)->sum('allocated_amount');
        $approved = (float) (clone $expense)->where('approval_status', 'Approved')->sum('amount');

        return [
            'allocated' => $allocated,
            'approved' => $approved,
            'available' => max(0, $allocated - $approved),
            'utilization' => $allocated > 0 ? min(100, (int) round(($approved / $allocated) * 100)) : 0,
            'pending_count' => (clone $expense)->where('approval_status', 'Submitted')->count(),
            'pending_amount' => (float) (clone $expense)->where('approval_status', 'Submitted')->sum('amount'),
            'unpaid' => (float) (clone $expense)->where('approval_status', 'Approved')->whereNotIn('payment_status', ['Paid', 'Cancelled'])->sum('amount'),
        ];
    }

    public function getBudgetsProperty(): Collection
    {
        return $this->budgetQuery()
            ->with('constituency:id,name')
            ->withSum(['expenses as expenses_sum_amount' => fn (Builder $query): Builder => $query->where('approval_status', 'Approved')], 'amount')
            ->orderByDesc('allocated_amount')
            ->limit(10)
            ->get();
    }

    public function getCategorySpendProperty(): Collection
    {
        return $this->expenseQuery()
            ->where('approval_status', 'Approved')
            ->selectRaw('category, SUM(amount) as total_amount')
            ->groupBy('category')
            ->orderByDesc('total_amount')
            ->get();
    }

    public function getRecentExpensesProperty(): Collection
    {
        return $this->expenseQuery()
            ->with(['constituency:id,name', 'budget:id,title', 'submitter:id,name'])
            ->latest('expense_date')
            ->latest('id')
            ->limit(15)
            ->get();
    }

    public function budgetIndexUrl(): string
    {
        return CampaignBudgetResource::getUrl('index');
    }

    public function createBudgetUrl(): string
    {
        return CampaignBudgetResource::getUrl('create');
    }

    public function expenseIndexUrl(): string
    {
        return CampaignExpenseResource::getUrl('index');
    }

    public function createExpenseUrl(): string
    {
        return CampaignExpenseResource::getUrl('create');
    }

    public function expenseUrl(CampaignExpense $expense): string
    {
        return CampaignExpenseResource::getUrl('edit', ['record' => $expense]);
    }

    private function budgetQuery(): Builder
    {
        return CampaignBudget::query()
            ->when($this->constituencyId, fn (Builder $query, string $id): Builder => $query->where('constituency_id', $id))
            ->when($this->category, fn (Builder $query, string $category): Builder => $query->where('category', $category));
    }

    private function expenseQuery(): Builder
    {
        return CampaignExpense::query()
            ->when($this->constituencyId, fn (Builder $query, string $id): Builder => $query->where('constituency_id', $id))
            ->when($this->category, fn (Builder $query, string $category): Builder => $query->where('category', $category));
    }
}
