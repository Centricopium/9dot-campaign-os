<x-filament-panels::page>

    @php

    $summary = $this->summary ?? [];

    $village = $this->selectedVillage;

    $boothIntelligence = $this->boothIntelligence;

@endphp

    <style>
        /* =========================================================
           VILLAGE DASHBOARD — LIGHT / WHITE THEME
        ========================================================== */

        .village-dashboard {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .vd-grid {
            display: grid;
            gap: 16px;
        }

        .vd-grid-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .vd-grid-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .vd-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 20px;
            min-width: 0;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
            transition:
                transform .18s ease,
                box-shadow .18s ease,
                border-color .18s ease;
        }

        .vd-card:hover {
            transform: translateY(-2px);
            border-color: #d1d5db;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        }

        .vd-icon {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #f3f4f6;
            font-size: 22px;
            margin-bottom: 14px;
        }

        .vd-label {
            font-size: 13px;
            font-weight: 500;
            color: #6b7280;
        }

        .vd-number {
            margin-top: 6px;
            font-size: 30px;
            line-height: 1.15;
            font-weight: 800;
            color: #111827;
        }

        .vd-value {
            margin-top: 6px;
            font-size: 27px;
            line-height: 1.15;
            font-weight: 800;
            color: #111827;
        }

        .vd-subtitle {
            margin-top: 6px;
            font-size: 12px;
            color: #9ca3af;
        }

        .vd-center {
            text-align: center;
        }

        .vd-center .vd-icon {
            margin-left: auto;
            margin-right: auto;
        }

        /* =========================================================
           HEADER / SELECTOR
        ========================================================== */

        .vd-selector {
            background: linear-gradient(
                135deg,
                #ffffff 0%,
                #f8fafc 100%
            );
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
        }

        .vd-selector-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 19px;
            font-weight: 750;
            color: #111827;
        }

        .vd-selector-description {
            margin-top: 5px;
            color: #6b7280;
            font-size: 13px;
        }

        .vd-select-label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 650;
            color: #374151;
        }

        .vd-select {
            width: 100%;
            min-height: 44px;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: #111827;
            padding: 9px 12px;
            outline: none;
            transition:
                border-color .15s ease,
                box-shadow .15s ease;
        }

        .vd-select:focus {
            border-color: #9ca3af;
            box-shadow: 0 0 0 3px rgba(156, 163, 175, .18);
        }

        /* =========================================================
           SECTION
        ========================================================== */

        .vd-section {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
        }

        .vd-section-header {
            padding: 17px 20px;
            border-bottom: 1px solid #eef0f2;
            background: #ffffff;
        }

        .vd-section-title {
            font-size: 15px;
            font-weight: 750;
            color: #111827;
        }

        .vd-section-description {
            margin-top: 4px;
            font-size: 12px;
            color: #6b7280;
        }

        .vd-section-body {
            padding: 16px;
        }

        /* =========================================================
           POLITICAL SUPPORT
        ========================================================== */

        .vd-support-card {
            position: relative;
            overflow: hidden;
        }

        .vd-support-card::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
            background: #d1d5db;
        }

        .vd-support-strong::after {
            background: #22c55e;
        }

        .vd-support-moderate::after {
            background: #84cc16;
        }

        .vd-support-leaning::after {
            background: #eab308;
        }

        .vd-support-neutral::after {
            background: #9ca3af;
        }

        .vd-support-undecided::after {
            background: #f97316;
        }

        .vd-support-opposition::after {
            background: #ef4444;
        }

        .vd-support-number {
            margin-top: 7px;
            font-size: 26px;
            font-weight: 800;
            color: #111827;
        }

        /* =========================================================
           PARTY CARDS
        ========================================================== */

        .vd-party-card {
            position: relative;
            overflow: hidden;
        }

        .vd-party-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .vd-party-left {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            min-width: 0;
        }

        .vd-party-accent {
            width: 4px;
            min-height: 50px;
            border-radius: 999px;
            background: #9ca3af;
        }

        .vd-party-symbol {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 11px;
            background: #f3f4f6;
            font-size: 20px;
        }

        .vd-party-name {
            margin-top: 2px;
            font-size: 16px;
            font-weight: 750;
            color: #111827;
        }

        .vd-party-full-name {
            margin-top: 3px;
            font-size: 11px;
            color: #6b7280;
        }

        .vd-party-total {
            font-size: 28px;
            line-height: 1;
            font-weight: 800;
            color: #111827;
        }

        .vd-party-list {
            margin-top: 18px;
            display: flex;
            flex-direction: column;
        }

        .vd-party-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 9px 0;
            border-bottom: 1px solid #f0f1f3;
            font-size: 12px;
            color: #4b5563;
        }

        .vd-party-row:last-child {
            border-bottom: 0;
        }

        .vd-party-count {
            font-weight: 750;
            color: #111827;
        }

        /* =========================================================
           INFORMATION
        ========================================================== */

        .vd-info-card {
            min-height: 95px;
        }

        .vd-info-value {
            margin-top: 6px;
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }

        /* =========================================================
           EMPTY STATE
        ========================================================== */

        .vd-empty {
            padding: 70px 20px;
            text-align: center;
        }

        .vd-empty-icon {
            width: 68px;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            border-radius: 18px;
            background: #f3f4f6;
            font-size: 32px;
        }

        .vd-empty-title {
            font-size: 21px;
            font-weight: 800;
            color: #111827;
        }

        .vd-empty-description {
            margin-top: 7px;
            color: #6b7280;
            font-size: 13px;
        }

        /* =========================================================
           DARK THEME — PURPLE / CHARCOAL
        ========================================================== */

        html.dark .vd-selector {
            border-color: rgba(139, 92, 246, .34);
            background: linear-gradient(135deg, #08070d 0%, #151022 55%, #291044 100%);
            box-shadow: 0 18px 42px rgba(0, 0, 0, .34), 0 0 35px rgba(124, 58, 237, .09);
        }

        html.dark .vd-selector-title,
        html.dark .vd-section-title,
        html.dark .vd-number,
        html.dark .vd-value,
        html.dark .vd-support-number,
        html.dark .vd-party-name,
        html.dark .vd-party-total,
        html.dark .vd-party-count,
        html.dark .vd-info-value,
        html.dark .vd-empty-title {
            color: #f8fafc;
        }

        html.dark .vd-selector-description,
        html.dark .vd-section-description,
        html.dark .vd-label,
        html.dark .vd-party-full-name,
        html.dark .vd-empty-description {
            color: #a8a3b7;
        }

        html.dark .vd-subtitle {
            color: #777184;
        }

        html.dark .vd-select-label {
            color: #d8d4e3;
        }

        html.dark .vd-select {
            color: #f8fafc;
            border-color: rgba(139, 92, 246, .34);
            background: #100d17;
            color-scheme: dark;
        }

        html.dark .vd-select:focus {
            border-color: #a78bfa;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, .20);
        }

        html.dark .vd-section {
            border-color: rgba(139, 92, 246, .25);
            background: linear-gradient(145deg, #09080e, #100c19);
            box-shadow: 0 15px 38px rgba(0, 0, 0, .28);
        }

        html.dark .vd-section-header {
            border-color: rgba(139, 92, 246, .20);
            background: linear-gradient(90deg, #0e0b14, #1c102b);
        }

        html.dark .vd-section-body {
            background: rgba(7, 6, 11, .40);
        }

        html.dark .vd-card {
            border-color: rgba(139, 92, 246, .22);
            background: linear-gradient(145deg, #121018 0%, #191126 100%);
            box-shadow: 0 7px 18px rgba(0, 0, 0, .22);
        }

        html.dark .vd-card:hover {
            border-color: rgba(167, 139, 250, .62);
            background: linear-gradient(145deg, #171221, #25143b);
            box-shadow: 0 13px 30px rgba(76, 29, 149, .23);
        }

        html.dark .vd-icon,
        html.dark .vd-party-symbol,
        html.dark .vd-empty-icon {
            color: #ddd6fe;
            border: 1px solid rgba(167, 139, 250, .22);
            background: rgba(124, 58, 237, .17);
        }

        html.dark .vd-party-row {
            color: #c4bfce;
            border-color: rgba(148, 163, 184, .12);
        }

        /* =========================================================
           RESPONSIVE
        ========================================================== */

        @media (max-width: 1100px) {
            .vd-grid-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .vd-grid-3 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .vd-grid-4,
            .vd-grid-3 {
                grid-template-columns: 1fr;
            }

            .vd-card {
                padding: 17px;
            }

            .vd-number {
                font-size: 27px;
            }

            .vd-value {
                font-size: 25px;
            }

            .vd-selector {
                padding: 18px;
            }
        }
    </style>


    <div class="village-dashboard">


        {{-- =========================================================
             VILLAGE SELECTOR
        ========================================================== --}}

        <div class="vd-selector">

            <div class="vd-selector-title">
                <span>🏘️</span>
                <span>Village Dashboard</span>
            </div>

            <div class="vd-selector-description">
                Select a village to view complete village intelligence.
            </div>

            <div style="margin-top:18px; max-width:650px;">

                <label
                    for="village-select"
                    class="vd-select-label"
                >
                    Select Village
                </label>

                <select
                    id="village-select"
                    wire:model.live="villageId"
                    class="vd-select"
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

        </div>


        @if ($village)


            {{-- =====================================================
                 OVERVIEW
            ====================================================== --}}

            <div class="vd-grid vd-grid-4">

                <div class="vd-card">

                    <div class="vd-icon">
                        🏠
                    </div>

                    <div class="vd-label">
                        Houses
                    </div>

                    <div class="vd-number">
                        {{ number_format($summary['houses'] ?? 0) }}
                    </div>

                    <div class="vd-subtitle">
                        Total Houses
                    </div>

                </div>


                <div class="vd-card">

                    <div class="vd-icon">
                        🗳️
                    </div>

                    <div class="vd-label">
                        Booths
                    </div>

                    <div class="vd-number">
                        {{ number_format($summary['booths'] ?? 0) }}
                    </div>

                    <div class="vd-subtitle">
                        Total Booths
                    </div>

                </div>


                <div class="vd-card">

                    <div class="vd-icon">
                        👥
                    </div>

                    <div class="vd-label">
                        Voters
                    </div>

                    <div class="vd-number">
                        {{ number_format($summary['voters'] ?? 0) }}
                    </div>

                    <div class="vd-subtitle">
                        Total Voters
                    </div>

                </div>


                <div class="vd-card">

                    <div class="vd-icon">
                        🙋
                    </div>

                    <div class="vd-label">
                        Volunteers
                    </div>

                    <div class="vd-number">
                        {{ number_format($summary['volunteers'] ?? 0) }}
                    </div>

                    <div class="vd-subtitle">
                        Village Volunteers
                    </div>

                </div>

            </div>


            {{-- =====================================================
                 CAMPAIGN PEOPLE
            ====================================================== --}}

            <div class="vd-grid vd-grid-4">

                <div class="vd-card">

                    <div class="vd-icon">
                        ⭐
                    </div>

                    <div class="vd-label">
                        Influencers
                    </div>

                    <div class="vd-number">
                        {{ number_format($summary['influencers'] ?? 0) }}
                    </div>

                    <div class="vd-subtitle">
                        Village Influencers
                    </div>

                </div>


                <div class="vd-card">

                    <div class="vd-icon">
                        ✅
                    </div>

                    <div class="vd-label">
                        Active Voters
                    </div>

                    <div class="vd-number">
                        {{ number_format($summary['active_voters'] ?? 0) }}
                    </div>

                    <div class="vd-subtitle">
                        Active Electoral Roll
                    </div>

                </div>


                <div class="vd-card">

                    <div class="vd-icon">
                        🤝
                    </div>

                    <div class="vd-label">
                        Neutral
                    </div>

                    <div class="vd-number">
                        {{ number_format($summary['neutral'] ?? 0) }}
                    </div>

                    <div class="vd-subtitle">
                        Neutral Voters
                    </div>

                </div>


                <div class="vd-card">

                    <div class="vd-icon">
                        ⏳
                    </div>

                    <div class="vd-label">
                        Undecided
                    </div>

                    <div class="vd-number">
                        {{ number_format($summary['undecided'] ?? 0) }}
                    </div>

                    <div class="vd-subtitle">
                        Voters Requiring Follow-up
                    </div>

                </div>

            </div>


            {{-- =====================================================
                 VOTER DEMOGRAPHICS
            ====================================================== --}}

            <div class="vd-section">

                <div class="vd-section-header">

                    <div class="vd-section-title">
                        👥 Voter Demographics
                    </div>

                    <div class="vd-section-description">
                        Demographic distribution of voters in this village.
                    </div>

                </div>

                <div class="vd-section-body">

                    <div class="vd-grid vd-grid-4">

                        <div class="vd-card vd-center">

                            <div class="vd-icon">
                                👥
                            </div>

                            <div class="vd-label">
                                Total Voters
                            </div>

                            <div class="vd-value">
                                {{ number_format($summary['voters'] ?? 0) }}
                            </div>

                        </div>


                        <div class="vd-card vd-center">

                            <div class="vd-icon">
                                👨
                            </div>

                            <div class="vd-label">
                                Male
                            </div>

                            <div class="vd-value">
                                {{ number_format($summary['male'] ?? 0) }}
                            </div>

                        </div>


                        <div class="vd-card vd-center">

                            <div class="vd-icon">
                                👩
                            </div>

                            <div class="vd-label">
                                Female
                            </div>

                            <div class="vd-value">
                                {{ number_format($summary['female'] ?? 0) }}
                            </div>

                        </div>


                        <div class="vd-card vd-center">

                            <div class="vd-icon">
                                👤
                            </div>

                            <div class="vd-label">
                                Other
                            </div>

                            <div class="vd-value">
                                {{ number_format($summary['other'] ?? 0) }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 POLITICAL SUPPORT
            ====================================================== --}}

            <div class="vd-section">

                <div class="vd-section-header">

                    <div class="vd-section-title">
                        📊 Political Support
                    </div>

                    <div class="vd-section-description">
                        Complete voter support-level distribution for this village.
                    </div>

                </div>

                <div class="vd-section-body">

                    <div class="vd-grid vd-grid-4">

                        <div class="vd-card vd-center vd-support-card vd-support-strong">

                            <div class="vd-label">
                                Strong Support
                            </div>

                            <div class="vd-support-number">
                                {{ number_format($summary['strong_support'] ?? 0) }}
                            </div>

                        </div>


                        <div class="vd-card vd-center vd-support-card vd-support-moderate">

                            <div class="vd-label">
                                Moderate Support
                            </div>

                            <div class="vd-support-number">
                                {{ number_format($summary['moderate_support'] ?? 0) }}
                            </div>

                        </div>


                        <div class="vd-card vd-center vd-support-card vd-support-leaning">

                            <div class="vd-label">
                                Leaning Support
                            </div>

                            <div class="vd-support-number">
                                {{ number_format($summary['leaning_support'] ?? 0) }}
                            </div>

                        </div>


                        <div class="vd-card vd-center vd-support-card vd-support-neutral">

                            <div class="vd-label">
                                Neutral
                            </div>

                            <div class="vd-support-number">
                                {{ number_format($summary['neutral'] ?? 0) }}
                            </div>

                        </div>


                        <div class="vd-card vd-center vd-support-card vd-support-undecided">

                            <div class="vd-label">
                                Undecided
                            </div>

                            <div class="vd-support-number">
                                {{ number_format($summary['undecided'] ?? 0) }}
                            </div>

                        </div>


                        <div class="vd-card vd-center vd-support-card vd-support-opposition">

                            <div class="vd-label">
                                Leaning Opposition
                            </div>

                            <div class="vd-support-number">
                                {{ number_format($summary['leaning_opposition'] ?? 0) }}
                            </div>

                        </div>


                        <div class="vd-card vd-center vd-support-card vd-support-opposition">

                            <div class="vd-label">
                                Moderate Opposition
                            </div>

                            <div class="vd-support-number">
                                {{ number_format($summary['moderate_opposition'] ?? 0) }}
                            </div>

                        </div>


                        <div class="vd-card vd-center vd-support-card vd-support-opposition">

                            <div class="vd-label">
                                Strong Opposition
                            </div>

                            <div class="vd-support-number">
                                {{ number_format($summary['strong_opposition'] ?? 0) }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 VOTER STATUS
            ====================================================== --}}

            <div class="vd-section">

                <div class="vd-section-header">

                    <div class="vd-section-title">
                        📈 Voter Status
                    </div>

                    <div class="vd-section-description">
                        Current electoral activity and follow-up status.
                    </div>

                </div>

                <div class="vd-section-body">

                    <div class="vd-grid vd-grid-4">

                        <div class="vd-card vd-center">

                            <div class="vd-icon">
                                🟢
                            </div>

                            <div class="vd-label">
                                Active Voters
                            </div>

                            <div class="vd-value">
                                {{ number_format($summary['active_voters'] ?? 0) }}
                            </div>

                        </div>


                        <div class="vd-card vd-center">

                            <div class="vd-icon">
                                ⚪
                            </div>

                            <div class="vd-label">
                                Inactive Voters
                            </div>

                            <div class="vd-value">
                                {{
                                    number_format(
                                        max(
                                            0,
                                            ($summary['voters'] ?? 0)
                                            - ($summary['active_voters'] ?? 0)
                                        )
                                    )
                                }}
                            </div>

                        </div>


                        <div class="vd-card vd-center">

                            <div class="vd-icon">
                                🤝
                            </div>

                            <div class="vd-label">
                                Neutral
                            </div>

                            <div class="vd-value">
                                {{ number_format($summary['neutral'] ?? 0) }}
                            </div>

                        </div>


                        <div class="vd-card vd-center">

                            <div class="vd-icon">
                                ❓
                            </div>

                            <div class="vd-label">
                                Undecided
                            </div>

                            <div class="vd-value">
                                {{ number_format($summary['undecided'] ?? 0) }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

{{-- =====================================================
     BOOTH INTELLIGENCE
====================================================== --}}

<div class="vd-section">

    <div class="vd-section-header">

        <div class="vd-section-title">
            🗳️ Booth Intelligence
        </div>

        <div class="vd-section-description">
            AI powered booth level analysis.
        </div>

    </div>


    <div class="vd-section-body">


        <div class="vd-grid vd-grid-3">


        @forelse($boothIntelligence as $booth)


            <div class="vd-card">


                <div class="vd-party-header">


                    <div>

                        <div class="vd-party-name">

                            🗳️ {{ $booth['booth'] }}

                        </div>


                        <div class="vd-party-full-name">

                            Booth No:
                            {{ $booth['booth_no'] }}

                        </div>


                    </div>



                    <div class="vd-party-total">

                        {{ $booth['total_voters'] }}

                    </div>


                </div>



                <br>


                <div class="vd-party-list">


                    <div class="vd-party-row">

                        <span>
                            Houses
                        </span>

                        <span class="vd-party-count">
                            {{ $booth['houses'] }}
                        </span>

                    </div>



                    <div class="vd-party-row">

                        <span>
                            Support
                        </span>

                        <span class="vd-party-count">

                            {{ $booth['support_percentage'] }}%

                        </span>

                    </div>



                    <div class="vd-party-row">

                        <span>
                            Neutral
                        </span>

                        <span class="vd-party-count">

                            {{ $booth['neutral_percentage'] }}%

                        </span>

                    </div>



                    <div class="vd-party-row">

                        <span>
                            AI Risk Score
                        </span>

                        <span class="vd-party-count">

                            {{ $booth['risk_score'] }}/100

                        </span>

                    </div>


                    <div class="vd-party-row">

                        <span>
                            Priority
                        </span>

                        <span class="vd-party-count">

                            {{ $booth['priority'] }}

                        </span>

                    </div>


                </div>



                <br>


                <div class="vd-subtitle">

                    🎯 {{ $booth['action'] }}

                </div>



                <br>


                <a
                href="{{ url('/admin/booth-intelligence?booth='.$booth['booth_id']) }}"
                class="vd-select"
                style="
                display:block;
                text-align:center;
                text-decoration:none;
                "
                >

                🔍 Open Booth Intelligence

                </a>


            </div>


        @empty


            <div class="vd-card">

                No Booth Data Found

            </div>


        @endforelse


        </div>


    </div>

</div>
            {{-- =====================================================
                 POLITICAL PARTY OVERVIEW
            ====================================================== --}}

            <div class="vd-section">

                <div class="vd-section-header">

                    <div class="vd-section-title">
                        🗳️ Political Party Overview
                    </div>

                    <div class="vd-section-description">
                        Party-wise voter support distribution for this village.
                    </div>

                </div>

                <div class="vd-section-body">

                    <div class="vd-grid vd-grid-4">

                        @forelse ($summary['parties'] ?? [] as $party)

                            <div class="vd-card vd-party-card">

                                <div class="vd-party-header">

                                    <div class="vd-party-left">

                                        <div class="vd-party-accent"></div>

                                        <div>

                                            <div class="vd-party-symbol">
                                                {{ $party['symbol'] ?: '🏛️' }}
                                            </div>

                                            <div class="vd-party-name">
                                                {{ $party['short_name'] ?: '-' }}
                                            </div>

                                            <div class="vd-party-full-name">
                                                {{ $party['name'] ?: '-' }}
                                            </div>

                                        </div>

                                    </div>

                                    <div class="vd-party-total">
                                        {{ number_format($party['total'] ?? 0) }}
                                    </div>

                                </div>


                                <div class="vd-party-list">

                                    <div class="vd-party-row">
                                        <span>Strong Support</span>

                                        <span class="vd-party-count">
                                            {{ number_format($party['strong_support'] ?? 0) }}
                                        </span>
                                    </div>


                                    <div class="vd-party-row">
                                        <span>Moderate Support</span>

                                        <span class="vd-party-count">
                                            {{ number_format($party['moderate_support'] ?? 0) }}
                                        </span>
                                    </div>


                                    <div class="vd-party-row">
                                        <span>Leaning Support</span>

                                        <span class="vd-party-count">
                                            {{ number_format($party['leaning_support'] ?? 0) }}
                                        </span>
                                    </div>


                                    <div class="vd-party-row">
                                        <span>Neutral</span>

                                        <span class="vd-party-count">
                                            {{ number_format($party['neutral'] ?? 0) }}
                                        </span>
                                    </div>


                                    <div class="vd-party-row">
                                        <span>Undecided</span>

                                        <span class="vd-party-count">
                                            {{ number_format($party['undecided'] ?? 0) }}
                                        </span>
                                    </div>


                                    <div class="vd-party-row">
                                        <span>Leaning Opposition</span>

                                        <span class="vd-party-count">
                                            {{ number_format($party['leaning_opposition'] ?? 0) }}
                                        </span>
                                    </div>


                                    <div class="vd-party-row">
                                        <span>Moderate Opposition</span>

                                        <span class="vd-party-count">
                                            {{ number_format($party['moderate_opposition'] ?? 0) }}
                                        </span>
                                    </div>


                                    <div class="vd-party-row">
                                        <span>Strong Opposition</span>

                                        <span class="vd-party-count">
                                            {{ number_format($party['strong_opposition'] ?? 0) }}
                                        </span>
                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="vd-card">
                                <div class="vd-label">
                                    No active political parties found.
                                </div>
                            </div>

                        @endforelse

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 VILLAGE INFORMATION
            ====================================================== --}}

            <div class="vd-section">

                <div class="vd-section-header">

                    <div class="vd-section-title">
                        🏘️ Village Information
                    </div>

                    <div class="vd-section-description">
                        Administrative and electoral information.
                    </div>

                </div>

                <div class="vd-section-body">

                    <div class="vd-grid vd-grid-4">

                        <div class="vd-card vd-info-card">

                            <div class="vd-label">
                                Constituency
                            </div>

                            <div class="vd-info-value">
                                {{ $village->constituency?->name ?? '-' }}
                            </div>

                        </div>


                        <div class="vd-card vd-info-card">

                            <div class="vd-label">
                                Village
                            </div>

                            <div class="vd-info-value">
                                {{ $village->name ?: '-' }}
                            </div>

                        </div>


                        <div class="vd-card vd-info-card">

                            <div class="vd-label">
                                Taluka
                            </div>

                            <div class="vd-info-value">
                                {{ $village->taluka ?: '-' }}
                            </div>

                        </div>


                        <div class="vd-card vd-info-card">

                            <div class="vd-label">
                                District
                            </div>

                            <div class="vd-info-value">
                                {{ $village->district ?: '-' }}
                            </div>

                        </div>


                        <div class="vd-card vd-info-card">

                            <div class="vd-label">
                                Category
                            </div>

                            <div class="vd-info-value">
                                {{ $village->category ?: '-' }}
                            </div>

                        </div>


                        <div class="vd-card vd-info-card">

                            <div class="vd-label">
                                Total Booths
                            </div>

                            <div class="vd-info-value">
                                {{ number_format($summary['booths'] ?? 0) }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>


        @else

            {{-- =====================================================
                 EMPTY STATE
            ====================================================== --}}

            <div class="vd-section">

                <div class="vd-empty">

                    <div class="vd-empty-icon">
                        🏘️
                    </div>

                    <div class="vd-empty-title">
                        Village Dashboard
                    </div>

                    <div class="vd-empty-description">
                        Select a village above to view complete village intelligence.
                    </div>

                </div>

            </div>

        @endif

    </div>

</x-filament-panels::page>
