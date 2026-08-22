<x-filament-panels::page>

@php
    $rows = $this->boothOrganisation;
    $summary = $this->summary;
    $villages = $this->villages;
@endphp

<style>
    .abo-wrapper {
        width: 100%;
    }

    .abo-toolbar {
        margin-bottom: 20px;
        padding: 18px;
        border-radius: 18px;
        background: rgba(255,255,255,.82);
        border: 1px solid rgba(0,0,0,.08);
        box-shadow: 0 4px 18px rgba(0,0,0,.035);
    }

    .dark .abo-toolbar {
        background: rgba(24,24,27,.82);
        border-color: rgba(255,255,255,.08);
    }

    .abo-filters {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto;
        gap: 12px;
        align-items: end;
    }

    .abo-filter-label {
        display: block;
        margin-bottom: 7px;
        font-size: 12px;
        font-weight: 800;
    }

    .abo-input,
    .abo-select {
        width: 100%;
        min-height: 42px;
        padding: 9px 12px;
        border-radius: 10px;
        border: 1px solid rgba(0,0,0,.12);
        background: rgba(255,255,255,.9);
        font-size: 13px;
    }

    .dark .abo-input,
    .dark .abo-select {
        background: rgba(39,39,42,.9);
        border-color: rgba(255,255,255,.12);
        color: white;
    }

    .abo-filter-actions {
        display: flex;
        gap: 8px;
    }

    .abo-button {
        min-height: 42px;
        padding: 9px 14px;
        border: 0;
        border-radius: 10px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .abo-button-reset {
        background: rgba(128,128,128,.12);
    }

    .abo-button-csv {
        background: rgba(34,197,94,.12);
        color: #15803d;
    }

    .abo-button-pdf {
        background: rgba(239,68,68,.12);
        color: #dc2626;
    }

    .abo-export-row {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 14px;
    }

    .abo-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .abo-card {
        padding: 20px;
        border-radius: 18px;
        background: rgba(255,255,255,.82);
        border: 1px solid rgba(0,0,0,.08);
        box-shadow: 0 4px 18px rgba(0,0,0,.035);
    }

    .dark .abo-card {
        background: rgba(24,24,27,.82);
        border-color: rgba(255,255,255,.08);
    }

    .abo-card-label {
        font-size: 12px;
        color: #6b7280;
        font-weight: 700;
    }

    .dark .abo-card-label {
        color: #9ca3af;
    }

    .abo-card-number {
        margin-top: 6px;
        font-size: 28px;
        font-weight: 800;
    }

    .abo-table-section {
        overflow: hidden;
        border-radius: 18px;
        background: rgba(255,255,255,.82);
        border: 1px solid rgba(0,0,0,.08);
        box-shadow: 0 4px 18px rgba(0,0,0,.035);
    }

    .dark .abo-table-section {
        background: rgba(24,24,27,.82);
        border-color: rgba(255,255,255,.08);
    }

    .abo-section-header {
        padding: 18px 20px;
        border-bottom: 1px solid rgba(0,0,0,.08);
        font-size: 17px;
        font-weight: 800;
    }

    .dark .abo-section-header {
        border-color: rgba(255,255,255,.08);
    }

    .abo-table-wrap {
        overflow-x: auto;
    }

    .abo-table {
        width: 100%;
        min-width: 1500px;
        border-collapse: collapse;
    }

    .abo-table th {
        padding: 13px 12px;
        text-align: left;
        font-size: 11px;
        font-weight: 800;
        color: #6b7280;
        background: rgba(128,128,128,.07);
        white-space: nowrap;
    }

    .dark .abo-table th {
        color: #9ca3af;
    }

    .abo-table td {
        padding: 14px 12px;
        border-top: 1px solid rgba(128,128,128,.12);
        font-size: 13px;
        vertical-align: top;
    }

    .abo-booth-no {
        font-size: 14px;
        font-weight: 800;
    }

    .abo-booth-name {
        font-weight: 750;
        margin-top: 3px;
    }

    .abo-village {
        margin-top: 3px;
        color: #6b7280;
        font-size: 11px;
    }

    .dark .abo-village {
        color: #9ca3af;
    }

    .abo-person {
        min-width: 150px;
    }

    .abo-person-name {
        font-weight: 750;
    }

    .abo-person-mobile {
        margin-top: 4px;
        font-size: 11px;
        color: #6b7280;
    }

    .dark .abo-person-mobile {
        color: #9ca3af;
    }

    .abo-person-house {
        margin-top: 3px;
        font-size: 10px;
        color: #9ca3af;
    }

    .abo-empty-role {
        color: #9ca3af;
        font-size: 12px;
    }

    .abo-role-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 22px;
        padding: 0 7px;
        border-radius: 999px;
        background: rgba(59,130,246,.10);
        color: #2563eb;
        font-size: 10px;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .abo-status {
        display: inline-flex;
        align-items: center;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
    }

    .abo-status-complete {
        background: rgba(34,197,94,.12);
        color: #16a34a;
    }

    .abo-status-pending {
        background: rgba(245,158,11,.12);
        color: #d97706;
    }

    .abo-empty {
        padding: 40px;
        text-align: center;
        color: #9ca3af;
    }

    /* =====================================================
       DARK THEME — PURPLE / CHARCOAL
    ====================================================== */

    html.dark .abo-toolbar {
        border-color: rgba(139, 92, 246, .34);
        background: linear-gradient(135deg, #08070d 0%, #151022 55%, #291044 100%);
        box-shadow: 0 18px 42px rgba(0, 0, 0, .34), 0 0 35px rgba(124, 58, 237, .09);
    }

    html.dark .abo-filter-label,
    html.dark .abo-card-number,
    html.dark .abo-section-header,
    html.dark .abo-booth-no,
    html.dark .abo-booth-name,
    html.dark .abo-person-name,
    html.dark .abo-table td {
        color: #f8fafc;
    }

    html.dark .abo-card-label,
    html.dark .abo-village,
    html.dark .abo-person-mobile,
    html.dark .abo-person-house,
    html.dark .abo-empty-role,
    html.dark .abo-empty {
        color: #a8a3b7;
    }

    html.dark .abo-input,
    html.dark .abo-select {
        color: #f8fafc;
        border-color: rgba(139, 92, 246, .34);
        background: #100d17;
        color-scheme: dark;
    }

    html.dark .abo-input::placeholder {
        color: #777184;
    }

    html.dark .abo-input:focus,
    html.dark .abo-select:focus {
        outline: none;
        border-color: #a78bfa;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, .20);
    }

    html.dark .abo-card {
        border-color: rgba(139, 92, 246, .23);
        background: linear-gradient(145deg, #121018 0%, #211230 100%);
        box-shadow: 0 9px 24px rgba(0, 0, 0, .25);
        transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
    }

    html.dark .abo-card:hover {
        transform: translateY(-2px);
        border-color: rgba(167, 139, 250, .60);
        box-shadow: 0 13px 30px rgba(76, 29, 149, .23);
    }

    html.dark .abo-table-section {
        border-color: rgba(139, 92, 246, .25);
        background: linear-gradient(145deg, #09080e, #100c19);
        box-shadow: 0 15px 38px rgba(0, 0, 0, .28);
    }

    html.dark .abo-section-header {
        border-color: rgba(139, 92, 246, .20);
        background: linear-gradient(90deg, #0e0b14, #1c102b);
    }

    html.dark .abo-table th {
        color: #c4b5fd;
        border-color: rgba(139, 92, 246, .18);
        background: #151020;
    }

    html.dark .abo-table td {
        border-color: rgba(148, 163, 184, .12);
    }

    html.dark .abo-table tbody tr:hover {
        background: rgba(124, 58, 237, .11);
    }

    html.dark .abo-role-count {
        color: #ddd6fe;
        border: 1px solid rgba(167, 139, 250, .24);
        background: rgba(124, 58, 237, .17);
    }

    html.dark .abo-button-reset {
        color: #ddd6fe;
        border: 1px solid rgba(167, 139, 250, .25);
        background: rgba(124, 58, 237, .16);
    }

    html.dark .abo-button-csv {
        color: #86efac;
        border: 1px solid rgba(34, 197, 94, .28);
        background: rgba(22, 101, 52, .22);
    }

    html.dark .abo-button-pdf {
        color: #fca5a5;
        border: 1px solid rgba(239, 68, 68, .28);
        background: rgba(153, 27, 27, .22);
    }

    html.dark .abo-status-complete {
        color: #86efac;
        background: rgba(22, 101, 52, .22);
    }

    html.dark .abo-status-pending {
        color: #fcd34d;
        background: rgba(146, 64, 14, .22);
    }

    @media(max-width:1100px) {

        .abo-filters {
            grid-template-columns: 1fr 1fr;
        }

        .abo-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media(max-width:700px) {

        .abo-filters {
            grid-template-columns: 1fr;
        }

        .abo-summary {
            grid-template-columns: 1fr;
        }

        .abo-export-row {
            justify-content: stretch;
            flex-direction: column;
        }

        .abo-filter-actions {
            width: 100%;
        }

        .abo-button {
            width: 100%;
        }
    }
</style>


<div class="abo-wrapper">


    {{-- =====================================================
         FILTER TOOLBAR
    ====================================================== --}}

    <div class="abo-toolbar">

        <div class="abo-filters">


            {{-- SEARCH --}}

            <div>

                <label class="abo-filter-label">
                    🔎 Search
                </label>

                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    class="abo-input"
                    placeholder="Booth, village, name, mobile..."
                >

            </div>


            {{-- VILLAGE --}}

            <div>

                <label class="abo-filter-label">
                    🏘️ Village
                </label>

                <select
                    wire:model.live="villageFilter"
                    class="abo-select"
                >

                    <option value="">
                        All Villages
                    </option>

                    @foreach($villages as $village)

                        <option value="{{ $village }}">
                            {{ $village }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- ROLE --}}

            <div>

                <label class="abo-filter-label">
                    👤 Role
                </label>

                <select
                    wire:model.live="roleFilter"
                    class="abo-select"
                >

                    <option value="">
                        All Roles
                    </option>

                    @foreach(\App\Filament\Pages\AssemblyBoothOrganisation::ROLES as $value => $label)

                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- STATUS --}}

            <div>

                <label class="abo-filter-label">
                    📊 Status
                </label>

                <select
                    wire:model.live="statusFilter"
                    class="abo-select"
                >

                    <option value="all">
                        All Booths
                    </option>

                    <option value="assigned">
                        Core Team Complete
                    </option>

                    <option value="pending">
                        Core Team Pending
                    </option>

                </select>

            </div>


            {{-- RESET --}}

            <div class="abo-filter-actions">

                <button
                    type="button"
                    wire:click="resetFilters"
                    class="abo-button abo-button-reset"
                >
                    ↻ Reset
                </button>

            </div>

        </div>


        {{-- EXPORT --}}

        <div class="abo-export-row">

            <button
                type="button"
                wire:click="exportCsv"
                class="abo-button abo-button-csv"
            >
                📊 Export CSV
            </button>


            <button
                type="button"
                wire:click="exportPdf"
                class="abo-button abo-button-pdf"
            >
                📄 Export PDF
            </button>

        </div>

    </div>


    {{-- =====================================================
         SUMMARY
    ====================================================== --}}

    <div class="abo-summary">

        <div class="abo-card">

            <div class="abo-card-label">
                🗳️ Booths
            </div>

            <div class="abo-card-number">
                {{ number_format($summary['total_booths'] ?? 0) }}
            </div>

        </div>


        <div class="abo-card">

            <div class="abo-card-label">
                👤 Booth Presidents
            </div>

            <div class="abo-card-number">
                {{ number_format($summary['presidents'] ?? 0) }}
            </div>

        </div>


        <div class="abo-card">

            <div class="abo-card-label">
                👩 Mahila Presidents
            </div>

            <div class="abo-card-number">
                {{ number_format($summary['mahila_presidents'] ?? 0) }}
            </div>

        </div>


        <div class="abo-card">

            <div class="abo-card-label">
                👦 Youth Presidents
            </div>

            <div class="abo-card-number">
                {{ number_format($summary['youth_presidents'] ?? 0) }}
            </div>

        </div>

    </div>


    {{-- =====================================================
         TABLE
    ====================================================== --}}

    <div class="abo-table-section">

        <div class="abo-section-header">

            🏛️ Assembly Booth Organisation

            <span style="
                margin-left:10px;
                font-size:11px;
                color:#9ca3af;
                font-weight:600;
            ">
                {{ $rows->count() }} booths
            </span>

        </div>


        <div class="abo-table-wrap">

            <table class="abo-table">

                <thead>

                    <tr>

                        <th>Booth</th>

                        <th>Booth President</th>

                        <th>Mahila President</th>

                        <th>Youth President</th>

                        <th>Vice President</th>

                        <th>General Secretary</th>

                        <th>Secretary</th>

                        <th>Treasurer</th>

                        <th>Panna Pramukh</th>

                        <th>Polling Agent</th>

                        <th>Volunteers</th>

                        <th>Status</th>

                    </tr>

                </thead>


                <tbody>

                @forelse($rows as $row)

                    @php

                        $president =
                            $row['president'] ?? collect();

                        $mahilaPresident =
                            $row['mahila_president'] ?? collect();

                        $youthPresident =
                            $row['youth_president'] ?? collect();

                        $vicePresident =
                            $row['vice_president'] ?? collect();

                        $generalSecretary =
                            $row['general_secretary'] ?? collect();

                        $secretary =
                            $row['secretary'] ?? collect();

                        $treasurer =
                            $row['treasurer'] ?? collect();

                        $pannaPramukh =
                            $row['panna_pramukh'] ?? collect();

                        $pollingAgent =
                            $row['polling_agent'] ?? collect();

                        $volunteers =
                            $row['volunteers'] ?? collect();

                        $allVolunteers = collect()
                            ->merge($volunteers)
                            ->merge($row['mahila_volunteers'] ?? collect())
                            ->merge($row['youth_volunteers'] ?? collect());

                        $isComplete =
                            $president->isNotEmpty()
                            &&
                            $mahilaPresident->isNotEmpty()
                            &&
                            $youthPresident->isNotEmpty();

                    @endphp


                    <tr>


                        {{-- BOOTH --}}

                        <td>

                            <div class="abo-booth-no">
                                Booth {{ $row['booth_no'] ?? '-' }}
                            </div>

                            <div class="abo-booth-name">
                                {{ $row['booth_name'] ?? '-' }}
                            </div>

                            <div class="abo-village">
                                📍 {{ $row['village'] ?? '-' }}
                            </div>

                        </td>


                        {{-- PRESIDENT --}}

                        <td>

                            @if($president->isNotEmpty())

                                @foreach($president as $person)

                                    <div class="abo-person">

                                        <div class="abo-person-name">
                                            👤 {{ $person['name'] ?? '-' }}
                                        </div>

                                        <div class="abo-person-mobile">
                                            📱 {{ $person['mobile'] ?? 'Mobile not available' }}
                                        </div>

                                        @if(!empty($person['house_no']))

                                            <div class="abo-person-house">
                                                House: {{ $person['house_no'] }}
                                            </div>

                                        @endif

                                    </div>

                                @endforeach

                            @else

                                <span class="abo-empty-role">
                                    Not Assigned
                                </span>

                            @endif

                        </td>


                        {{-- MAHILA PRESIDENT --}}

                        <td>

                            @if($mahilaPresident->isNotEmpty())

                                @foreach($mahilaPresident as $person)

                                    <div class="abo-person">

                                        <div class="abo-person-name">
                                            👩 {{ $person['name'] ?? '-' }}
                                        </div>

                                        <div class="abo-person-mobile">
                                            📱 {{ $person['mobile'] ?? 'Mobile not available' }}
                                        </div>

                                    </div>

                                @endforeach

                            @else

                                <span class="abo-empty-role">
                                    Not Assigned
                                </span>

                            @endif

                        </td>


                        {{-- YOUTH PRESIDENT --}}

                        <td>

                            @if($youthPresident->isNotEmpty())

                                @foreach($youthPresident as $person)

                                    <div class="abo-person">

                                        <div class="abo-person-name">
                                            👦 {{ $person['name'] ?? '-' }}
                                        </div>

                                        <div class="abo-person-mobile">
                                            📱 {{ $person['mobile'] ?? 'Mobile not available' }}
                                        </div>

                                    </div>

                                @endforeach

                            @else

                                <span class="abo-empty-role">
                                    Not Assigned
                                </span>

                            @endif

                        </td>


                        {{-- VICE PRESIDENT --}}

                        <td>

                            @if($vicePresident->isNotEmpty())

                                @foreach($vicePresident as $person)

                                    <div class="abo-person">

                                        <div class="abo-person-name">
                                            {{ $person['name'] ?? '-' }}
                                        </div>

                                        <div class="abo-person-mobile">
                                            📱 {{ $person['mobile'] ?? '-' }}
                                        </div>

                                    </div>

                                @endforeach

                            @else

                                <span class="abo-empty-role">
                                    Not Assigned
                                </span>

                            @endif

                        </td>


                        {{-- GENERAL SECRETARY --}}

                        <td>

                            @if($generalSecretary->isNotEmpty())

                                @foreach($generalSecretary as $person)

                                    <div class="abo-person">

                                        <div class="abo-person-name">
                                            {{ $person['name'] ?? '-' }}
                                        </div>

                                        <div class="abo-person-mobile">
                                            📱 {{ $person['mobile'] ?? '-' }}
                                        </div>

                                    </div>

                                @endforeach

                            @else

                                <span class="abo-empty-role">
                                    Not Assigned
                                </span>

                            @endif

                        </td>


                        {{-- SECRETARY --}}

                        <td>

                            @if($secretary->isNotEmpty())

                                @foreach($secretary as $person)

                                    <div class="abo-person">

                                        <div class="abo-person-name">
                                            {{ $person['name'] ?? '-' }}
                                        </div>

                                        <div class="abo-person-mobile">
                                            📱 {{ $person['mobile'] ?? '-' }}
                                        </div>

                                    </div>

                                @endforeach

                            @else

                                <span class="abo-empty-role">
                                    Not Assigned
                                </span>

                            @endif

                        </td>


                        {{-- TREASURER --}}

                        <td>

                            @if($treasurer->isNotEmpty())

                                @foreach($treasurer as $person)

                                    <div class="abo-person">

                                        <div class="abo-person-name">
                                            {{ $person['name'] ?? '-' }}
                                        </div>

                                        <div class="abo-person-mobile">
                                            📱 {{ $person['mobile'] ?? '-' }}
                                        </div>

                                    </div>

                                @endforeach

                            @else

                                <span class="abo-empty-role">
                                    Not Assigned
                                </span>

                            @endif

                        </td>


                        {{-- PANNA PRAMUKH --}}

                        <td>

                            @if($pannaPramukh->isNotEmpty())

                                <span class="abo-role-count">
                                    {{ $pannaPramukh->count() }}
                                </span>

                                @foreach($pannaPramukh as $person)

                                    <div class="abo-person">

                                        <div class="abo-person-name">
                                            {{ $person['name'] ?? '-' }}
                                        </div>

                                        <div class="abo-person-mobile">
                                            📱 {{ $person['mobile'] ?? '-' }}
                                        </div>

                                    </div>

                                @endforeach

                            @else

                                <span class="abo-empty-role">
                                    Not Assigned
                                </span>

                            @endif

                        </td>


                        {{-- POLLING AGENT --}}

                        <td>

                            @if($pollingAgent->isNotEmpty())

                                @foreach($pollingAgent as $person)

                                    <div class="abo-person">

                                        <div class="abo-person-name">
                                            {{ $person['name'] ?? '-' }}
                                        </div>

                                        <div class="abo-person-mobile">
                                            📱 {{ $person['mobile'] ?? '-' }}
                                        </div>

                                    </div>

                                @endforeach

                            @else

                                <span class="abo-empty-role">
                                    Not Assigned
                                </span>

                            @endif

                        </td>


                        {{-- VOLUNTEERS --}}

                        <td>

                            @if($allVolunteers->isNotEmpty())

                                <span class="abo-role-count">
                                    {{ $allVolunteers->count() }}
                                </span>

                                @foreach($allVolunteers as $person)

                                    <div class="abo-person">

                                        <div class="abo-person-name">
                                            {{ $person['name'] ?? '-' }}
                                        </div>

                                        <div class="abo-person-mobile">
                                            📱 {{ $person['mobile'] ?? '-' }}
                                        </div>

                                    </div>

                                @endforeach

                            @else

                                <span class="abo-empty-role">
                                    Not Assigned
                                </span>

                            @endif

                        </td>


                        {{-- STATUS --}}

                        <td>

                            @if($isComplete)

                                <span class="abo-status abo-status-complete">
                                    ✓ Core Team Complete
                                </span>

                            @else

                                <span class="abo-status abo-status-pending">
                                    ⚠ Core Team Pending
                                </span>

                            @endif

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td colspan="12">

                            <div class="abo-empty">

                                🔎 No booths found for the selected filters.

                            </div>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

</x-filament-panels::page>
