<x-filament-panels::page>

@php

    $summary = $this->summary ?? [];

    $constituency = $this->selectedConstituency;

    $aiWarRoom = $this->aiWarRoom ?? [];

    $parties = $summary['parties'] ?? [];

    $villagesList = $summary['villages_list'] ?? [];

    $boothRisk = collect($aiWarRoom['booth_risk'] ?? []);

    $highRiskBooths = $boothRisk->where('priority', 'HIGH');

    $swingVillages = collect($aiWarRoom['swing_villages'] ?? [])
        ->take(10);

@endphp


<style>

/* =========================================================
   CONSTITUENCY DASHBOARD
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


/* =========================================================
   CARD
========================================================= */

.cd-card {
    position: relative;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 20px;
    min-width: 0;
    box-shadow: 0 1px 3px rgba(15, 23, 42, .05);
    transition: .18s ease;
}

.cd-card:hover {
    transform: translateY(-2px);
    border-color: #cbd5e1;
    box-shadow: 0 8px 24px rgba(15, 23, 42, .08);
}

.cd-icon {
    font-size: 26px;
    line-height: 1;
    margin-bottom: 10px;
}

.cd-label {
    font-size: 13px;
    color: #64748b;
    font-weight: 600;
}

.cd-number {
    font-size: 30px;
    line-height: 1.2;
    font-weight: 800;
    color: #0f172a;
    margin-top: 6px;
}

.cd-value {
    font-size: 22px;
    line-height: 1.35;
    font-weight: 800;
    color: #0f172a;
    margin-top: 6px;
}

.cd-subtitle {
    font-size: 12px;
    color: #94a3b8;
    margin-top: 6px;
}


/* =========================================================
   SELECTOR
========================================================= */

.cd-selector {
    background: linear-gradient(
        135deg,
        #ffffff 0%,
        #f8fafc 100%
    );
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 22px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
}

.cd-selector-title {
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
}

.cd-selector-description {
    font-size: 13px;
    color: #94a3b8;
    margin-top: 5px;
    margin-bottom: 18px;
}

.cd-select {
    width: 100%;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 11px 13px;
    background: #ffffff;
    color: #0f172a;
    outline: none;
}

.cd-select:focus {
    border-color: #94a3b8;
    box-shadow: 0 0 0 3px rgba(148, 163, 184, .15);
}


/* =========================================================
   SECTION
========================================================= */

.cd-section {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(15, 23, 42, .035);
}

.cd-section-header {
    padding: 18px 20px;
    border-bottom: 1px solid #eef2f7;
}

.cd-section-title {
    font-size: 16px;
    font-weight: 800;
    color: #0f172a;
}

.cd-section-description {
    font-size: 12px;
    color: #94a3b8;
    margin-top: 5px;
}

.cd-section-body {
    padding: 16px;
}


/* =========================================================
   STATUS
========================================================= */

.cd-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
}

.cd-status-active {
    background: #f0fdf4;
    color: #15803d;
    border: 1px solid #bbf7d0;
}

.cd-status-inactive {
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}

.cd-status-warning {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
}


/* =========================================================
   SUPPORT CARDS
========================================================= */

.cd-support-card {
    position: relative;
    overflow: hidden;
}

.cd-support-card::after {
    content: "";
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
    background: #f8fafc;
    color: #64748b;
    font-weight: 700;
}

.cd-table td {
    color: #334155;
}

.cd-table tbody tr:hover {
    background: #f8fafc;
}

.cd-table-voter {
    font-weight: 700;
    color: #0f172a !important;
}


/* =========================================================
   BOOTH ACTION BUTTON
========================================================= */

.cd-booth-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 7px 12px;
    border-radius: 8px;
    background: #0f172a;
    color: #ffffff !important;
    text-decoration: none !important;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
    transition: .15s ease;
}

.cd-booth-action:hover {
    background: #334155;
    color: #ffffff !important;
    transform: translateY(-1px);
}


/* =========================================================
   PARTY
========================================================= */

.cd-party-name {
    font-size: 16px;
    font-weight: 800;
    color: #0f172a;
    margin-top: 5px;
}

.cd-party-full-name {
    font-size: 12px;
    color: #94a3b8;
    margin-top: 3px;
}

.cd-party-list {
    margin-top: 15px;
}

.cd-party-row {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    padding: 9px 0;
    border-bottom: 1px solid #eef2f7;
    font-size: 13px;
}

.cd-party-row:last-child {
    border-bottom: 0;
}

.cd-party-count {
    font-weight: 800;
    color: #334155;
}


/* =========================================================
   EMPTY
========================================================= */

.cd-empty {
    text-align: center;
    padding: 50px 20px;
    color: #94a3b8;
}

.cd-empty-icon {
    font-size: 45px;
    margin-bottom: 12px;
}

.cd-empty-title {
    font-size: 18px;
    font-weight: 800;
    color: #334155;
}

.cd-empty-description {
    margin-top: 5px;
    font-size: 13px;
}


/* =========================================================
   DARK THEME — PURPLE / CHARCOAL
========================================================= */

html.dark .cd-selector {
    border-color: rgba(139, 92, 246, .34);
    background: linear-gradient(135deg, #08070d 0%, #151022 55%, #24103b 100%);
    box-shadow: 0 18px 42px rgba(0, 0, 0, .34), 0 0 35px rgba(124, 58, 237, .08);
}

html.dark .cd-selector-title,
html.dark .cd-section-title,
html.dark .cd-number,
html.dark .cd-value,
html.dark .cd-party-name,
html.dark .cd-party-count,
html.dark .cd-table-voter,
html.dark .cd-empty-title {
    color: #f8fafc !important;
}

html.dark .cd-selector-description,
html.dark .cd-section-description,
html.dark .cd-label,
html.dark .cd-party-full-name,
html.dark .cd-empty,
html.dark .cd-empty-description {
    color: #a8a3b7;
}

html.dark .cd-subtitle {
    color: #777184;
}

html.dark .cd-select {
    color: #f8fafc;
    border-color: rgba(139, 92, 246, .32);
    background: #100d17;
    color-scheme: dark;
}

html.dark .cd-select:focus {
    border-color: #a78bfa;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, .20);
}

html.dark .cd-section {
    border-color: rgba(139, 92, 246, .25);
    background: linear-gradient(145deg, #09080e, #100c19);
    box-shadow: 0 15px 38px rgba(0, 0, 0, .28);
}

html.dark .cd-section-header {
    border-color: rgba(139, 92, 246, .20);
    background: linear-gradient(90deg, #0d0b13, #171021);
}

html.dark .cd-section-body {
    background: rgba(7, 6, 11, .42);
}

html.dark .cd-card {
    border-color: rgba(139, 92, 246, .22);
    background: linear-gradient(145deg, #121018 0%, #191126 100%);
    box-shadow: 0 7px 18px rgba(0, 0, 0, .22);
}

html.dark .cd-card:hover {
    border-color: rgba(167, 139, 250, .62);
    background: linear-gradient(145deg, #171221, #25143b);
    box-shadow: 0 12px 28px rgba(76, 29, 149, .22);
}

html.dark .cd-table {
    background: transparent;
}

html.dark .cd-table th {
    color: #c4b5fd;
    border-color: rgba(139, 92, 246, .20);
    background: #151020;
}

html.dark .cd-table td {
    color: #d1cbdc;
    border-color: rgba(148, 163, 184, .12);
}

html.dark .cd-table tbody tr:hover {
    background: rgba(124, 58, 237, .12);
}

html.dark .cd-party-row {
    color: #c4bfce;
    border-color: rgba(148, 163, 184, .12);
}

html.dark .cd-booth-action {
    border: 1px solid rgba(167, 139, 250, .34);
    background: linear-gradient(135deg, #6d28d9, #3b1764);
}

html.dark .cd-booth-action:hover {
    background: linear-gradient(135deg, #7c3aed, #4c1d95);
    box-shadow: 0 8px 18px rgba(76, 29, 149, .30);
}

html.dark .cd-status-active {
    color: #86efac;
    border-color: rgba(34, 197, 94, .30);
    background: rgba(22, 101, 52, .20);
}

html.dark .cd-status-inactive {
    color: #fca5a5;
    border-color: rgba(239, 68, 68, .30);
    background: rgba(153, 27, 27, .20);
}

html.dark .cd-status-warning {
    color: #fcd34d;
    border-color: rgba(245, 158, 11, .30);
    background: rgba(146, 64, 14, .20);
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media(max-width:1100px) {

    .cd-grid-4 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

}

@media(max-width:640px) {

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
        min-width: 1000px;
    }

}

</style>



<div class="cd-wrapper">


{{-- =========================================================
     CONSTITUENCY SELECTOR
========================================================= --}}

<div class="cd-selector">

    <div class="cd-selector-title">
        🏛️ Constituency Dashboard
    </div>

    <div class="cd-selector-description">
        Select constituency to view complete campaign intelligence.
    </div>


    <select
        wire:model.live="constituencyId"
        class="cd-select"
    >

        <option value="">
            -- Select Constituency --
        </option>


        @foreach($this->constituencies as $item)

            <option value="{{ $item->id }}">

                {{ $item->name }}

                @if($item->district)
                    — {{ $item->district }}
                @endif

            </option>

        @endforeach

    </select>

</div>



@if($constituency)


{{-- =========================================================
     MAIN OVERVIEW
========================================================= --}}

<div class="cd-grid cd-grid-4">


    <div class="cd-card">

        <div class="cd-icon">
            🏘️
        </div>

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

        <div class="cd-icon">
            🗳️
        </div>

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

        <div class="cd-icon">
            🏠
        </div>

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

        <div class="cd-icon">
            👥
        </div>

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



{{-- =========================================================
     CAMPAIGN PEOPLE
========================================================= --}}

<div class="cd-grid cd-grid-4">


    <div class="cd-card">

        <div class="cd-icon">
            🙋
        </div>

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

        <div class="cd-icon">
            ⭐
        </div>

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

        <div class="cd-icon">
            ✅
        </div>

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

        <div class="cd-icon">
            ⏳
        </div>

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



{{-- =========================================================
     VOTER DEMOGRAPHICS
========================================================= --}}

<div class="cd-section">

    <div class="cd-section-header">

        <div class="cd-section-title">
            👥 Voter Demographics
        </div>

        <div class="cd-section-description">
            Gender-wise voter distribution.
        </div>

    </div>


    <div class="cd-section-body">

        <div class="cd-grid cd-grid-4">


            <div class="cd-card">

                <div class="cd-label">
                    Male
                </div>

                <div class="cd-number">
                    {{ number_format($summary['male'] ?? 0) }}
                </div>

            </div>



            <div class="cd-card">

                <div class="cd-label">
                    Female
                </div>

                <div class="cd-number">
                    {{ number_format($summary['female'] ?? 0) }}
                </div>

            </div>



            <div class="cd-card">

                <div class="cd-label">
                    Other
                </div>

                <div class="cd-number">
                    {{ number_format($summary['other'] ?? 0) }}
                </div>

            </div>



            <div class="cd-card">

                <div class="cd-label">
                    Total
                </div>

                <div class="cd-number">
                    {{ number_format($summary['voters'] ?? 0) }}
                </div>

            </div>


        </div>

    </div>

</div>



{{-- =========================================================
     POLITICAL OVERVIEW
========================================================= --}}

<div class="cd-section">

    <div class="cd-section-header">

        <div class="cd-section-title">
            📊 Political Overview
        </div>

        <div class="cd-section-description">
            Complete voter support distribution.
        </div>

    </div>


    <div class="cd-section-body">

        <div class="cd-grid cd-grid-4">


            @php

                $supportTypes = [

                    'strong_support' => '🟢 Strong Support',

                    'moderate_support' => '🟢 Moderate Support',

                    'leaning_support' => '🟡 Leaning Support',

                    'neutral' => '⚪ Neutral',

                    'undecided' => '🟠 Undecided',

                    'leaning_opposition' => '🔴 Leaning Opposition',

                    'moderate_opposition' => '🔴 Moderate Opposition',

                    'strong_opposition' => '🔴 Strong Opposition',

                ];

            @endphp


            @foreach($supportTypes as $key => $label)

                <div class="cd-card">

                    <div class="cd-label">
                        {{ $label }}
                    </div>

                    <div class="cd-number">
                        {{ number_format($summary[$key] ?? 0) }}
                    </div>

                </div>

            @endforeach


        </div>

    </div>

</div>



{{-- =========================================================
     AI WAR ROOM
========================================================= --}}

<div class="cd-section">

    <div class="cd-section-header">

        <div class="cd-section-title">
            🤖 AI War Room Intelligence
        </div>

        <div class="cd-section-description">
            AI-based booth risk analysis, swing villages and strategic priority.
        </div>

    </div>


    <div class="cd-section-body">


        {{-- =====================================================
             AI SUMMARY
        ====================================================== --}}

        <div class="cd-grid cd-grid-4">


            <div class="cd-card cd-support-card cd-support-opposition">

                <div class="cd-icon">
                    🚨
                </div>

                <div class="cd-label">
                    High Risk Booths
                </div>

                <div class="cd-number">
                    {{ number_format($aiWarRoom['high_risk_booths'] ?? 0) }}
                </div>

                <div class="cd-subtitle">
                    Immediate attention
                </div>

            </div>



            <div class="cd-card cd-support-card cd-support-undecided">

                <div class="cd-icon">
                    ⚠️
                </div>

                <div class="cd-label">
                    Medium Risk Booths
                </div>

                <div class="cd-number">
                    {{ number_format($aiWarRoom['medium_risk_booths'] ?? 0) }}
                </div>

                <div class="cd-subtitle">
                    Monitoring required
                </div>

            </div>



            <div class="cd-card cd-support-card cd-support-strong">

                <div class="cd-icon">
                    ✅
                </div>

                <div class="cd-label">
                    Safe Booths
                </div>

                <div class="cd-number">
                    {{ number_format($aiWarRoom['safe_booths'] ?? 0) }}
                </div>

                <div class="cd-subtitle">
                    Strong position
                </div>

            </div>



            <div class="cd-card">

                <div class="cd-icon">
                    🧠
                </div>

                <div class="cd-label">
                    AI Mode
                </div>

                <div class="cd-value">
                    {{ $aiWarRoom['war_mode'] ?? 'STABLE' }}
                </div>

                <div class="cd-subtitle">
                    Strategic status
                </div>

            </div>


        </div>



        <br>



        {{-- =====================================================
             HIGH RISK BOOTH INTELLIGENCE
        ====================================================== --}}

        <div class="cd-section-title">
            🚨 High Risk Booth Intelligence
        </div>

        <div style="margin-top:12px;">

            <div class="cd-table-wrapper">

                <table class="cd-table">

                    <thead>

                        <tr>

                            <th>
                                Booth
                            </th>

                            <th>
                                Village
                            </th>

                            <th>
                                Voters
                            </th>

                            <th>
                                Neutral
                            </th>

                            <th>
                                Support
                            </th>

                            <th>
                                Risk Score
                            </th>

                            <th>
                                Priority
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    @if($highRiskBooths->count() > 0)


                        @foreach($highRiskBooths as $booth)

                            <tr>

                                {{-- BOOTH --}}

                                <td class="cd-table-voter">

                                    {{ $booth['booth_name'] ?? '-' }}

                                </td>


                                {{-- VILLAGE --}}

                                <td>

                                    {{ $booth['village'] ?? '-' }}

                                </td>


                                {{-- VOTERS --}}

                                <td>

                                    {{ number_format($booth['voters'] ?? 0) }}

                                </td>


                                {{-- NEUTRAL --}}

                                <td>

                                    {{ number_format($booth['neutral'] ?? 0) }}

                                </td>


                                {{-- SUPPORT --}}

                                <td>

                                    {{ number_format($booth['support'] ?? 0) }}

                                </td>


                                {{-- RISK SCORE --}}

                                <td>

                                    <strong>
                                        {{ number_format($booth['risk_score'] ?? 0) }}/100
                                    </strong>

                                </td>


                                {{-- PRIORITY --}}

                                <td>

                                    <span class="cd-status cd-status-inactive">

                                        🔴 HIGH

                                    </span>

                                </td>


                                {{-- ACTION --}}

                                <td>

                                    @if(!empty($booth['url']))

                                        <a
                                            href="{{ $booth['url'] }}"
                                            class="cd-booth-action"
                                        >

                                            👁️ Open Booth Intelligence

                                        </a>

                                    @else

                                        <span
                                            style="
                                                color:#94a3b8;
                                                font-size:12px;
                                            "
                                        >
                                            No link
                                        </span>

                                    @endif

                                </td>


                            </tr>

                        @endforeach


                    @else


                        <tr>

                            <td
                                colspan="8"
                                style="
                                    text-align:center;
                                    padding:30px;
                                    color:#94a3b8;
                                "
                            >

                                🚨 No High Risk Booths Detected

                            </td>

                        </tr>


                    @endif


                    </tbody>

                </table>

            </div>

        </div>



        <br>



        {{-- =====================================================
             SWING VILLAGES
        ====================================================== --}}

        <div class="cd-section-title">
            🏘️ Swing Villages
        </div>

        <div style="margin-top:12px;">

            <div class="cd-table-wrapper">

                <table class="cd-table">

                    <thead>

                        <tr>

                            <th>
                                Village
                            </th>

                            <th>
                                Voters
                            </th>

                            <th>
                                Neutral %
                            </th>

                            <th>
                                Priority
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    @if($swingVillages->count() > 0)


                        @foreach($swingVillages as $village)

                            <tr>

                                <td class="cd-table-voter">

                                    {{ $village['village'] ?? '-' }}

                                </td>

                                <td>

                                    {{ number_format($village['voters'] ?? 0) }}

                                </td>

                                <td>

                                    {{ number_format($village['neutral_percentage'] ?? 0) }}%

                                </td>

                                <td>


                                    @if(($village['priority'] ?? '') === 'HIGH')

                                        <span class="cd-status cd-status-inactive">

                                            🔴 HIGH

                                        </span>

                                    @else

                                        <span class="cd-status cd-status-active">

                                            🟢 NORMAL

                                        </span>

                                    @endif


                                </td>

                            </tr>

                        @endforeach


                    @else


                        <tr>

                            <td
                                colspan="4"
                                style="
                                    text-align:center;
                                    padding:30px;
                                    color:#94a3b8;
                                "
                            >

                                No Swing Village Data Available

                            </td>

                        </tr>


                    @endif


                    </tbody>

                </table>

            </div>

        </div>



        <br>



        {{-- =====================================================
             AI RECOMMENDATION
        ====================================================== --}}

        <div class="cd-card">

            <div class="cd-icon">
                🧠
            </div>

            <div class="cd-label">
                AI Recommendation
            </div>

            <div class="cd-value">
                {{ $aiWarRoom['recommendation'] ?? 'No recommendation available.' }}
            </div>

        </div>


    </div>

</div>



{{-- =========================================================
     PARTY SUPPORT
========================================================= --}}

<div class="cd-section">

    <div class="cd-section-header">

        <div class="cd-section-title">
            🗳️ Party Support
        </div>

        <div class="cd-section-description">
            Party-wise voter support distribution.
        </div>

    </div>


    <div class="cd-section-body">

        <div class="cd-grid cd-grid-4">


        @if(count($parties) > 0)


            @foreach($parties as $party)


                <div class="cd-card">

                    <div class="cd-icon">
                        {{ $party['symbol'] ?? '🏛️' }}
                    </div>

                    <div class="cd-party-name">
                        {{ $party['short_name'] ?? '-' }}
                    </div>

                    <div class="cd-party-full-name">
                        {{ $party['name'] ?? '-' }}
                    </div>

                    <div class="cd-number">
                        {{ number_format($party['total'] ?? 0) }}
                    </div>


                    <div class="cd-party-list">


                        @php

                            $partySupportTypes = [

                                'strong_support' => 'Strong Support',

                                'moderate_support' => 'Moderate Support',

                                'leaning_support' => 'Leaning Support',

                                'neutral' => 'Neutral',

                                'undecided' => 'Undecided',

                                'leaning_opposition' => 'Leaning Opposition',

                                'moderate_opposition' => 'Moderate Opposition',

                                'strong_opposition' => 'Strong Opposition',

                            ];

                        @endphp


                        @foreach($partySupportTypes as $key => $label)

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


            @endforeach


        @else


            <div class="cd-card">

                <div class="cd-empty-title">
                    No Party Data Available
                </div>

            </div>


        @endif


        </div>

    </div>

</div>



{{-- =========================================================
     VILLAGE WISE SUMMARY
========================================================= --}}

<div class="cd-section">

    <div class="cd-section-header">

        <div class="cd-section-title">
            🏘️ Village Wise Summary
        </div>

        <div class="cd-section-description">
            Village-level campaign and voter distribution.
        </div>

    </div>


    <div class="cd-table-wrapper">

        <table class="cd-table">

            <thead>

                <tr>

                    <th>
                        #
                    </th>

                    <th>
                        Village
                    </th>

                    <th>
                        Taluka
                    </th>

                    <th>
                        Booths
                    </th>

                    <th>
                        Houses
                    </th>

                    <th>
                        Voters
                    </th>

                </tr>

            </thead>


            <tbody>


            @if(count($villagesList) > 0)


                @foreach($villagesList as $index => $village)

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

                @endforeach


            @else


                <tr>

                    <td
                        colspan="6"
                        style="
                            text-align:center;
                            padding:30px;
                            color:#94a3b8;
                        "
                    >

                        No Village Data Found

                    </td>

                </tr>


            @endif


            </tbody>

        </table>

    </div>

</div>



{{-- =========================================================
     CONSTITUENCY INFORMATION
========================================================= --}}

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

                <div class="cd-value">
                    {{ $constituency->name ?? '-' }}
                </div>

            </div>



            <div class="cd-card">

                <div class="cd-label">
                    District
                </div>

                <div class="cd-value">
                    {{ $constituency->district ?? '-' }}
                </div>

            </div>



            <div class="cd-card">

                <div class="cd-label">
                    State
                </div>

                <div class="cd-value">
                    {{ $constituency->state ?? '-' }}
                </div>

            </div>



            <div class="cd-card">

                <div class="cd-label">
                    Status
                </div>

                <div style="margin-top:8px;">

                    @if($constituency->is_active ?? false)

                        <span class="cd-status cd-status-active">

                            🟢 Active

                        </span>

                    @else

                        <span class="cd-status cd-status-inactive">

                            🔴 Inactive

                        </span>

                    @endif

                </div>

            </div>


        </div>

    </div>

</div>



@else


{{-- =========================================================
     EMPTY STATE
========================================================= --}}

<div class="cd-section">

    <div class="cd-empty">

        <div class="cd-empty-icon">
            🏛️
        </div>

        <div class="cd-empty-title">
            Constituency Dashboard
        </div>

        <div class="cd-empty-description">
            Select a constituency above to view complete campaign intelligence.
        </div>

    </div>

</div>


@endif


</div>

</x-filament-panels::page>
