<x-filament-panels::page>
    @php($stats = $this->stats)
    @php($budgets = $this->budgets)
    @php($categories = $this->categorySpend)
    @php($maxCategory = max(1, (float) ($categories->max('total_amount') ?? 0)))
    <style>
        .fc{display:flex;flex-direction:column;gap:18px}.fc-hero,.fc-panel,.fc-stat{border:1px solid rgba(124,58,237,.16);border-radius:20px;background:#fff;box-shadow:0 18px 48px -36px rgba(76,29,149,.5)}.fc-hero{padding:24px;background:linear-gradient(125deg,#fff,#f5f3ff 62%,#ede9fe)}.fc-head{display:flex;justify-content:space-between;align-items:flex-start;gap:16px}.fc-title{font-size:27px;font-weight:950;color:#111827;letter-spacing:-.03em}.fc-sub{margin-top:6px;font-size:12px;color:#6b7280}.fc-actions{display:flex;flex-wrap:wrap;gap:9px;justify-content:flex-end}.fc-btn{display:inline-flex;align-items:center;min-height:42px;padding:9px 14px;border-radius:12px;text-decoration:none;color:#fff;background:linear-gradient(135deg,#7c3aed,#17111f);font-size:11px;font-weight:900}.fc-btn.alt{color:#5b21b6;background:#fff;border:1px solid #ddd6fe}.fc-filters{display:grid;grid-template-columns:2fr 1.4fr;gap:12px;margin-top:20px}.fc-field label{display:block;margin-bottom:6px;color:#4b5563;font-size:9px;font-weight:900;letter-spacing:.06em;text-transform:uppercase}.fc-select{width:100%;min-height:43px;padding:9px 12px;border:1px solid #ddd6fe;border-radius:12px;color:#111827;background:#fff}.fc-stats{display:grid;grid-template-columns:repeat(6,1fr);gap:10px}.fc-stat{padding:16px}.fc-stat span{font-size:8px;font-weight:900;color:#7c3aed;text-transform:uppercase;letter-spacing:.05em}.fc-stat strong{display:block;margin-top:6px;color:#111827;font-size:20px}.fc-stat small{display:block;margin-top:4px;color:#9ca3af;font-size:8px}.fc-layout{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(300px,.7fr);gap:16px}.fc-panel{overflow:hidden}.fc-panel-head{display:flex;align-items:center;justify-content:space-between;padding:16px 18px;border-bottom:1px solid #e5e7eb}.fc-panel-title{font-size:13px;font-weight:900;color:#111827}.fc-link{color:#7c3aed;font-size:10px;font-weight:850;text-decoration:none}.fc-budget{padding:15px 18px;border-bottom:1px solid #eef0f3}.fc-row{display:flex;justify-content:space-between;gap:12px}.fc-budget-name,.fc-expense-name{color:#111827;font-size:12px;font-weight:900}.fc-meta{margin-top:5px;color:#6b7280;font-size:9px}.fc-money{white-space:nowrap;text-align:right;color:#111827;font-size:11px;font-weight:850}.fc-track{height:7px;margin-top:10px;overflow:hidden;border-radius:999px;background:#ede9fe}.fc-track i{display:block;height:100%;border-radius:999px;background:linear-gradient(90deg,#7c3aed,#a855f7)}.fc-progress-label{display:flex;justify-content:space-between;margin-top:5px;color:#9ca3af;font-size:8px}.fc-cat{padding:13px 17px;border-bottom:1px solid #eef0f3}.fc-cat-label{display:flex;justify-content:space-between;color:#4b5563;font-size:10px}.fc-cat .fc-track{height:6px;margin-top:7px}.fc-expense{display:grid;grid-template-columns:90px minmax(0,1fr) auto auto;gap:12px;align-items:center;padding:14px 18px;border-bottom:1px solid #eef0f3;text-decoration:none}.fc-date{font-size:9px;color:#6b7280}.fc-badge{padding:4px 7px;border-radius:999px;color:#5b21b6;background:#ede9fe;font-size:8px;font-weight:850}.fc-badge.approved{color:#166534;background:#dcfce7}.fc-badge.rejected{color:#991b1b;background:#fee2e2}.fc-empty{padding:35px 18px;text-align:center;color:#9ca3af;font-size:11px}
        html.dark .fc-hero,html.dark .fc-panel,html.dark .fc-stat{border-color:rgba(167,139,250,.24);background:linear-gradient(145deg,#09080e,#171023);box-shadow:0 18px 48px -32px #000}html.dark .fc-hero{background:linear-gradient(125deg,#09080e,#171023 62%,#27113e)}html.dark .fc-title,html.dark .fc-stat strong,html.dark .fc-panel-title,html.dark .fc-budget-name,html.dark .fc-expense-name,html.dark .fc-money{color:#f8fafc}html.dark .fc-sub,html.dark .fc-field label,html.dark .fc-meta,html.dark .fc-cat-label,html.dark .fc-date{color:#aaa4b8}html.dark .fc-select{color:#f8fafc;background:#100d17;border-color:rgba(167,139,250,.3);color-scheme:dark}html.dark .fc-btn.alt{color:#ddd6fe;background:#171023;border-color:#4c1d95}html.dark .fc-panel-head,html.dark .fc-budget,html.dark .fc-cat,html.dark .fc-expense{border-color:rgba(148,163,184,.13)}html.dark .fc-track{background:#2b2038}
        @media(max-width:1200px){.fc-stats{grid-template-columns:repeat(3,1fr)}}@media(max-width:900px){.fc-layout{grid-template-columns:1fr}.fc-expense{grid-template-columns:75px 1fr auto}}@media(max-width:650px){.fc-head{flex-direction:column}.fc-actions{justify-content:flex-start}.fc-filters,.fc-stats{grid-template-columns:1fr 1fr}.fc-expense{grid-template-columns:1fr}.fc-money{text-align:left}}
    </style>

    <div class="fc">
        <section class="fc-hero">
            <div class="fc-head">
                <div><div class="fc-title">Campaign Budget & Expense Centre</div><div class="fc-sub">Plan Assembly budgets, approve expenses, track payments and protect every campaign rupee.</div></div>
                <div class="fc-actions">
                    <a class="fc-btn alt" href="{{ $this->budgetIndexUrl() }}">View Budgets</a>
                    <a class="fc-btn alt" href="{{ $this->expenseIndexUrl() }}">Expense Register</a>
                    @can('campaign_finance.create')<a class="fc-btn" href="{{ $this->createBudgetUrl() }}">+ Create Budget</a><a class="fc-btn" href="{{ $this->createExpenseUrl() }}">+ Submit Expense</a>@endcan
                </div>
            </div>
            <div class="fc-filters">
                <div class="fc-field"><label>Assembly Constituency</label><select class="fc-select" wire:model.live="constituencyId"><option value="">All Assemblies</option>@foreach($this->constituencies as $constituency)<option value="{{ $constituency->id }}">{{ $constituency->name }}</option>@endforeach</select></div>
                <div class="fc-field"><label>Budget Category</label><select class="fc-select" wire:model.live="category"><option value="">All Categories</option>@foreach(\App\Models\CampaignBudget::CATEGORIES as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></div>
            </div>
        </section>

        <div class="fc-stats">
            <div class="fc-stat"><span>Total Allocated</span><strong>Rs. {{ number_format($stats['allocated'], 0) }}</strong></div>
            <div class="fc-stat"><span>Approved Spend</span><strong>Rs. {{ number_format($stats['approved'], 0) }}</strong></div>
            <div class="fc-stat"><span>Available</span><strong>Rs. {{ number_format($stats['available'], 0) }}</strong></div>
            <div class="fc-stat"><span>Budget Used</span><strong>{{ $stats['utilization'] }}%</strong></div>
            <div class="fc-stat"><span>Awaiting Approval</span><strong>{{ number_format($stats['pending_count']) }}</strong><small>Rs. {{ number_format($stats['pending_amount'], 0) }}</small></div>
            <div class="fc-stat"><span>Approved Unpaid</span><strong>Rs. {{ number_format($stats['unpaid'], 0) }}</strong></div>
        </div>

        <div class="fc-layout">
            <section class="fc-panel">
                <div class="fc-panel-head"><div class="fc-panel-title">Budget Utilization</div><a class="fc-link" href="{{ $this->budgetIndexUrl() }}">Manage all</a></div>
                @forelse($budgets as $budget)
                    <div class="fc-budget"><div class="fc-row"><div><div class="fc-budget-name">{{ $budget->title }}</div><div class="fc-meta">{{ $budget->constituency?->name }} · {{ $budget->category }} · {{ $budget->status }}</div></div><div class="fc-money">Rs. {{ number_format($budget->approved_spent, 0) }} / {{ number_format((float) $budget->allocated_amount, 0) }}</div></div><div class="fc-track"><i style="width:{{ $budget->utilization_percent }}%"></i></div><div class="fc-progress-label"><span>{{ $budget->utilization_percent }}% utilized</span><span>Rs. {{ number_format($budget->available_amount, 0) }} available</span></div></div>
                @empty<div class="fc-empty">No budget allocations found for the selected filters.</div>@endforelse
            </section>
            <aside class="fc-panel">
                <div class="fc-panel-head"><div class="fc-panel-title">Approved Spend by Category</div></div>
                @forelse($categories as $item)<div class="fc-cat"><div class="fc-cat-label"><span>{{ $item->category }}</span><strong>Rs. {{ number_format((float) $item->total_amount, 0) }}</strong></div><div class="fc-track"><i style="width:{{ round(((float) $item->total_amount / $maxCategory) * 100) }}%"></i></div></div>@empty<div class="fc-empty">Approved expenses will appear here.</div>@endforelse
            </aside>
        </div>

        <section class="fc-panel">
            <div class="fc-panel-head"><div class="fc-panel-title">Recent Expense & Approval Queue</div><a class="fc-link" href="{{ $this->expenseIndexUrl() }}">Open expense register</a></div>
            @forelse($this->recentExpenses as $expense)
                <a class="fc-expense" href="{{ $this->expenseUrl($expense) }}"><div class="fc-date">{{ $expense->expense_date->format('d M Y') }}<br>{{ $expense->expense_code }}</div><div><div class="fc-expense-name">{{ $expense->description }}</div><div class="fc-meta">{{ $expense->constituency?->name }} · {{ $expense->budget?->title ?? 'Unallocated' }} · {{ $expense->vendor ?: 'No vendor' }} · By {{ $expense->submitter?->name ?? 'System' }}</div></div><div class="fc-money">Rs. {{ number_format((float) $expense->amount, 0) }}</div><span class="fc-badge {{ strtolower($expense->approval_status) }}">{{ $expense->approval_status }}</span></a>
            @empty<div class="fc-empty">No expenses submitted yet.</div>@endforelse
        </section>
    </div>
</x-filament-panels::page>
