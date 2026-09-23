<x-filament-panels::page>
    @php($stats = $this->stats)

    <style>
        .hr-shell { display:grid; gap:1.25rem; }
        .hr-hero { position:relative; overflow:hidden; border-radius:1.5rem; padding:1.6rem; color:white; background:linear-gradient(120deg,#111016 0%,#2e1065 48%,#7c3aed 100%); box-shadow:0 20px 45px rgba(76,29,149,.24); }
        .hr-hero:after { content:""; position:absolute; width:18rem; height:18rem; border-radius:50%; right:-6rem; top:-9rem; background:rgba(255,255,255,.12); filter:blur(2px); }
        .hr-hero-row { position:relative; z-index:1; display:flex; justify-content:space-between; gap:1rem; align-items:flex-start; flex-wrap:wrap; }
        .hr-eyebrow { margin:0 0 .35rem; color:#ddd6fe; font-size:.76rem; font-weight:800; letter-spacing:.14em; text-transform:uppercase; }
        .hr-title { margin:0; font-size:clamp(1.55rem,3vw,2.25rem); line-height:1.08; font-weight:900; }
        .hr-subtitle { margin:.65rem 0 0; max-width:42rem; color:#ede9fe; }
        .hr-actions { display:flex; gap:.65rem; flex-wrap:wrap; }
        .hr-button { display:inline-flex; align-items:center; justify-content:center; min-height:2.65rem; padding:.68rem 1rem; border-radius:.8rem; font-weight:800; text-decoration:none; transition:.18s ease; }
        .hr-button:hover { transform:translateY(-1px); }
        .hr-button-primary { color:#3b0764; background:white; }
        .hr-button-ghost { color:white; border:1px solid rgba(255,255,255,.35); background:rgba(255,255,255,.08); }
        .hr-filter { position:relative; z-index:1; margin-top:1.2rem; max-width:20rem; }
        .hr-filter label { display:block; margin-bottom:.35rem; color:#ddd6fe; font-size:.75rem; font-weight:800; }
        .hr-filter select { width:100%; border-radius:.75rem; border:1px solid rgba(255,255,255,.25); background:#18111f; color:white; padding:.65rem .8rem; }
        .hr-stats { display:grid; grid-template-columns:repeat(5,minmax(0,1fr)); gap:.9rem; }
        .hr-card { border:1px solid rgb(229 231 235); border-radius:1.15rem; background:white; box-shadow:0 10px 25px rgba(15,23,42,.055); }
        .dark .hr-card { border-color:#302b3b; background:#17141d; box-shadow:none; }
        .hr-stat { padding:1.05rem; }
        .hr-stat-label { color:#6b7280; font-size:.78rem; font-weight:750; }
        .dark .hr-stat-label { color:#aaa3b7; }
        .hr-stat-value { margin-top:.3rem; color:#17111f; font-size:1.55rem; font-weight:900; }
        .dark .hr-stat-value { color:#faf7ff; }
        .hr-grid { display:grid; grid-template-columns:1.05fr .95fr; gap:1rem; }
        .hr-section-head { display:flex; justify-content:space-between; gap:1rem; align-items:center; padding:1rem 1.1rem; border-bottom:1px solid rgb(229 231 235); }
        .dark .hr-section-head { border-color:#302b3b; }
        .hr-section-title { margin:0; color:#1f1628; font-size:1rem; font-weight:900; }
        .dark .hr-section-title { color:#f5f3ff; }
        .hr-link { color:#7c3aed; font-size:.8rem; font-weight:800; text-decoration:none; }
        .hr-list { margin:0; padding:0; list-style:none; }
        .hr-list li { display:flex; justify-content:space-between; gap:1rem; align-items:center; padding:.88rem 1.1rem; border-bottom:1px solid #f1eef5; }
        .dark .hr-list li { border-color:#28232f; }
        .hr-list li:last-child { border-bottom:0; }
        .hr-name { color:#24192d; font-weight:800; }
        .dark .hr-name { color:#f5f3ff; }
        .hr-meta { margin-top:.15rem; color:#81788d; font-size:.75rem; }
        .hr-value { color:#201529; font-weight:900; white-space:nowrap; }
        .dark .hr-value { color:#ede9fe; }
        .hr-badge { display:inline-flex; padding:.27rem .55rem; border-radius:999px; background:#f3e8ff; color:#6b21a8; font-size:.7rem; font-weight:850; }
        .dark .hr-badge { background:#332145; color:#d8b4fe; }
        .hr-empty { padding:1.5rem; text-align:center; color:#81788d; }
        @media (max-width:1100px) { .hr-stats { grid-template-columns:repeat(3,minmax(0,1fr)); } }
        @media (max-width:760px) { .hr-shell { gap:.85rem; } .hr-hero { padding:1.2rem; border-radius:1.15rem; } .hr-actions { width:100%; } .hr-button { flex:1; } .hr-stats { grid-template-columns:repeat(2,minmax(0,1fr)); } .hr-grid { grid-template-columns:1fr; } }
        @media (max-width:430px) { .hr-stats { grid-template-columns:1fr 1fr; gap:.6rem; } .hr-stat { padding:.85rem; } .hr-stat-value { font-size:1.25rem; } }
    </style>

    <div class="hr-shell">
        <section class="hr-hero">
            <div class="hr-hero-row">
                <div>
                    <p class="hr-eyebrow">People · Attendance · Payroll</p>
                    <h2 class="hr-title">HR & Payroll Command Centre</h2>
                    <p class="hr-subtitle">Employee records, attendance, leave approvals and constituency-wise monthly payouts in one secure workspace.</p>
                </div>
                <div class="hr-actions">
                    @can('hr_employee.create')<a class="hr-button hr-button-primary" href="{{ $this->createEmployeeUrl() }}">+ Add Employee</a>@endcan
                    @can('payroll.create')<a class="hr-button hr-button-ghost" href="{{ $this->createPayrollUrl() }}">Create Payroll</a>@endcan
                </div>
            </div>
            @if ($this->constituencies->count() > 1 && ! auth()->user()?->isAssemblyAdmin())
                <div class="hr-filter">
                    <label for="hr-constituency">Assembly Constituency</label>
                    <select id="hr-constituency" wire:model.live="constituencyId">
                        <option value="">All constituencies</option>
                        @foreach ($this->constituencies as $constituency)<option value="{{ $constituency->id }}">{{ $constituency->name }}</option>@endforeach
                    </select>
                </div>
            @endif
        </section>

        <section class="hr-stats">
            <article class="hr-card hr-stat"><div class="hr-stat-label">Active Employees</div><div class="hr-stat-value">{{ number_format($stats['active_employees']) }}</div></article>
            <article class="hr-card hr-stat"><div class="hr-stat-label">Present Today</div><div class="hr-stat-value">{{ number_format($stats['present_today']) }}</div></article>
            <article class="hr-card hr-stat"><div class="hr-stat-label">Pending Leaves</div><div class="hr-stat-value">{{ number_format($stats['pending_leaves']) }}</div></article>
            <article class="hr-card hr-stat"><div class="hr-stat-label">This Month Payout</div><div class="hr-stat-value">₹{{ number_format($stats['month_payout'], 0) }}</div></article>
            <article class="hr-card hr-stat"><div class="hr-stat-label">Payments Pending</div><div class="hr-stat-value">{{ number_format($stats['unpaid_employees']) }}</div></article>
        </section>

        <section class="hr-grid">
            <article class="hr-card">
                <div class="hr-section-head"><h3 class="hr-section-title">Recent Payroll Runs</h3><a class="hr-link" href="{{ $this->payrollUrl() }}">View payroll →</a></div>
                <ul class="hr-list">
                    @forelse ($this->recentPayrolls as $payroll)
                        <li><div><div class="hr-name">{{ $payroll->period_label }} · {{ $payroll->constituency?->name }}</div><div class="hr-meta">{{ $payroll->items_count }} employees · <span class="hr-badge">{{ $payroll->status }}</span></div></div><div class="hr-value">₹{{ number_format((float) $payroll->items_sum_net_pay, 0) }}</div></li>
                    @empty <li class="hr-empty">No payroll run created yet.</li> @endforelse
                </ul>
            </article>

            <article class="hr-card">
                <div class="hr-section-head"><h3 class="hr-section-title">Workforce by Department</h3><a class="hr-link" href="{{ $this->employeesUrl() }}">All employees →</a></div>
                <ul class="hr-list">
                    @forelse ($this->departmentSummary as $department)
                        <li><div class="hr-name">{{ $department->department_name }}</div><div class="hr-value">{{ number_format($department->employee_count) }}</div></li>
                    @empty <li class="hr-empty">Add employees to view workforce distribution.</li> @endforelse
                </ul>
            </article>
        </section>

        <section class="hr-card">
            <div class="hr-section-head"><h3 class="hr-section-title">Latest Leave Requests</h3><div><a class="hr-link" href="{{ $this->attendanceUrl() }}">Attendance</a> · <a class="hr-link" href="{{ $this->leavesUrl() }}">Manage leaves →</a></div></div>
            <ul class="hr-list">
                @forelse ($this->recentLeaves as $leave)
                    <li><div><div class="hr-name">{{ $leave->employee?->name }} <span class="hr-badge">{{ $leave->status }}</span></div><div class="hr-meta">{{ $leave->leave_type }} · {{ $leave->starts_on?->format('d M') }} to {{ $leave->ends_on?->format('d M Y') }} · {{ $leave->days }} day(s)</div></div><div class="hr-value">{{ $leave->employee?->employee_code }}</div></li>
                @empty <li class="hr-empty">No leave requests found.</li> @endforelse
            </ul>
        </section>
    </div>
</x-filament-panels::page>
