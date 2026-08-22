<x-filament-panels::page>

@php

    $data = $this->data;

    $selectedBooth = $data['selectedBooth'] ?? [];

    $boothOptions = $data['boothOptions'] ?? [];

    $booth = $selectedBooth['booth'] ?? null;

    $summary = $selectedBooth['summary'] ?? [];

    $genderAnalysis = $selectedBooth['gender_analysis'] ?? [];

    $genderAggregate = $selectedBooth['gender_aggregate'] ?? [];

    $houses = $selectedBooth['houses'] ?? collect();

    $voters = $selectedBooth['voters'] ?? collect();

    $swingVoters = $selectedBooth['swing_voters'] ?? collect();

    $influencers = $selectedBooth['influencers'] ?? collect();

    $volunteers = $selectedBooth['volunteers'] ?? collect();

    $riskScore = (float) ($selectedBooth['risk_score'] ?? 0);

    $priority = $selectedBooth['priority'] ?? 'LOW';

    $actions = $selectedBooth['actions'] ?? [];

    $organisationTeam = $selectedBooth['organisation_team'] ?? [];


    $priorityClass = match ($priority) {

        'HIGH' => 'bi-badge-high',

        'MEDIUM' => 'bi-badge-medium',

        default => 'bi-badge-low',

    };


    $riskLabel = match (true) {

        $riskScore >= 70 => 'High Campaign Risk',

        $riskScore >= 40 => 'Medium Campaign Risk',

        default => 'Low Campaign Risk',

    };

@endphp


<style>

.bi-wrapper {
    width: 100%;
}

.bi-selector {
    margin-bottom: 20px;
    padding: 18px 20px;
    border-radius: 18px;
    background: rgba(255,255,255,.82);
    border: 1px solid rgba(0,0,0,.08);
    box-shadow: 0 4px 18px rgba(0,0,0,.035);
}

.dark .bi-selector {
    background: rgba(24,24,27,.82);
    border-color: rgba(255,255,255,.08);
}

.bi-selector-label {
    display: block;
    margin-bottom: 8px;
    font-size: 13px;
    font-weight: 700;
}

.bi-select {
    width: 100%;
    padding: 11px 14px;
    border-radius: 10px;
    border: 1px solid rgba(0,0,0,.12);
    background: rgba(255,255,255,.9);
    font-size: 14px;
}

.dark .bi-select {
    background: rgba(39,39,42,.9);
    border-color: rgba(255,255,255,.12);
    color: white;
}

.bi-hero {
    padding: 26px;
    border-radius: 20px;
    background: linear-gradient(
        135deg,
        rgba(255,255,255,.95),
        rgba(248,250,252,.85)
    );
    border: 1px solid rgba(0,0,0,.08);
    box-shadow: 0 8px 30px rgba(0,0,0,.05);
}

.dark .bi-hero {
    background: linear-gradient(
        135deg,
        rgba(24,24,27,.95),
        rgba(39,39,42,.85)
    );
    border-color: rgba(255,255,255,.08);
}

.bi-hero-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
}

.bi-title {
    font-size: 26px;
    font-weight: 800;
    line-height: 1.2;
}

.bi-subtitle {
    margin-top: 7px;
    color: #6b7280;
    font-size: 14px;
}

.dark .bi-subtitle {
    color: #9ca3af;
}

.bi-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 18px;
}

.bi-meta-item {
    padding: 7px 12px;
    border-radius: 999px;
    background: rgba(128,128,128,.10);
    font-size: 12px;
}

.bi-grid {
    display: grid;
    gap: 16px;
}

.bi-grid-4 {
    grid-template-columns: repeat(4,minmax(0,1fr));
}

.bi-grid-3 {
    grid-template-columns: repeat(3,minmax(0,1fr));
}

.bi-grid-2 {
    grid-template-columns: repeat(2,minmax(0,1fr));
}

.bi-card {
    padding: 20px;
    border-radius: 18px;
    background: rgba(255,255,255,.78);
    border: 1px solid rgba(0,0,0,.08);
    box-shadow: 0 4px 18px rgba(0,0,0,.035);
}

.dark .bi-card {
    background: rgba(24,24,27,.78);
    border-color: rgba(255,255,255,.08);
}

.bi-icon {
    font-size: 26px;
}

.bi-label {
    margin-top: 8px;
    font-size: 12px;
    color: #6b7280;
}

.dark .bi-label {
    color: #9ca3af;
}

.bi-number {
    margin-top: 5px;
    font-size: 28px;
    font-weight: 800;
}

.bi-small {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    color: #6b7280;
}

.dark .bi-small {
    color: #9ca3af;
}

.bi-section {
    margin-top: 20px;
    overflow: hidden;
    border-radius: 18px;
    background: rgba(255,255,255,.78);
    border: 1px solid rgba(0,0,0,.08);
}

.dark .bi-section {
    background: rgba(24,24,27,.78);
    border-color: rgba(255,255,255,.08);
}

.bi-header {
    padding: 17px 20px;
    border-bottom: 1px solid rgba(0,0,0,.07);
    font-size: 17px;
    font-weight: 750;
}

.dark .bi-header {
    border-color: rgba(255,255,255,.08);
}

.bi-body {
    padding: 20px;
}

.bi-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    background: rgba(128,128,128,.12);
}

.bi-badge-high {
    background: rgba(239,68,68,.12);
    color: #dc2626;
}

.bi-badge-medium {
    background: rgba(245,158,11,.13);
    color: #d97706;
}

.bi-badge-low {
    background: rgba(34,197,94,.12);
    color: #16a34a;
}

.bi-risk {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}

.bi-risk-score {
    font-size: 42px;
    font-weight: 850;
}

.bi-risk-label {
    font-size: 12px;
    color: #6b7280;
}

.dark .bi-risk-label {
    color: #9ca3af;
}

.bi-progress {
    width: 100%;
    height: 10px;
    margin-top: 15px;
    border-radius: 999px;
    background: rgba(128,128,128,.15);
    overflow: hidden;
}

.bi-progress-bar {
    height: 100%;
    border-radius: 999px;
    background: #ef4444;
}

.bi-action {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px;
    margin-bottom: 10px;
    border-radius: 12px;
    background: rgba(128,128,128,.07);
}

.bi-action:last-child {
    margin-bottom: 0;
}

.bi-action-icon {
    width: 30px;
    height: 30px;
    flex: 0 0 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(59,130,246,.12);
}

.bi-table-wrap {
    overflow-x: auto;
}

.bi-table {
    width: 100%;
    min-width: 650px;
    border-collapse: collapse;
}

.bi-table th {
    padding: 12px;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    color: #6b7280;
    background: rgba(128,128,128,.07);
}

.dark .bi-table th {
    color: #9ca3af;
}

.bi-table td {
    padding: 13px 12px;
    border-top: 1px solid rgba(128,128,128,.12);
    font-size: 13px;
}

.bi-empty {
    padding: 30px;
    text-align: center;
    color: #9ca3af;
}

.bi-data-missing {
    padding: 18px;
    border-radius: 12px;
    background: rgba(245,158,11,.08);
    border: 1px solid rgba(245,158,11,.18);
    color: #b45309;
}

.dark .bi-data-missing {
    color: #fbbf24;
}

/* =========================================================
   DARK THEME — PURPLE / CHARCOAL
========================================================= */

html.dark .bi-selector,
html.dark .bi-card,
html.dark .bi-section {
    border-color: rgba(139, 92, 246, .25);
    background: linear-gradient(145deg, #0b0910 0%, #171020 58%, #211032 100%);
    box-shadow: 0 15px 36px rgba(0, 0, 0, .28), 0 0 28px rgba(124, 58, 237, .06);
}

html.dark .bi-hero {
    border-color: rgba(139, 92, 246, .34);
    background: linear-gradient(135deg, #08070d 0%, #151022 55%, #291044 100%);
    box-shadow: 0 18px 42px rgba(0, 0, 0, .34), 0 0 35px rgba(124, 58, 237, .09);
}

html.dark .bi-card {
    transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
}

html.dark .bi-card:hover {
    transform: translateY(-2px);
    border-color: rgba(167, 139, 250, .62);
    background: linear-gradient(145deg, #171221, #25143b);
    box-shadow: 0 13px 30px rgba(76, 29, 149, .23);
}

html.dark .bi-title,
html.dark .bi-selector-label,
html.dark .bi-number,
html.dark .bi-header,
html.dark .bi-risk-score,
html.dark .bi-action strong,
html.dark .bi-table td,
html.dark .bi-table td strong {
    color: #f8fafc;
}

html.dark .bi-subtitle,
html.dark .bi-label,
html.dark .bi-small,
html.dark .bi-risk-label,
html.dark .bi-empty,
html.dark .bi-action span {
    color: #a8a3b7;
}

html.dark .bi-select {
    color: #f8fafc;
    border-color: rgba(139, 92, 246, .34);
    background: #100d17;
    color-scheme: dark;
}

html.dark .bi-select:focus {
    outline: none;
    border-color: #a78bfa;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, .20);
}

html.dark .bi-meta-item,
html.dark .bi-badge {
    color: #ddd6fe;
    border: 1px solid rgba(167, 139, 250, .23);
    background: rgba(124, 58, 237, .15);
}

html.dark .bi-header {
    border-color: rgba(139, 92, 246, .20);
    background: linear-gradient(90deg, #0e0b14, #1c102b);
}

html.dark .bi-body {
    background: rgba(7, 6, 11, .38);
}

html.dark .bi-action {
    color: #d8d4e3;
    border: 1px solid rgba(139, 92, 246, .17);
    background: linear-gradient(135deg, rgba(28, 23, 36, .92), rgba(38, 20, 57, .70));
}

html.dark .bi-action-icon {
    color: #ddd6fe;
    background: rgba(124, 58, 237, .20);
}

html.dark .bi-progress {
    background: rgba(148, 163, 184, .16);
}

html.dark .bi-table th {
    color: #c4b5fd;
    border-color: rgba(139, 92, 246, .18);
    background: #151020;
}

html.dark .bi-table td {
    border-color: rgba(148, 163, 184, .12);
}

html.dark .bi-table tbody tr:hover {
    background: rgba(124, 58, 237, .11);
}

html.dark .bi-badge-high {
    color: #fca5a5;
    border-color: rgba(239, 68, 68, .28);
    background: rgba(153, 27, 27, .22);
}

html.dark .bi-badge-medium {
    color: #fcd34d;
    border-color: rgba(245, 158, 11, .28);
    background: rgba(146, 64, 14, .22);
}

html.dark .bi-badge-low {
    color: #86efac;
    border-color: rgba(34, 197, 94, .28);
    background: rgba(22, 101, 52, .22);
}

html.dark .bi-data-missing {
    color: #fcd34d;
    border-color: rgba(245, 158, 11, .26);
    background: rgba(146, 64, 14, .18);
}

@media(max-width:1100px) {

    .bi-grid-4 {
        grid-template-columns: repeat(2,minmax(0,1fr));
    }

}

@media(max-width:700px) {

    .bi-grid-4,
    .bi-grid-3,
    .bi-grid-2 {
        grid-template-columns: 1fr;
    }

    .bi-hero-top {
        flex-direction: column;
    }

    .bi-title {
        font-size: 22px;
    }

    .bi-risk {
        flex-direction: column;
        align-items: flex-start;
    }

}

</style>


<div class="bi-wrapper">


{{-- =========================================================
     BOOTH SELECTOR
========================================================= --}}

<div class="bi-selector">

    <label class="bi-selector-label">
        🗳️ Select Booth
    </label>

    @if(count($boothOptions) > 0)

        <select
            wire:model.live="booth"
            class="bi-select"
        >

            @foreach($boothOptions as $id => $label)

                <option value="{{ $id }}">
                    {{ $label }}
                </option>

            @endforeach

        </select>

    @else

        <div class="bi-empty">
            No active booths available.
        </div>

    @endif

</div>


{{-- =========================================================
     EMPTY BOOTH
========================================================= --}}

@if(! $booth)

    <div class="bi-section">

        <div class="bi-body">

            <div class="bi-empty">

                🗳️ No booth selected.

                <br>

                Please select a booth to view intelligence.

            </div>

        </div>

    </div>


@else


{{-- =========================================================
     BOOTH HEADER
========================================================= --}}

<div class="bi-hero">

    <div class="bi-hero-top">

        <div>

            <div class="bi-title">
                🗳️ {{ $booth['name'] ?? 'Unknown Booth' }}
            </div>

            <div class="bi-subtitle">
                Booth Intelligence & Campaign Analysis
            </div>

            <div class="bi-meta">

                <span class="bi-meta-item">
                    Booth No: {{ $booth['booth_no'] ?? '-' }}
                </span>

                <span class="bi-meta-item">
                    Part No: {{ $booth['part_no'] ?? '-' }}
                </span>

                <span class="bi-meta-item">
                    📍 {{ $booth['village'] ?? '-' }}
                </span>

                <span class="bi-meta-item">
                    {{ $booth['taluka'] ?? '-' }}
                </span>

                <span class="bi-meta-item">
                    {{ $booth['district'] ?? '-' }}
                </span>

                <span class="bi-meta-item">
                    {{ $booth['category'] ?? '-' }}
                </span>

            </div>

        </div>

        <div>

            <span class="bi-badge {{ $priorityClass }}">
                {{ $priority }} PRIORITY
            </span>

        </div>

    </div>

</div>


{{-- =========================================================
     DATA WARNING
========================================================= --}}

@if(($summary['total_voters'] ?? 0) === 0)

    <div class="bi-section">

        <div class="bi-body">

            <div class="bi-data-missing">

                ⚠️ <strong>No voter data available for this booth.</strong>

                <br>

                This booth exists, but no houses/voters are currently
                mapped to it.

            </div>

        </div>

    </div>

@endif
{{-- =========================================================
     BOOTH ORGANISATION
========================================================= --}}

@if(!empty($organisationTeam))

    <div class="bi-section">

        <div class="bi-header">
            🏛️ Booth Organisation
        </div>

        <div class="bi-body">

            <div class="bi-grid bi-grid-3">

                @foreach($organisationTeam as $role => $members)

                    @foreach($members as $member)

                        <div class="bi-card">

                            <div style="
                                display:flex;
                                align-items:flex-start;
                                gap:14px;
                            ">

                                <div style="
                                    width:46px;
                                    height:46px;
                                    flex:0 0 46px;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    border-radius:12px;
                                    background:rgba(59,130,246,.10);
                                    font-size:22px;
                                ">
                                    👤
                                </div>

                                <div style="min-width:0;">

                                    <div style="
                                        font-size:11px;
                                        font-weight:700;
                                        color:#6b7280;
                                        margin-bottom:5px;
                                        text-transform:uppercase;
                                    ">
                                        {{ $role }}
                                    </div>

                                    <div style="
                                        font-size:17px;
                                        font-weight:800;
                                        line-height:1.3;
                                    ">
                                        {{ $member['name'] ?? '-' }}
                                    </div>

                                    @if(!empty($member['mobile']))

                                        <div style="
                                            margin-top:8px;
                                            font-size:13px;
                                            color:#6b7280;
                                        ">
                                            📱 {{ $member['mobile'] }}
                                        </div>

                                    @endif

                                    @if(!empty($member['gender']))

                                        <div style="
                                            margin-top:4px;
                                            font-size:12px;
                                            color:#9ca3af;
                                        ">
                                            {{ $member['gender'] }}
                                        </div>

                                    @endif

                                </div>

                            </div>

                        </div>

                    @endforeach

                @endforeach

            </div>

        </div>

    </div>

@endif


{{-- =========================================================
     KEY METRICS
========================================================= --}}

<div
    class="bi-grid bi-grid-4"
    style="margin-top:20px;"
>

    <div class="bi-card">

        <div class="bi-icon">👥</div>

        <div class="bi-label">
            Total Voters
        </div>

        <div class="bi-number">
            {{ number_format($summary['total_voters'] ?? 0) }}
        </div>

    </div>


    <div class="bi-card">

        <div class="bi-icon">👨</div>

        <div class="bi-label">
            Male Voters
        </div>

        <div class="bi-number">
            {{ number_format($summary['male_voters'] ?? 0) }}
        </div>

    </div>


    <div class="bi-card">

        <div class="bi-icon">👩</div>

        <div class="bi-label">
            Female Voters
        </div>

        <div class="bi-number">
            {{ number_format($summary['female_voters'] ?? 0) }}
        </div>

    </div>


    <div class="bi-card">

        <div class="bi-icon">🏠</div>

        <div class="bi-label">
            Houses
        </div>

        <div class="bi-number">
            {{ number_format($summary['houses'] ?? 0) }}
        </div>

    </div>

</div>


{{-- =========================================================
     POLITICAL METRICS
========================================================= --}}

<div
    class="bi-grid bi-grid-4"
    style="margin-top:16px;"
>

    <div class="bi-card">

        <div class="bi-icon">🟢</div>

        <div class="bi-label">
            Support
        </div>

        <div class="bi-number">
            {{ $summary['support_percentage'] ?? 0 }}%
        </div>

        <span class="bi-small">
            {{ $summary['supporters'] ?? 0 }} voters
        </span>

    </div>


    <div class="bi-card">

        <div class="bi-icon">⚪</div>

        <div class="bi-label">
            Neutral
        </div>

        <div class="bi-number">
            {{ $summary['neutral_percentage'] ?? 0 }}%
        </div>

        <span class="bi-small">
            {{ $summary['neutral'] ?? 0 }} voters
        </span>

    </div>


    <div class="bi-card">

        <div class="bi-icon">🔴</div>

        <div class="bi-label">
            Opposition
        </div>

        <div class="bi-number">
            {{ $summary['opposition_percentage'] ?? 0 }}%
        </div>

        <span class="bi-small">
            {{ $summary['opposition'] ?? 0 }} voters
        </span>

    </div>


    <div class="bi-card">

        <div class="bi-icon">❓</div>

        <div class="bi-label">
            Undecided
        </div>

        <div class="bi-number">
            {{ $summary['undecided'] ?? 0 }}
        </div>

        <span class="bi-small">
            voters
        </span>

    </div>

</div>


{{-- =========================================================
     AI RISK + ACTIONS
========================================================= --}}

<div
    class="bi-grid bi-grid-2"
    style="margin-top:20px;"
>

    <div
        class="bi-section"
        style="margin-top:0;"
    >

        <div class="bi-header">
            🤖 AI Risk Analysis
        </div>

        <div class="bi-body">

            <div class="bi-risk">

                <div>

                    <div class="bi-risk-score">
                        {{ number_format($riskScore, 0) }}/100
                    </div>

                    <div class="bi-risk-label">
                        {{ $riskLabel }}
                    </div>

                </div>

                <span class="bi-badge {{ $priorityClass }}">
                    {{ $priority }}
                </span>

            </div>

            <div class="bi-progress">

                <div
                    class="bi-progress-bar"
                    style="width:{{ min(100, max(0, $riskScore)) }}%;"
                ></div>

            </div>

        </div>

    </div>


    <div
        class="bi-section"
        style="margin-top:0;"
    >

        <div class="bi-header">
            🎯 Recommended Actions
        </div>

        <div class="bi-body">

            @forelse($actions as $action)

                <div class="bi-action">

                    <div class="bi-action-icon">
                        ✓
                    </div>

                    <div>
                        {{ $action }}
                    </div>

                </div>

            @empty

                <div class="bi-empty">
                    No actions available.
                </div>

            @endforelse

        </div>

    </div>

</div>


{{-- =========================================================
     GENDER-WISE PARTY INTELLIGENCE
========================================================= --}}

<div class="bi-section">

    <div class="bi-header">
        👥 Gender-wise Party Intelligence
    </div>

    <div class="bi-body">

        {{-- =========================
             MALE
        ========================== --}}

        <h3 style="font-size:16px;font-weight:800;margin-bottom:12px;">
            👨 Male
        </h3>

        <div class="bi-table-wrap">

            <table class="bi-table">

                <thead>
                    <tr>
                        <th>Party</th>
                        <th>Strong</th>
                        <th>Moderate</th>
                        <th>Leaning</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>

                @forelse(($genderAggregate['male'] ?? []) as $party)

                    <tr>

                        <td>
                            <strong>
                                {{ $party['symbol'] ?? '' }}
                                {{ $party['short_name'] ?? $party['name'] ?? '-' }}
                            </strong>
                        </td>

                        <td>
                            @if(($party['name'] ?? '') === 'Neutral' || ($party['name'] ?? '') === 'Undecided')
                                —
                            @else
                                {{ $party['strong'] ?? 0 }}
                            @endif
                        </td>

                        <td>
                            @if(($party['name'] ?? '') === 'Neutral' || ($party['name'] ?? '') === 'Undecided')
                                —
                            @else
                                {{ $party['moderate'] ?? 0 }}
                            @endif
                        </td>

                        <td>
                            @if(($party['name'] ?? '') === 'Neutral' || ($party['name'] ?? '') === 'Undecided')
                                —
                            @else
                                {{ $party['leaning'] ?? 0 }}
                            @endif
                        </td>

                        <td>
                            <strong>
                                {{ $party['total'] ?? 0 }}
                            </strong>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5">
                            <div class="bi-empty">
                                No male party intelligence available.
                            </div>
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        {{-- =========================
             FEMALE
        ========================== --}}

        <h3 style="font-size:16px;font-weight:800;margin-top:28px;margin-bottom:12px;">
            👩 Female
        </h3>

        <div class="bi-table-wrap">

            <table class="bi-table">

                <thead>
                    <tr>
                        <th>Party</th>
                        <th>Strong</th>
                        <th>Moderate</th>
                        <th>Leaning</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>

                @forelse(($genderAggregate['female'] ?? []) as $party)

                    <tr>

                        <td>
                            <strong>
                                {{ $party['symbol'] ?? '' }}
                                {{ $party['short_name'] ?? $party['name'] ?? '-' }}
                            </strong>
                        </td>

                        <td>
                            @if(($party['name'] ?? '') === 'Neutral' || ($party['name'] ?? '') === 'Undecided')
                                —
                            @else
                                {{ $party['strong'] ?? 0 }}
                            @endif
                        </td>

                        <td>
                            @if(($party['name'] ?? '') === 'Neutral' || ($party['name'] ?? '') === 'Undecided')
                                —
                            @else
                                {{ $party['moderate'] ?? 0 }}
                            @endif
                        </td>

                        <td>
                            @if(($party['name'] ?? '') === 'Neutral' || ($party['name'] ?? '') === 'Undecided')
                                —
                            @else
                                {{ $party['leaning'] ?? 0 }}
                            @endif
                        </td>

                        <td>
                            <strong>
                                {{ $party['total'] ?? 0 }}
                            </strong>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5">
                            <div class="bi-empty">
                                No female party intelligence available.
                            </div>
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        {{-- =========================
             OTHER
        ========================== --}}

        @if(!empty($genderAggregate['other']))

            <h3 style="font-size:16px;font-weight:800;margin-top:28px;margin-bottom:12px;">
                ⚪ Other
            </h3>

            <div class="bi-table-wrap">

                <table class="bi-table">

                    <thead>
                        <tr>
                            <th>Party</th>
                            <th>Strong</th>
                            <th>Moderate</th>
                            <th>Leaning</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>

                    @foreach($genderAggregate['other'] as $party)

                        <tr>

                            <td>
                                <strong>
                                    {{ $party['symbol'] ?? '' }}
                                    {{ $party['short_name'] ?? $party['name'] ?? '-' }}
                                </strong>
                            </td>

                            <td>
                                @if(($party['name'] ?? '') === 'Neutral' || ($party['name'] ?? '') === 'Undecided')
                                    —
                                @else
                                    {{ $party['strong'] ?? 0 }}
                                @endif
                            </td>

                            <td>
                                @if(($party['name'] ?? '') === 'Neutral' || ($party['name'] ?? '') === 'Undecided')
                                    —
                                @else
                                    {{ $party['moderate'] ?? 0 }}
                                @endif
                            </td>

                            <td>
                                @if(($party['name'] ?? '') === 'Neutral' || ($party['name'] ?? '') === 'Undecided')
                                    —
                                @else
                                    {{ $party['leaning'] ?? 0 }}
                                @endif
                            </td>

                            <td>
                                <strong>
                                    {{ $party['total'] ?? 0 }}
                                </strong>
                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        @endif

    </div>

</div>


{{-- =========================================================
     HOUSE INTELLIGENCE
========================================================= --}}

<div class="bi-section">

    <div class="bi-header">
        🏠 House Intelligence
    </div>

    <div class="bi-body">

        <div class="bi-table-wrap">

            <table class="bi-table">

                <thead>

                    <tr>

                        <th>House</th>
                        <th>Head</th>
                        <th>Voters</th>
                        <th>Mobile</th>
                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                @forelse($houses as $house)

                    @php

                        /*
                         * Houses can arrive as either:
                         * - Eloquent models
                         * - arrays
                         *
                         * Handle both safely.
                         */

                        $houseNo = is_array($house)
                            ? ($house['house_no'] ?? '-')
                            : ($house->house_no ?? '-');

                        $head = is_array($house)
                            ? ($house['head_of_family'] ?? '-')
                            : ($house->head_of_family ?? '-');

                        $votersCount = is_array($house)
                            ? ($house['voters_count'] ?? 0)
                            : ($house->voters_count ?? 0);

                        $mobile = is_array($house)
                            ? ($house['mobile'] ?? '-')
                            : ($house->mobile ?? '-');

                        $verified = is_array($house)
                            ? ($house['is_verified'] ?? false)
                            : ($house->is_verified ?? false);

                    @endphp

                    <tr>

                        <td>
                            <strong>
                                {{ $houseNo }}
                            </strong>
                        </td>

                        <td>
                            {{ $head }}
                        </td>

                        <td>
                            {{ $votersCount }}
                        </td>

                        <td>
                            {{ $mobile }}
                        </td>

                        <td>

                            @if($verified)

                                <span class="bi-badge bi-badge-low">
                                    Verified
                                </span>

                            @else

                                <span class="bi-badge bi-badge-medium">
                                    Pending
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5">

                            <div class="bi-empty">
                                No house data found.
                            </div>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- =========================================================
     SWING VOTERS
========================================================= --}}

<div class="bi-section">

    <div class="bi-header">
        🎯 Swing / Persuadable Voters
    </div>

    <div class="bi-body">

        <div class="bi-table-wrap">

            <table class="bi-table">

                <thead>

                    <tr>

                       <th>Voter</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Party</th>
                        <th>Support</th>
                        <th>Priority</th>
                        <th>House</th>

                    </tr>

                </thead>

                <tbody>

                @forelse($swingVoters->take(20) as $voter)

                    <tr>

                        <td>
                            <strong>
                                {{ $voter->name }}
                            </strong>
                        </td>

                        <td>
                            {{ $voter->gender ?? '-' }}
                        </td>

                        <td>
                            {{ $voter->age ?? '-' }}
                        </td>
                         <td>
                            {{ $voter->politicalParty?->short_name
                                ?? $voter->politicalParty?->name
                                ?? 'Not Assigned' }}
                        </td>
                        <td>
                            {{ $voter->support_level ?? '-' }}
                        </td>

                        <td>

                            <span class="bi-badge">

                                {{ $voter->priority ?? 'Medium' }}

                            </span>

                        </td>

                        <td>
                            {{ $voter->house?->house_no ?? '-' }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7">

                            <div class="bi-empty">
                                No swing voters found.
                            </div>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- =========================================================
     INFLUENCERS + VOLUNTEERS
========================================================= --}}

<div
    class="bi-grid bi-grid-2"
    style="margin-top:20px;"
>

    <div
        class="bi-section"
        style="margin-top:0;"
    >

        <div class="bi-header">
            ⭐ Influencers
        </div>

        <div class="bi-body">

            @forelse($influencers as $voter)

                <div class="bi-action">

                    <div class="bi-action-icon">
                        ⭐
                    </div>

                    <div>

                        <strong>
                            {{ $voter->name }}
                        </strong>

                        <br>

                        <small>

                            {{ $voter->gender ?? '-' }}
                            ·
                            {{ $voter->age ?? '-' }} years

                        </small>

                    </div>

                </div>

            @empty

                <div class="bi-empty">
                    No influencers identified.
                </div>

            @endforelse

        </div>

    </div>


    <div
        class="bi-section"
        style="margin-top:0;"
    >

        <div class="bi-header">
            🙋 Booth Volunteers
        </div>

        <div class="bi-body">

            @forelse($volunteers as $voter)

                <div class="bi-action">

                    <div class="bi-action-icon">
                        ✓
                    </div>

                    <div>

                        <strong>
                            {{ $voter->name }}
                        </strong>

                        <br>

                        <small>

                            {{ $voter->booth_committee_role
                                ?? 'Volunteer' }}

                        </small>

                    </div>

                </div>

            @empty

                <div class="bi-empty">
                    No volunteers identified.
                </div>

            @endforelse

        </div>

    </div>

</div>


{{-- =========================================================
     COMPLETE VOTER LIST
========================================================= --}}

<div class="bi-section">

    <div class="bi-header">
        👥 Booth Voter Intelligence
    </div>

    <div class="bi-body">

        <div class="bi-table-wrap">

            <table class="bi-table">

                <thead>

                    <tr>

                        <th>#</th>
                        <th>Voter</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Party</th>
                        <th>Support</th>
                        <th>House</th>
                        <th>Priority</th>

                    </tr>

                </thead>

                <tbody>

                @forelse($voters as $index => $voter)

                    <tr>

                        <td>
                            {{ $index + 1 }}
                        </td>

                        <td>

                            <strong>
                                {{ $voter->name }}
                            </strong>

                            @if($voter->epic_no)

                                <br>

                                <small>
                                    {{ $voter->epic_no }}
                                </small>

                            @endif

                        </td>

                        <td>
                            {{ $voter->gender ?? '-' }}
                        </td>

                        <td>
                            {{ $voter->age ?? '-' }}
                        </td>

                        <td>
                               {{ $voter->politicalParty?->short_name
                                   ?? $voter->politicalParty?->name
                                   ?? 'Not Assigned' }}
                        </td>
                        <td>
                            {{ $voter->support_level ?? 'Not Assigned' }}
                        </td>

                        <td>
                            {{ $voter->house?->house_no ?? '-' }}
                        </td>

                        <td>

                            <span class="bi-badge">

                                {{ $voter->priority ?? 'Medium' }}

                            </span>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="8">

                            <div class="bi-empty">
                                No voters found.
                            </div>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


@endif


</div>

</x-filament-panels::page>
