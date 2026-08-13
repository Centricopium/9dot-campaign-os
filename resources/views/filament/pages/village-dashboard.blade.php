<x-filament-panels::page>

    @php
        $summary = $this->summary;
        $village = $this->selectedVillage;
    @endphp

    <style>
        .village-dashboard-grid {
            display: grid;
            gap: 16px;
        }

        .village-dashboard-grid-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .village-dashboard-card {
            border: 1px solid rgb(55 65 81 / 0.45);
            border-radius: 12px;
            padding: 20px;
            background: rgb(24 24 27 / 0.65);
            min-width: 0;
        }

        .village-dashboard-icon {
            font-size: 24px;
            line-height: 1;
            margin-bottom: 10px;
        }

        .village-dashboard-label {
            font-size: 14px;
            color: rgb(156 163 175);
        }

        .village-dashboard-number {
            font-size: 30px;
            line-height: 1.2;
            font-weight: 700;
            margin-top: 8px;
        }

        .village-dashboard-subtitle {
            font-size: 13px;
            margin-top: 6px;
            color: rgb(156 163 175);
        }

        .village-dashboard-value-card {
            text-align: center;
        }

        .village-dashboard-value {
            font-size: 28px;
            font-weight: 700;
            margin-top: 8px;
        }

        .village-dashboard-party-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .village-dashboard-party-total {
            font-size: 30px;
            font-weight: 700;
        }

        .village-dashboard-party-name {
            font-size: 16px;
            font-weight: 700;
            margin-top: 6px;
        }

        .village-dashboard-party-full-name {
            font-size: 12px;
            color: rgb(156 163 175);
            margin-top: 3px;
        }

        .village-dashboard-party-list {
            margin-top: 18px;
            display: grid;
            gap: 8px;
        }

        .village-dashboard-party-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            font-size: 13px;
            padding-bottom: 6px;
            border-bottom: 1px solid rgb(55 65 81 / 0.35);
        }

        .village-dashboard-party-row:last-child {
            border-bottom: 0;
        }

        .village-dashboard-party-count {
            font-weight: 600;
        }

        .village-dashboard-info-value {
            font-size: 15px;
            font-weight: 600;
            margin-top: 5px;
        }

        @media (max-width: 1024px) {
            .village-dashboard-grid-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .village-dashboard-grid-4 {
                grid-template-columns: 1fr;
            }
        }
    </style>


    {{-- Village Selection --}}
    <x-filament::section>

        <x-slot name="heading">
            🏘️ Village Dashboard
        </x-slot>

        <div style="max-width: 600px;">

            <label
                for="village-select"
                style="display:block; font-size:14px; font-weight:600; margin-bottom:8px;"
            >
                Select Village
            </label>

            <select
                id="village-select"
                wire:model.live="villageId"
                style="
                    width:100%;
                    border-radius:8px;
                    padding:10px 12px;
                    border:1px solid rgb(75 85 99);
                    background:rgb(24 24 27);
                    color:inherit;
                "
            >

                <option value="">
                    -- Select Village --
                </option>

                @foreach ($this->villages as $item)

                    <option value="{{ $item->id }}">
                        {{ $item->name }}
                        @if ($item->taluka)
                            — {{ $item->taluka }}
                        @endif
                    </option>

                @endforeach

            </select>

        </div>

    </x-filament::section>


    @if ($village)

        {{-- Overview --}}
        <div
            class="village-dashboard-grid village-dashboard-grid-4"
            style="margin-top:20px;"
        >

            <div class="village-dashboard-card">

                <div class="village-dashboard-icon">
                    🏠
                </div>

                <div class="village-dashboard-label">
                    Houses
                </div>

                <div class="village-dashboard-number">
                    {{ number_format($summary['houses']) }}
                </div>

                <div class="village-dashboard-subtitle">
                    Total Houses
                </div>

            </div>


            <div class="village-dashboard-card">

                <div class="village-dashboard-icon">
                    🗳️
                </div>

                <div class="village-dashboard-label">
                    Booths
                </div>

                <div class="village-dashboard-number">
                    {{ number_format($summary['booths']) }}
                </div>

                <div class="village-dashboard-subtitle">
                    Total Booths
                </div>

            </div>


            <div class="village-dashboard-card">

                <div class="village-dashboard-icon">
                    👥
                </div>

                <div class="village-dashboard-label">
                    Voters
                </div>

                <div class="village-dashboard-number">
                    {{ number_format($summary['voters']) }}
                </div>

                <div class="village-dashboard-subtitle">
                    Total Voters
                </div>

            </div>


            <div class="village-dashboard-card">

                <div class="village-dashboard-icon">
                    🙋
                </div>

                <div class="village-dashboard-label">
                    Volunteers
                </div>

                <div class="village-dashboard-number">
                    {{ number_format($summary['volunteers']) }}
                </div>

                <div class="village-dashboard-subtitle">
                    Village Volunteers
                </div>

            </div>

        </div>


        {{-- Influencers --}}
        <div
            class="village-dashboard-grid village-dashboard-grid-4"
            style="margin-top:16px;"
        >

            <div class="village-dashboard-card">

                <div class="village-dashboard-icon">
                    ⭐
                </div>

                <div class="village-dashboard-label">
                    Influencers
                </div>

                <div class="village-dashboard-number">
                    {{ number_format($summary['influencers']) }}
                </div>

                <div class="village-dashboard-subtitle">
                    Village Influencers
                </div>

            </div>

        </div>


        {{-- Demographics --}}
        <x-filament::section style="margin-top:20px;">

            <x-slot name="heading">
                👥 Voter Demographics
            </x-slot>

            <div class="village-dashboard-grid village-dashboard-grid-4">

                <div class="village-dashboard-card village-dashboard-value-card">
                    <div class="village-dashboard-label">Total Voters</div>
                    <div class="village-dashboard-value">
                        {{ number_format($summary['voters']) }}
                    </div>
                </div>

                <div class="village-dashboard-card village-dashboard-value-card">
                    <div class="village-dashboard-label">Male</div>
                    <div class="village-dashboard-value">
                        {{ number_format($summary['male']) }}
                    </div>
                </div>

                <div class="village-dashboard-card village-dashboard-value-card">
                    <div class="village-dashboard-label">Female</div>
                    <div class="village-dashboard-value">
                        {{ number_format($summary['female']) }}
                    </div>
                </div>

                <div class="village-dashboard-card village-dashboard-value-card">
                    <div class="village-dashboard-label">Other</div>
                    <div class="village-dashboard-value">
                        {{ number_format($summary['other']) }}
                    </div>
                </div>

            </div>

        </x-filament::section>


        {{-- Political Overview --}}
        <x-filament::section style="margin-top:20px;">

            <x-slot name="heading">
                📊 Political Overview
            </x-slot>

            <div class="village-dashboard-grid village-dashboard-grid-4">

                <div class="village-dashboard-card village-dashboard-value-card">
                    <div class="village-dashboard-label">
                        Neutral
                    </div>

                    <div class="village-dashboard-value">
                        {{ number_format($summary['neutral']) }}
                    </div>
                </div>

                <div class="village-dashboard-card village-dashboard-value-card">
                    <div class="village-dashboard-label">
                        Undecided
                    </div>

                    <div class="village-dashboard-value">
                        {{ number_format($summary['undecided']) }}
                    </div>
                </div>

                <div class="village-dashboard-card village-dashboard-value-card">
                    <div class="village-dashboard-label">
                        Active Voters
                    </div>

                    <div class="village-dashboard-value">
                        {{ number_format($summary['active_voters']) }}
                    </div>
                </div>

                <div class="village-dashboard-card village-dashboard-value-card">
                    <div class="village-dashboard-label">
                        Inactive Voters
                    </div>

                    <div class="village-dashboard-value">
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


        {{-- Party Support --}}
        <x-filament::section style="margin-top:20px;">

            <x-slot name="heading">
                🗳️ Party Support
            </x-slot>

            <div class="village-dashboard-grid village-dashboard-grid-4">

                @foreach ($summary['parties'] as $party)

                    <div class="village-dashboard-card">

                        <div class="village-dashboard-party-header">

                            <div>

                                <div class="village-dashboard-icon">
                                    {{ $party['symbol'] ?: '🏛️' }}
                                </div>

                                <div class="village-dashboard-party-name">
                                    {{ $party['short_name'] }}
                                </div>

                                <div class="village-dashboard-party-full-name">
                                    {{ $party['name'] }}
                                </div>

                            </div>

                            <div class="village-dashboard-party-total">
                                {{ number_format($party['total']) }}
                            </div>

                        </div>


                        <div class="village-dashboard-party-list">

                            <div class="village-dashboard-party-row">
                                <span>Strong Congress</span>
                                <span class="village-dashboard-party-count">
                                    {{ number_format($party['strong_congress']) }}
                                </span>
                            </div>

                            <div class="village-dashboard-party-row">
                                <span>Congress Leaning</span>
                                <span class="village-dashboard-party-count">
                                    {{ number_format($party['congress_leaning']) }}
                                </span>
                            </div>

                            <div class="village-dashboard-party-row">
                                <span>Neutral</span>
                                <span class="village-dashboard-party-count">
                                    {{ number_format($party['neutral']) }}
                                </span>
                            </div>

                            <div class="village-dashboard-party-row">
                                <span>Undecided</span>
                                <span class="village-dashboard-party-count">
                                    {{ number_format($party['undecided']) }}
                                </span>
                            </div>

                            <div class="village-dashboard-party-row">
                                <span>BJP Leaning</span>
                                <span class="village-dashboard-party-count">
                                    {{ number_format($party['bjp_leaning']) }}
                                </span>
                            </div>

                            <div class="village-dashboard-party-row">
                                <span>Strong BJP</span>
                                <span class="village-dashboard-party-count">
                                    {{ number_format($party['strong_bjp']) }}
                                </span>
                            </div>

                            <div class="village-dashboard-party-row">
                                <span>Other</span>
                                <span class="village-dashboard-party-count">
                                    {{ number_format($party['other']) }}
                                </span>
                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        </x-filament::section>


        {{-- Village Information --}}
        <x-filament::section style="margin-top:20px;">

            <x-slot name="heading">
                🏘️ Village Information
            </x-slot>

            <div class="village-dashboard-grid village-dashboard-grid-4">

                <div class="village-dashboard-card">

                    <div class="village-dashboard-label">
                        Constituency
                    </div>

                    <div class="village-dashboard-info-value">
                        {{ $village->constituency?->name ?? '-' }}
                    </div>

                </div>


                <div class="village-dashboard-card">

                    <div class="village-dashboard-label">
                        Village
                    </div>

                    <div class="village-dashboard-info-value">
                        {{ $village->name }}
                    </div>

                </div>


                <div class="village-dashboard-card">

                    <div class="village-dashboard-label">
                        Taluka
                    </div>

                    <div class="village-dashboard-info-value">
                        {{ $village->taluka ?: '-' }}
                    </div>

                </div>


                <div class="village-dashboard-card">

                    <div class="village-dashboard-label">
                        District
                    </div>

                    <div class="village-dashboard-info-value">
                        {{ $village->district ?: '-' }}
                    </div>

                </div>


                <div class="village-dashboard-card">

                    <div class="village-dashboard-label">
                        Category
                    </div>

                    <div class="village-dashboard-info-value">
                        {{ $village->category ?: '-' }}
                    </div>

                </div>


                <div class="village-dashboard-card">

                    <div class="village-dashboard-label">
                        Total Booths
                    </div>

                    <div class="village-dashboard-info-value">
                        {{ number_format($summary['booths']) }}
                    </div>

                </div>

            </div>

        </x-filament::section>

    @endif

</x-filament-panels::page>