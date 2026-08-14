<x-filament-panels::page>

    @php
        $summary = $this->summary;
        $booth = $this->selectedBooth;

        $supportLevels = [
            'strong_support' => 'Strong Support',
            'moderate_support' => 'Moderate Support',
            'leaning_support' => 'Leaning Support',
            'neutral' => 'Neutral',
            'undecided' => 'Undecided',
            'leaning_opposition' => 'Leaning Opposition',
            'moderate_opposition' => 'Moderate Opposition',
            'strong_opposition' => 'Strong Opposition',
        ];

        $overallSupport = [];

        foreach (array_keys($supportLevels) as $key) {
            $overallSupport[$key] = 0;
        }

        if ($booth) {
            $boothVoters = \App\Models\Voter::query()
                ->whereHas('house', function ($query) use ($booth) {
                    $query->where('booth_id', $booth->id);
                })
                ->get(['support_level']);

            foreach ($boothVoters as $voter) {
                $supportKey = match ($voter->support_level) {
                    'Strong Support' => 'strong_support',
                    'Moderate Support' => 'moderate_support',
                    'Leaning Support' => 'leaning_support',
                    'Neutral' => 'neutral',
                    'Undecided' => 'undecided',
                    'Leaning Opposition' => 'leaning_opposition',
                    'Moderate Opposition' => 'moderate_opposition',
                    'Strong Opposition' => 'strong_opposition',
                    default => null,
                };

                if ($supportKey !== null) {
                    $overallSupport[$supportKey]++;
                }
            }
        }
    @endphp


    <style>
        /* ============================================================
           BOOTH DASHBOARD — LIGHT THEME
        ============================================================ */

        .booth-dashboard {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .booth-header {
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 24px;
            background: linear-gradient(
                135deg,
                #ffffff 0%,
                #f8fafc 100%
            );
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.06);
        }

        .booth-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .booth-header-title {
            font-size: 27px;
            line-height: 1.2;
            font-weight: 800;
            color: #111827;
        }

        .booth-header-subtitle {
            margin-top: 7px;
            font-size: 14px;
            color: #6b7280;
        }

        .booth-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 13px;
            border-radius: 999px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            color: #4b5563;
            font-size: 12px;
            font-weight: 600;
        }

        .booth-section {
            overflow: hidden;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 3px 14px rgba(15, 23, 42, 0.045);
        }

        .booth-section-header {
            padding: 17px 20px;
            border-bottom: 1px solid #e5e7eb;
            background: #ffffff;
        }

        .booth-section-title {
            font-size: 15px;
            font-weight: 750;
            color: #111827;
        }

        .booth-section-description {
            margin-top: 4px;
            font-size: 12px;
            color: #6b7280;
        }

        .booth-section-body {
            padding: 14px;
        }

        .booth-grid {
            display: grid;
            gap: 12px;
        }

        .booth-grid-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .booth-grid-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .booth-grid-5 {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .booth-card {
            min-width: 0;
            border: 1px solid #e5e7eb;
            border-radius: 13px;
            padding: 18px;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.035);
            transition:
                transform .15s ease,
                box-shadow .15s ease,
                border-color .15s ease;
        }

        .booth-card:hover {
            transform: translateY(-2px);
            border-color: #c4b5fd;
            box-shadow: 0 7px 18px rgba(15, 23, 42, 0.08);
        }

        .booth-card-icon {
            font-size: 24px;
            line-height: 1;
        }

        .booth-card-label {
            margin-top: 11px;
            font-size: 13px;
            font-weight: 500;
            color: #6b7280;
        }

        .booth-card-number {
            margin-top: 5px;
            font-size: 28px;
            line-height: 1.15;
            font-weight: 800;
            color: #111827;
        }

        .booth-card-description {
            margin-top: 6px;
            font-size: 11px;
            color: #9ca3af;
        }

        /* ============================================================
           SELECT
        ============================================================ */

        .booth-select-wrapper {
            max-width: 700px;
        }

        .booth-select-label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 650;
            color: #374151;
        }

        .booth-select {
            width: 100%;
            min-height: 44px;
            padding: 9px 13px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            background: #ffffff;
            color: #111827;
            outline: none;
            transition:
                border-color .15s ease,
                box-shadow .15s ease;
        }

        .booth-select:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, .12);
        }

        /* ============================================================
           DEMOGRAPHICS
        ============================================================ */

        .booth-value-card {
            text-align: center;
            background: linear-gradient(
                180deg,
                #ffffff,
                #fafafa
            );
        }

        .booth-value {
            margin-top: 7px;
            font-size: 28px;
            font-weight: 800;
            color: #111827;
        }

        /* ============================================================
           SUPPORT
        ============================================================ */

        .support-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .support-card {
            position: relative;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            background: #ffffff;
        }

        .support-card::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
        }

        .support-card:nth-child(1)::after {
            background: #22c55e;
        }

        .support-card:nth-child(2)::after {
            background: #84cc16;
        }

        .support-card:nth-child(3)::after {
            background: #a3e635;
        }

        .support-card:nth-child(4)::after {
            background: #9ca3af;
        }

        .support-card:nth-child(5)::after {
            background: #f59e0b;
        }

        .support-card:nth-child(6)::after {
            background: #f97316;
        }

        .support-card:nth-child(7)::after {
            background: #ef4444;
        }

        .support-card:nth-child(8)::after {
            background: #dc2626;
        }

        .support-label {
            font-size: 12px;
            color: #6b7280;
        }

        .support-number {
            margin-top: 5px;
            font-size: 25px;
            font-weight: 800;
            color: #111827;
        }

        /* ============================================================
           PARTY CARDS
        ============================================================ */

        .party-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 15px;
        }

        .party-left {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            min-width: 0;
        }

        .party-accent {
            width: 4px;
            min-height: 48px;
            flex: 0 0 4px;
            border-radius: 999px;
        }

        .party-symbol {
            font-size: 24px;
            line-height: 1;
        }

        .party-name {
            margin-top: 5px;
            font-size: 16px;
            font-weight: 750;
            color: #111827;
        }

        .party-full-name {
            margin-top: 3px;
            font-size: 11px;
            color: #6b7280;
        }

        .party-total {
            font-size: 27px;
            line-height: 1;
            font-weight: 800;
            color: #111827;
        }

        .party-list {
            margin-top: 17px;
        }

        .party-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 9px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 12px;
            color: #4b5563;
        }

        .party-row:last-child {
            border-bottom: 0;
        }

        .party-count {
            min-width: 30px;
            text-align: right;
            font-weight: 750;
            color: #111827;
        }

        /* ============================================================
           INFORMATION CARDS
        ============================================================ */

        .info-label {
            font-size: 12px;
            color: #6b7280;
        }

        .info-value {
            margin-top: 5px;
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }

        /* ============================================================
           EMPTY STATE
        ============================================================ */

        .empty-state {
            padding: 65px 20px;
            text-align: center;
        }

        .empty-icon {
            width: 68px;
            height: 68px;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: #f5f3ff;
            font-size: 34px;
        }

        .empty-title {
            font-size: 21px;
            font-weight: 800;
            color: #111827;
        }

        .empty-description {
            max-width: 520px;
            margin: 8px auto 0;
            font-size: 13px;
            color: #6b7280;
        }

        /* ============================================================
           RESPONSIVE
        ============================================================ */

        @media (max-width: 1200px) {
            .booth-grid-5 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 1050px) {
            .booth-grid-4,
            .booth-grid-3,
            .support-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 700px) {
            .booth-header-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .booth-grid-5,
            .booth-grid-4,
            .booth-grid-3,
            .support-grid {
                grid-template-columns: 1fr;
            }

            .booth-header-title {
                font-size: 23px;
            }

            .booth-card-number,
            .booth-value {
                font-size: 25px;
            }
        }
    </style>


    <div class="booth-dashboard">


        {{-- ============================================================
             HEADER
        ============================================================= --}}

        <div class="booth-header">

            <div class="booth-header-top">

                <div>

                    <div class="booth-header-title">
                        🏛️ Booth Command Center
                    </div>

                    <div class="booth-header-subtitle">
                        Complete polling booth intelligence and voter analysis
                    </div>

                </div>

                <div class="booth-header-badge">
                    📊 Booth Intelligence
                </div>

            </div>

        </div>


        {{-- ============================================================
             BOOTH SELECTION
        ============================================================= --}}

        <div class="booth-section">

            <div class="booth-section-header">

                <div class="booth-section-title">
                    🏛️ Select Booth
                </div>

                <div class="booth-section-description">
                    Select a booth to view complete booth intelligence.
                </div>

            </div>

            <div class="booth-section-body">

                <div class="booth-select-wrapper">

                    <label
                        for="booth-select"
                        class="booth-select-label"
                    >
                        Booth
                    </label>

                    <select
                        id="booth-select"
                        wire:model.live="boothId"
                        class="booth-select"
                    >

                        <option value="">
                            -- Select Booth --
                        </option>

                        @foreach ($this->booths as $item)

                            <option value="{{ $item->id }}">

                                {{ $item->booth_name ?: 'Booth ' . $item->booth_no }}

                                @if ($item->village)
                                    — {{ $item->village->name }}
                                @endif

                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

        </div>


        @if ($booth)


            {{-- ========================================================
                 BOOTH OVERVIEW
            ========================================================= --}}

            <div class="booth-grid booth-grid-4">

                <div class="booth-card">

                    <div class="booth-card-icon">
                        🏠
                    </div>

                    <div class="booth-card-label">
                        Houses
                    </div>

                    <div class="booth-card-number">
                        {{ number_format($summary['houses'] ?? 0) }}
                    </div>

                    <div class="booth-card-description">
                        Total Houses
                    </div>

                </div>


                <div class="booth-card">

                    <div class="booth-card-icon">
                        👥
                    </div>

                    <div class="booth-card-label">
                        Voters
                    </div>

                    <div class="booth-card-number">
                        {{ number_format($summary['voters'] ?? 0) }}
                    </div>

                    <div class="booth-card-description">
                        Total Voters
                    </div>

                </div>


                <div class="booth-card">

                    <div class="booth-card-icon">
                        🙋
                    </div>

                    <div class="booth-card-label">
                        Volunteers
                    </div>

                    <div class="booth-card-number">
                        {{ number_format($summary['volunteers'] ?? 0) }}
                    </div>

                    <div class="booth-card-description">
                        Booth Volunteers
                    </div>

                </div>


                <div class="booth-card">

                    <div class="booth-card-icon">
                        ⭐
                    </div>

                    <div class="booth-card-label">
                        Influencers
                    </div>

                    <div class="booth-card-number">
                        {{ number_format($summary['influencers'] ?? 0) }}
                    </div>

                    <div class="booth-card-description">
                        Booth Influencers
                    </div>

                </div>

            </div>


            {{-- ========================================================
                 VOTER DEMOGRAPHICS
            ========================================================= --}}

            <div class="booth-section">

                <div class="booth-section-header">

                    <div class="booth-section-title">
                        👥 Voter Demographics
                    </div>

                    <div class="booth-section-description">
                        Demographic distribution of voters in this booth.
                    </div>

                </div>

                <div class="booth-section-body">

                    <div class="booth-grid booth-grid-4">

                        <div class="booth-card booth-value-card">

                            <div class="booth-card-label">
                                Total Voters
                            </div>

                            <div class="booth-value">
                                {{ number_format($summary['voters'] ?? 0) }}
                            </div>

                        </div>


                        <div class="booth-card booth-value-card">

                            <div class="booth-card-label">
                                Male
                            </div>

                            <div class="booth-value">
                                {{ number_format($summary['male'] ?? 0) }}
                            </div>

                        </div>


                        <div class="booth-card booth-value-card">

                            <div class="booth-card-label">
                                Female
                            </div>

                            <div class="booth-value">
                                {{ number_format($summary['female'] ?? 0) }}
                            </div>

                        </div>


                        <div class="booth-card booth-value-card">

                            <div class="booth-card-label">
                                Other
                            </div>

                            <div class="booth-value">
                                {{ number_format($summary['other'] ?? 0) }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ========================================================
                 POLITICAL SUPPORT
            ========================================================= --}}

            <div class="booth-section">

                <div class="booth-section-header">

                    <div class="booth-section-title">
                        📊 Political Support
                    </div>

                    <div class="booth-section-description">
                        Complete voter support-level distribution for this booth.
                    </div>

                </div>

                <div class="booth-section-body">

                    <div class="support-grid">

                        @foreach ($supportLevels as $key => $label)

                            <div class="support-card">

                                <div class="support-label">
                                    {{ $label }}
                                </div>

                                <div class="support-number">
                                    {{ number_format($overallSupport[$key] ?? 0) }}
                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            </div>


            {{-- ========================================================
                 BOOTH INTELLIGENCE
            ========================================================= --}}

            <div class="booth-section">

                <div class="booth-section-header">

                    <div class="booth-section-title">
                        🎯 Booth Intelligence
                    </div>

                    <div class="booth-section-description">
                        Active voter and follow-up indicators.
                    </div>

                </div>

                <div class="booth-section-body">

                    <div class="booth-grid booth-grid-4">

                        <div class="booth-card booth-value-card">

                            <div class="booth-card-label">
                                Active Voters
                            </div>

                            <div class="booth-value">
                                {{ number_format($summary['active_voters'] ?? 0) }}
                            </div>

                        </div>


                        <div class="booth-card booth-value-card">

                            <div class="booth-card-label">
                                Inactive Voters
                            </div>

                            <div class="booth-value">

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


                        <div class="booth-card booth-value-card">

                            <div class="booth-card-label">
                                Neutral
                            </div>

                            <div class="booth-value">
                                {{ number_format($overallSupport['neutral'] ?? 0) }}
                            </div>

                        </div>


                        <div class="booth-card booth-value-card">

                            <div class="booth-card-label">
                                Undecided
                            </div>

                            <div class="booth-value">
                                {{ number_format($overallSupport['undecided'] ?? 0) }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ========================================================
                 PARTY OVERVIEW
            ========================================================= --}}

            <div class="booth-section">

                <div class="booth-section-header">

                    <div class="booth-section-title">
                        🗳️ Political Party Overview
                    </div>

                    <div class="booth-section-description">
                        Party-wise voter support and opposition distribution.
                    </div>

                </div>

                <div class="booth-section-body">

                    <div class="booth-grid booth-grid-4">

                        @foreach ($summary['parties'] ?? [] as $party)

                            <div class="booth-card">

                                <div class="party-header">

                                    <div class="party-left">

                                        <div
                                            class="party-accent"
                                            style="
                                                background: {{ $party['color'] ?: '#8b5cf6' }};
                                            "
                                        ></div>

                                        <div>

                                            <div class="party-symbol">
                                                {{ $party['symbol'] ?: '🏛️' }}
                                            </div>

                                            <div class="party-name">
                                                {{ $party['short_name'] ?: '-' }}
                                            </div>

                                            <div class="party-full-name">
                                                {{ $party['name'] ?: '-' }}
                                            </div>

                                        </div>

                                    </div>

                                    <div class="party-total">
                                        {{ number_format($party['total'] ?? 0) }}
                                    </div>

                                </div>


                                <div class="party-list">

                                    @foreach ($supportLevels as $key => $label)

                                        <div class="party-row">

                                            <span>
                                                {{ $label }}
                                            </span>

                                            <span class="party-count">
                                                {{ number_format($party[$key] ?? 0) }}
                                            </span>

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        @endforeach

                    </div>


                    @if (count($summary['parties'] ?? []) === 0)

                        <div class="empty-state">

                            <div class="empty-icon">
                                🗳️
                            </div>

                            <div class="empty-title">
                                No Party Data
                            </div>

                            <div class="empty-description">
                                No political party voter mapping is currently available for this booth.
                            </div>

                        </div>

                    @endif

                </div>

            </div>


            {{-- ========================================================
                 BOOTH INFORMATION
            ========================================================= --}}

            <div class="booth-section">

                <div class="booth-section-header">

                    <div class="booth-section-title">
                        🏛️ Booth Information
                    </div>

                    <div class="booth-section-description">
                        Electoral location and booth master information.
                    </div>

                </div>

                <div class="booth-section-body">

                    <div class="booth-grid booth-grid-4">

                        <div class="booth-card">

                            <div class="info-label">
                                Village
                            </div>

                            <div class="info-value">
                                {{ $booth->village?->name ?? '-' }}
                            </div>

                        </div>


                        <div class="booth-card">

                            <div class="info-label">
                                Booth No.
                            </div>

                            <div class="info-value">
                                {{ $booth->booth_no ?: '-' }}
                            </div>

                        </div>


                        <div class="booth-card">

                            <div class="info-label">
                                Part No.
                            </div>

                            <div class="info-value">
                                {{ $booth->part_no ?: '-' }}
                            </div>

                        </div>


                        <div class="booth-card">

                            <div class="info-label">
                                Category
                            </div>

                            <div class="info-value">
                                {{ $booth->category ?: '-' }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>


        @else


            {{-- ========================================================
                 EMPTY STATE
            ========================================================= --}}

            <div class="booth-section">

                <div class="empty-state">

                    <div class="empty-icon">
                        🏛️
                    </div>

                    <div class="empty-title">
                        Booth Dashboard
                    </div>

                    <div class="empty-description">
                        Select a booth above to view complete booth intelligence,
                        voter demographics, political support and party analysis.
                    </div>

                </div>

            </div>

        @endif

    </div>

</x-filament-panels::page>