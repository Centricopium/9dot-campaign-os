<x-filament-panels::page>

    @php
        $summary = $this->summary;
        $constituency = $this->selectedConstituency;
    @endphp

    <style>
        .cd-grid {
            display: grid;
            gap: 16px;
        }

        .cd-grid-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .cd-card {
            border: 1px solid rgb(55 65 81 / 0.45);
            border-radius: 14px;
            padding: 20px;
            background: rgb(24 24 27 / 0.65);
            min-width: 0;
        }

        .cd-icon {
            font-size: 25px;
            line-height: 1;
            margin-bottom: 10px;
        }

        .cd-label {
            font-size: 13px;
            color: rgb(156 163 175);
        }

        .cd-number {
            font-size: 30px;
            line-height: 1.2;
            font-weight: 700;
            margin-top: 7px;
        }

        .cd-subtitle {
            font-size: 12px;
            margin-top: 6px;
            color: rgb(156 163 175);
        }

        .cd-center {
            text-align: center;
        }

        .cd-value {
            font-size: 27px;
            font-weight: 700;
            margin-top: 7px;
        }

        .cd-party-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .cd-party-total {
            font-size: 30px;
            font-weight: 700;
        }

        .cd-party-name {
            font-size: 16px;
            font-weight: 700;
            margin-top: 5px;
        }

        .cd-party-full-name {
            font-size: 12px;
            color: rgb(156 163 175);
            margin-top: 3px;
        }

        .cd-party-list {
            margin-top: 18px;
            display: grid;
            gap: 8px;
        }

        .cd-party-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-size: 13px;
            padding-bottom: 7px;
            border-bottom: 1px solid rgb(55 65 81 / 0.35);
        }

        .cd-party-row:last-child {
            border-bottom: 0;
        }

        .cd-party-count {
            font-weight: 700;
        }

        .cd-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .cd-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cd-table th,
        .cd-table td {
            padding: 13px 12px;
            border-bottom: 1px solid rgb(55 65 81 / 0.35);
            text-align: left;
            font-size: 13px;
            white-space: nowrap;
        }

        .cd-table th {
            color: rgb(156 163 175);
            font-weight: 600;
        }

        .cd-table tbody tr:hover {
            background: rgb(39 39 42 / 0.45);
        }

        .cd-info-value {
            font-size: 15px;
            font-weight: 600;
            margin-top: 5px;
        }

        @media (max-width: 1100px) {
            .cd-grid-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .cd-grid-4 {
                grid-template-columns: 1fr;
            }
        }
    </style>


    {{-- ========================================================= --}}
    {{-- CONSTITUENCY SELECTOR --}}
    {{-- ========================================================= --}}

    <x-filament::section>

        <x-slot name="heading">
            🏛️ Constituency Dashboard
        </x-slot>

        <x-slot name="description">
            Select a constituency to view complete campaign data.
        </x-slot>

        <div style="max-width: 650px;">

            <label
                for="constituency-select"
                style="display:block;font-size:14px;font-weight:600;margin-bottom:8px;"
            >
                Select Constituency
            </label>

            <select
                id="constituency-select"
                wire:model.live="constituencyId"
                style="
                    width:100%;
                    border-radius:9px;
                    padding:11px 13px;
                    border:1px solid rgb(75 85 99);
                    background:rgb(24 24 27);
                    color:inherit;
                "
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

    </x-filament::section>


    @if ($constituency)

        {{-- ========================================================= --}}
        {{-- MAIN OVERVIEW --}}
        {{-- ========================================================= --}}

        <div
            class="cd-grid cd-grid-4"
            style="margin-top:20px;"
        >

            <div class="cd-card">
                <div class="cd-icon">🏘️</div>

                <div class="cd-label">
                    Villages
                </div>

                <div class="cd-number">
                    {{ number_format($summary['villages']) }}
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
                    {{ number_format($summary['booths']) }}
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
                    {{ number_format($summary['houses']) }}
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
                    {{ number_format($summary['voters']) }}
                </div>

                <div class="cd-subtitle">
                    Total Voters
                </div>
            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- CAMPAIGN PEOPLE --}}
        {{-- ========================================================= --}}

        <div
            class="cd-grid cd-grid-4"
            style="margin-top:16px;"
        >

            <div class="cd-card">
                <div class="cd-icon">🙋</div>

                <div class="cd-label">
                    Volunteers
                </div>

                <div class="cd-number">
                    {{ number_format($summary['volunteers']) }}
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
                    {{ number_format($summary['influencers']) }}
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
                    {{ number_format($summary['active_voters']) }}
                </div>

                <div class="cd-subtitle">
                    Active Electoral Roll
                </div>
            </div>


            <div class="cd-card">
                <div class="cd-icon">⏳</div>

                <div class="cd-label">
                    Undecided
                </div>

                <div class="cd-number">
                    {{ number_format($summary['undecided']) }}
                </div>

                <div class="cd-subtitle">
                    Undecided Voters
                </div>
            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- VOTER DEMOGRAPHICS --}}
        {{-- ========================================================= --}}

        <x-filament::section style="margin-top:20px;">

            <x-slot name="heading">
                👥 Voter Demographics
            </x-slot>

            <div class="cd-grid cd-grid-4">

                <div class="cd-card cd-center">
                    <div class="cd-label">
                        Total Voters
                    </div>

                    <div class="cd-value">
                        {{ number_format($summary['voters']) }}
                    </div>
                </div>


                <div class="cd-card cd-center">
                    <div class="cd-label">
                        Male
                    </div>

                    <div class="cd-value">
                        {{ number_format($summary['male']) }}
                    </div>
                </div>


                <div class="cd-card cd-center">
                    <div class="cd-label">
                        Female
                    </div>

                    <div class="cd-value">
                        {{ number_format($summary['female']) }}
                    </div>
                </div>


                <div class="cd-card cd-center">
                    <div class="cd-label">
                        Other
                    </div>

                    <div class="cd-value">
                        {{ number_format($summary['other']) }}
                    </div>
                </div>

            </div>

        </x-filament::section>


        {{-- ========================================================= --}}
        {{-- POLITICAL OVERVIEW --}}
        {{-- ========================================================= --}}

        <x-filament::section style="margin-top:20px;">

            <x-slot name="heading">
                📊 Political Overview
            </x-slot>

            <div class="cd-grid cd-grid-4">

                <div class="cd-card cd-center">
                    <div class="cd-label">
                        Neutral
                    </div>

                    <div class="cd-value">
                        {{ number_format($summary['neutral']) }}
                    </div>
                </div>


                <div class="cd-card cd-center">
                    <div class="cd-label">
                        Undecided
                    </div>

                    <div class="cd-value">
                        {{ number_format($summary['undecided']) }}
                    </div>
                </div>


                <div class="cd-card cd-center">
                    <div class="cd-label">
                        Active Voters
                    </div>

                    <div class="cd-value">
                        {{ number_format($summary['active_voters']) }}
                    </div>
                </div>


                <div class="cd-card cd-center">
                    <div class="cd-label">
                        Inactive Voters
                    </div>

                    <div class="cd-value">
                        {{
                            number_format(
                                max(
                                    0,
                                    $summary['voters'] - $summary['active_voters']
                                )
                            )
                        }}
                    </div>
                </div>

            </div>

        </x-filament::section>


        {{-- ========================================================= --}}
        {{-- PARTY SUPPORT --}}
        {{-- ========================================================= --}}

        <x-filament::section style="margin-top:20px;">

            <x-slot name="heading">
                🗳️ Party Support
            </x-slot>

            <div class="cd-grid cd-grid-4">

                @forelse ($summary['parties'] as $party)

                    <div class="cd-card">

                        <div class="cd-party-header">

                            <div>

                                <div class="cd-icon">
                                    {{ $party['symbol'] ?: '🏛️' }}
                                </div>

                                <div class="cd-party-name">
                                    {{ $party['short_name'] }}
                                </div>

                                <div class="cd-party-full-name">
                                    {{ $party['name'] }}
                                </div>

                            </div>

                            <div class="cd-party-total">
                                {{ number_format($party['total']) }}
                            </div>

                        </div>


                        <div class="cd-party-list">

                            <div class="cd-party-row">
                                <span>Strong Congress</span>
                                <span class="cd-party-count">
                                    {{ number_format($party['strong_congress']) }}
                                </span>
                            </div>

                            <div class="cd-party-row">
                                <span>Congress Leaning</span>
                                <span class="cd-party-count">
                                    {{ number_format($party['congress_leaning']) }}
                                </span>
                            </div>

                            <div class="cd-party-row">
                                <span>Neutral</span>
                                <span class="cd-party-count">
                                    {{ number_format($party['neutral']) }}
                                </span>
                            </div>

                            <div class="cd-party-row">
                                <span>Undecided</span>
                                <span class="cd-party-count">
                                    {{ number_format($party['undecided']) }}
                                </span>
                            </div>

                            <div class="cd-party-row">
                                <span>BJP Leaning</span>
                                <span class="cd-party-count">
                                    {{ number_format($party['bjp_leaning']) }}
                                </span>
                            </div>

                            <div class="cd-party-row">
                                <span>Strong BJP</span>
                                <span class="cd-party-count">
                                    {{ number_format($party['strong_bjp']) }}
                                </span>
                            </div>

                            <div class="cd-party-row">
                                <span>Other</span>
                                <span class="cd-party-count">
                                    {{ number_format($party['other']) }}
                                </span>
                            </div>

                        </div>

                    </div>

                @empty

                    <div class="cd-card">
                        No active political parties found.
                    </div>

                @endforelse

            </div>

        </x-filament::section>


        {{-- ========================================================= --}}
        {{-- VILLAGE-WISE SUMMARY --}}
        {{-- ========================================================= --}}

        <x-filament::section style="margin-top:20px;">

            <x-slot name="heading">
                🏘️ Village-wise Summary
            </x-slot>

            <x-slot name="description">
                Village-level campaign strength and voter distribution.
            </x-slot>

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

                        @forelse ($summary['villages_list'] as $index => $village)

                            <tr>

                                <td>
                                    {{ $index + 1 }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $village['name'] ?? '-' }}
                                    </strong>
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

                                <td>
                                    <strong>
                                        {{ number_format($village['voters'] ?? 0) }}
                                    </strong>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6">
                                    No villages found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </x-filament::section>


        {{-- ========================================================= --}}
        {{-- CONSTITUENCY INFORMATION --}}
        {{-- ========================================================= --}}

        <x-filament::section style="margin-top:20px;">

            <x-slot name="heading">
                🏛️ Constituency Information
            </x-slot>

            <div class="cd-grid cd-grid-4">

                <div class="cd-card">

                    <div class="cd-label">
                        Constituency
                    </div>

                    <div class="cd-info-value">
                        {{ $constituency->name }}
                    </div>

                </div>


                <div class="cd-card">

                    <div class="cd-label">
                        District
                    </div>

                    <div class="cd-info-value">
                        {{ $constituency->district ?: '-' }}
                    </div>

                </div>


                <div class="cd-card">

                    <div class="cd-label">
                        State
                    </div>

                    <div class="cd-info-value">
                        {{ $constituency->state ?: '-' }}
                    </div>

                </div>


                <div class="cd-card">

                    <div class="cd-label">
                        Status
                    </div>

                    <div class="cd-info-value">
                        {{ $constituency->is_active ? 'Active' : 'Inactive' }}
                    </div>

                </div>

            </div>

        </x-filament::section>

    @endif

</x-filament-panels::page>