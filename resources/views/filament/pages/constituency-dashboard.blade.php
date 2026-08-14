<x-filament-panels::page>

    @php
        $summary = $this->summary ?? [];
        $constituency = $this->selectedConstituency;
    @endphp

    <style>
        /* =========================================================
           CONSTITUENCY DASHBOARD — LIGHT / WHITE THEME
        ========================================================= */

        .cd-wrapper {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .cd-grid {
            display: grid;
            gap: 16px;
        }

        .cd-grid-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        /* Cards */

        .cd-card {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 20px;
            background: #ffffff;
            min-width: 0;
            box-shadow:
                0 1px 2px rgba(15, 23, 42, 0.04);
            transition:
                transform 0.18s ease,
                box-shadow 0.18s ease,
                border-color 0.18s ease;
        }

        .cd-card:hover {
            transform: translateY(-2px);
            border-color: #cbd5e1;
            box-shadow:
                0 8px 24px rgba(15, 23, 42, 0.07);
        }

        .cd-icon {
            font-size: 25px;
            line-height: 1;
            margin-bottom: 11px;
        }

        .cd-label {
            font-size: 13px;
            font-weight: 500;
            color: #64748b;
        }

        .cd-number {
            font-size: 30px;
            line-height: 1.2;
            font-weight: 800;
            margin-top: 7px;
            color: #0f172a;
        }

        .cd-subtitle {
            font-size: 12px;
            margin-top: 6px;
            color: #94a3b8;
        }

        .cd-center {
            text-align: center;
        }

        .cd-value {
            font-size: 27px;
            font-weight: 800;
            margin-top: 7px;
            color: #0f172a;
        }

        /* =========================================================
           SELECTOR
        ========================================================= */

        .cd-selector {
            background:
                linear-gradient(
                    135deg,
                    #ffffff 0%,
                    #f8fafc 100%
                );
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 22px;
            box-shadow:
                0 4px 18px rgba(15, 23, 42, 0.04);
        }

        .cd-selector-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
        }

        .cd-select {
            width: 100%;
            border-radius: 10px;
            padding: 11px 13px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            outline: none;
            transition:
                border-color 0.15s ease,
                box-shadow 0.15s ease;
        }

        .cd-select:focus {
            border-color: #8b5cf6;
            box-shadow:
                0 0 0 3px rgba(139, 92, 246, 0.12);
        }

        /* =========================================================
           SECTION
        ========================================================= */

        .cd-section {
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            background: #ffffff;
            overflow: hidden;
            box-shadow:
                0 2px 8px rgba(15, 23, 42, 0.035);
        }

        .cd-section-header {
            padding: 18px 20px;
            border-bottom: 1px solid #eef2f7;
            background: #ffffff;
        }

        .cd-section-title {
            font-size: 16px;
            font-weight: 750;
            color: #0f172a;
        }

        .cd-section-description {
            margin-top: 4px;
            font-size: 12px;
            color: #94a3b8;
        }

        .cd-section-body {
            padding: 16px;
        }

        /* =========================================================
           PARTY
        ========================================================= */

        .cd-party-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .cd-party-total {
            font-size: 30px;
            font-weight: 800;
            color: #0f172a;
        }

        .cd-party-name {
            font-size: 16px;
            font-weight: 750;
            margin-top: 5px;
            color: #0f172a;
        }

        .cd-party-full-name {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 3px;
        }

        .cd-party-list {
            margin-top: 18px;
            display: grid;
            gap: 0;
        }

        .cd-party-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-size: 13px;
            padding: 10px 0;
            border-bottom: 1px solid #eef2f7;
        }

        .cd-party-row:last-child {
            border-bottom: 0;
        }

        .cd-party-count {
            font-weight: 750;
            color: #334155;
        }

        /* =========================================================
           TABLE
        ========================================================= */

        .cd-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .cd-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
        }

        .cd-table th,
        .cd-table td {
            padding: 13px 12px;
            border-bottom: 1px solid #eef2f7;
            text-align: left;
            font-size: 13px;
            white-space: nowrap;
        }

        .cd-table th {
            color: #64748b;
            font-weight: 700;
            background: #f8fafc;
        }

        .cd-table td {
            color: #334155;
        }

        .cd-table tbody tr {
            transition: background 0.15s ease;
        }

        .cd-table tbody tr:hover {
            background: #f8fafc;
        }

        .cd-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .cd-table-voter {
            font-weight: 700;
            color: #0f172a;
        }

        /* =========================================================
           INFO
        ========================================================= */

        .cd-info-value {
            font-size: 15px;
            font-weight: 650;
            margin-top: 5px;
            color: #0f172a;
        }

        /* =========================================================
           STATUS
        ========================================================= */

        .cd-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .cd-status-active {
            color: #15803d;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }

        .cd-status-inactive {
            color: #b91c1c;
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        /* =========================================================
           SUPPORT CARDS
        ========================================================= */

        .cd-support-card {
            position: relative;
            overflow: hidden;
        }

        .cd-support-card::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
        }

        .cd-support-strong::after {
            background: #22c55e;
        }

        .cd-support-moderate::after {
            background: #84cc16;
        }

        .cd-support-leaning::after {
            background: #eab308;
        }

        .cd-support-neutral::after {
            background: #94a3b8;
        }

        .cd-support-undecided::after {
            background: #f97316;
        }

        .cd-support-opposition::after {
            background: #ef4444;
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 1100px) {
            .cd-grid-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .cd-grid-4 {
                grid-template-columns: 1fr;
            }

            .cd-card {
                padding: 17px;
            }

            .cd-number {
                font-size: 26px;
            }

            .cd-table {
                min-width: 750px;
            }
        }
    </style>


    <div class="cd-wrapper">


        {{-- =========================================================
             CONSTITUENCY SELECTOR
        ========================================================== --}}

        <div class="cd-selector">

            <div style="
                font-size:20px;
                font-weight:800;
                color:#0f172a;
                margin-bottom:4px;
            ">
                🏛️ Constituency Dashboard
            </div>

            <div style="
                font-size:13px;
                color:#94a3b8;
                margin-bottom:18px;
            ">
                Select a constituency to view complete campaign intelligence.
            </div>

            <div style="max-width:650px;">

                <label
                    for="constituency-select"
                    class="cd-selector-label"
                >
                    Select Constituency
                </label>

                <select
                    id="constituency-select"
                    wire:model.live="constituencyId"
                    class="cd-select"
                >

                    <option value="">
                        -- Select Constituency --
                    </option>

                    @foreach ($this->constituencies as $item)

                        <option value="{{ $item->id }}">
                            {{ $item->name }}

                            @if ($item->district)
                                — {{ $item->district }}
                            @endif
                        </option>

                    @endforeach

                </select>

            </div>

        </div>


        @if ($constituency)


            {{-- =====================================================
                 MAIN OVERVIEW
            ====================================================== --}}

            <div class="cd-grid cd-grid-4">

                <div class="cd-card">

                    <div class="cd-icon">🏘️</div>

                    <div class="cd-label">
                        Villages
                    </div>

                    <div class="cd-number">
                        {{ number_format($summary['villages'] ?? 0) }}
                    </div>

                    <div class="cd-subtitle">
                        Total Villages
                    </div>

                </div>


                <div class="cd-card">

                    <div class="cd-icon">🗳️</div>

                    <div class="cd-label">
                        Booths
                    </div>

                    <div class="cd-number">
                        {{ number_format($summary['booths'] ?? 0) }}
                    </div>

                    <div class="cd-subtitle">
                        Total Booths
                    </div>

                </div>


                <div class="cd-card">

                    <div class="cd-icon">🏠</div>

                    <div class="cd-label">
                        Houses
                    </div>

                    <div class="cd-number">
                        {{ number_format($summary['houses'] ?? 0) }}
                    </div>

                    <div class="cd-subtitle">
                        Total Houses
                    </div>

                </div>


                <div class="cd-card">

                    <div class="cd-icon">👥</div>

                    <div class="cd-label">
                        Voters
                    </div>

                    <div class="cd-number">
                        {{ number_format($summary['voters'] ?? 0) }}
                    </div>

                    <div class="cd-subtitle">
                        Total Voters
                    </div>

                </div>

            </div>


            {{-- =====================================================
                 CAMPAIGN PEOPLE
            ====================================================== --}}

            <div class="cd-grid cd-grid-4">

                <div class="cd-card">

                    <div class="cd-icon">🙋</div>

                    <div class="cd-label">
                        Volunteers
                    </div>

                    <div class="cd-number">
                        {{ number_format($summary['volunteers'] ?? 0) }}
                    </div>

                    <div class="cd-subtitle">
                        Constituency Volunteers
                    </div>

                </div>


                <div class="cd-card">

                    <div class="cd-icon">⭐</div>

                    <div class="cd-label">
                        Influencers
                    </div>

                    <div class="cd-number">
                        {{ number_format($summary['influencers'] ?? 0) }}
                    </div>

                    <div class="cd-subtitle">
                        Constituency Influencers
                    </div>

                </div>


                <div class="cd-card">

                    <div class="cd-icon">✅</div>

                    <div class="cd-label">
                        Active Voters
                    </div>

                    <div class="cd-number">
                        {{ number_format($summary['active_voters'] ?? 0) }}
                    </div>

                    <div class="cd-subtitle">
                        Active Electoral Roll
                    </div>

                </div>


                <div class="cd-card cd-support-card cd-support-undecided">

                    <div class="cd-icon">⏳</div>

                    <div class="cd-label">
                        Undecided
                    </div>

                    <div class="cd-number">
                        {{ number_format($summary['undecided'] ?? 0) }}
                    </div>

                    <div class="cd-subtitle">
                        Undecided Voters
                    </div>

                </div>

            </div>


            {{-- =====================================================
                 VOTER DEMOGRAPHICS
            ====================================================== --}}

            <div class="cd-section">

                <div class="cd-section-header">

                    <div class="cd-section-title">
                        👥 Voter Demographics
                    </div>

                    <div class="cd-section-description">
                        Demographic distribution of voters across the constituency.
                    </div>

                </div>

                <div class="cd-section-body">

                    <div class="cd-grid cd-grid-4">

                        <div class="cd-card cd-center">

                            <div class="cd-label">
                                Total Voters
                            </div>

                            <div class="cd-value">
                                {{ number_format($summary['voters'] ?? 0) }}
                            </div>

                        </div>


                        <div class="cd-card cd-center">

                            <div class="cd-label">
                                Male
                            </div>

                            <div class="cd-value">
                                {{ number_format($summary['male'] ?? 0) }}
                            </div>

                        </div>


                        <div class="cd-card cd-center">

                            <div class="cd-label">
                                Female
                            </div>

                            <div class="cd-value">
                                {{ number_format($summary['female'] ?? 0) }}
                            </div>

                        </div>


                        <div class="cd-card cd-center">

                            <div class="cd-label">
                                Other
                            </div>

                            <div class="cd-value">
                                {{ number_format($summary['other'] ?? 0) }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 POLITICAL OVERVIEW
            ====================================================== --}}

            <div class="cd-section">

                <div class="cd-section-header">

                    <div class="cd-section-title">
                        📊 Political Overview
                    </div>

                    <div class="cd-section-description">
                        Complete voter sentiment and support distribution.
                    </div>

                </div>

                <div class="cd-section-body">

                    <div class="cd-grid cd-grid-4">


                        <div class="cd-card cd-center cd-support-card cd-support-strong">
                            <div class="cd-label">Strong Support</div>
                            <div class="cd-value">
                                {{ number_format($summary['strong_support'] ?? 0) }}
                            </div>
                        </div>


                        <div class="cd-card cd-center cd-support-card cd-support-moderate">
                            <div class="cd-label">Moderate Support</div>
                            <div class="cd-value">
                                {{ number_format($summary['moderate_support'] ?? 0) }}
                            </div>
                        </div>


                        <div class="cd-card cd-center cd-support-card cd-support-leaning">
                            <div class="cd-label">Leaning Support</div>
                            <div class="cd-value">
                                {{ number_format($summary['leaning_support'] ?? 0) }}
                            </div>
                        </div>


                        <div class="cd-card cd-center cd-support-card cd-support-neutral">
                            <div class="cd-label">Neutral</div>
                            <div class="cd-value">
                                {{ number_format($summary['neutral'] ?? 0) }}
                            </div>
                        </div>


                        <div class="cd-card cd-center cd-support-card cd-support-undecided">
                            <div class="cd-label">Undecided</div>
                            <div class="cd-value">
                                {{ number_format($summary['undecided'] ?? 0) }}
                            </div>
                        </div>


                        <div class="cd-card cd-center cd-support-card cd-support-opposition">
                            <div class="cd-label">Leaning Opposition</div>
                            <div class="cd-value">
                                {{ number_format($summary['leaning_opposition'] ?? 0) }}
                            </div>
                        </div>


                        <div class="cd-card cd-center cd-support-card cd-support-opposition">
                            <div class="cd-label">Moderate Opposition</div>
                            <div class="cd-value">
                                {{ number_format($summary['moderate_opposition'] ?? 0) }}
                            </div>
                        </div>


                        <div class="cd-card cd-center cd-support-card cd-support-opposition">
                            <div class="cd-label">Strong Opposition</div>
                            <div class="cd-value">
                                {{ number_format($summary['strong_opposition'] ?? 0) }}
                            </div>
                        </div>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 PARTY SUPPORT
            ====================================================== --}}

            <div class="cd-section">

                <div class="cd-section-header">

                    <div class="cd-section-title">
                        🗳️ Party Support
                    </div>

                    <div class="cd-section-description">
                        Party-wise voter support and opposition distribution.
                    </div>

                </div>

                <div class="cd-section-body">

                    <div class="cd-grid cd-grid-4">

                        @forelse ($summary['parties'] ?? [] as $party)

                            <div class="cd-card">

                                <div class="cd-party-header">

                                    <div>

                                        <div class="cd-icon">
                                            {{ $party['symbol'] ?? '🏛️' }}
                                        </div>

                                        <div class="cd-party-name">
                                            {{ $party['short_name'] ?? '-' }}
                                        </div>

                                        <div class="cd-party-full-name">
                                            {{ $party['name'] ?? '-' }}
                                        </div>

                                    </div>

                                    <div class="cd-party-total">
                                        {{ number_format($party['total'] ?? 0) }}
                                    </div>

                                </div>


                                <div class="cd-party-list">

                                    @foreach([
                                        'strong_support' => 'Strong Support',
                                        'moderate_support' => 'Moderate Support',
                                        'leaning_support' => 'Leaning Support',
                                        'neutral' => 'Neutral',
                                        'undecided' => 'Undecided',
                                        'leaning_opposition' => 'Leaning Opposition',
                                        'moderate_opposition' => 'Moderate Opposition',
                                        'strong_opposition' => 'Strong Opposition',
                                    ] as $key => $label)

                                        <div class="cd-party-row">

                                            <span>
                                                {{ $label }}
                                            </span>

                                            <span class="cd-party-count">
                                                {{ number_format($party[$key] ?? 0) }}
                                            </span>

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        @empty

                            <div class="cd-card">
                                No active political parties found.
                            </div>

                        @endforelse

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 VILLAGE-WISE SUMMARY
            ====================================================== --}}

            <div class="cd-section">

                <div class="cd-section-header">

                    <div class="cd-section-title">
                        🏘️ Village-wise Summary
                    </div>

                    <div class="cd-section-description">
                        Village-level campaign strength and voter distribution.
                    </div>

                </div>

                <div class="cd-table-wrapper">

                    <table class="cd-table">

                        <thead>

                            <tr>
                                <th>#</th>
                                <th>Village</th>
                                <th>Taluka</th>
                                <th>Booths</th>
                                <th>Houses</th>
                                <th>Voters</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse ($summary['villages_list'] ?? [] as $index => $village)

                                <tr>

                                    <td>
                                        {{ $index + 1 }}
                                    </td>

                                    <td class="cd-table-voter">
                                        {{ $village['name'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $village['taluka'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ number_format($village['booths'] ?? 0) }}
                                    </td>

                                    <td>
                                        {{ number_format($village['houses'] ?? 0) }}
                                    </td>

                                    <td class="cd-table-voter">
                                        {{ number_format($village['voters'] ?? 0) }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6"
                                        style="
                                            text-align:center;
                                            padding:30px;
                                            color:#94a3b8;
                                        "
                                    >
                                        No villages found.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- =====================================================
                 CONSTITUENCY INFORMATION
            ====================================================== --}}

            <div class="cd-section">

                <div class="cd-section-header">

                    <div class="cd-section-title">
                        🏛️ Constituency Information
                    </div>

                    <div class="cd-section-description">
                        Basic constituency information and operational status.
                    </div>

                </div>

                <div class="cd-section-body">

                    <div class="cd-grid cd-grid-4">


                        <div class="cd-card">

                            <div class="cd-label">
                                Constituency
                            </div>

                            <div class="cd-info-value">
                                {{ $constituency->name ?? '-' }}
                            </div>

                        </div>


                        <div class="cd-card">

                            <div class="cd-label">
                                District
                            </div>

                            <div class="cd-info-value">
                                {{ $constituency->district ?? '-' }}
                            </div>

                        </div>


                        <div class="cd-card">

                            <div class="cd-label">
                                State
                            </div>

                            <div class="cd-info-value">
                                {{ $constituency->state ?? '-' }}
                            </div>

                        </div>


                        <div class="cd-card">

                            <div class="cd-label">
                                Status
                            </div>

                            <div class="cd-info-value">

                                @if($constituency->is_active ?? false)

                                    <span class="cd-status cd-status-active">
                                        ● Active
                                    </span>

                                @else

                                    <span class="cd-status cd-status-inactive">
                                        ● Inactive
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            </div>


        @else


            {{-- =====================================================
                 EMPTY STATE
            ====================================================== --}}

            <div class="cd-section">

                <div style="
                    padding:70px 20px;
                    text-align:center;
                ">

                    <div style="
                        font-size:52px;
                        margin-bottom:16px;
                    ">
                        🏛️
                    </div>

                    <div style="
                        font-size:22px;
                        font-weight:800;
                        color:#0f172a;
                    ">
                        Constituency Dashboard
                    </div>

                    <div style="
                        margin-top:8px;
                        font-size:14px;
                        color:#94a3b8;
                    ">
                        Select a constituency above to view complete
                        campaign intelligence.
                    </div>

                </div>

            </div>


        @endif

    </div>

</x-filament-panels::page>