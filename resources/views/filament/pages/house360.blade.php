<x-filament-panels::page>

    <style>
        /* =========================================================
           HOUSE 360 — LIGHT / WHITE THEME
        ========================================================== */

        .house360 {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .house360-grid {
            display: grid;
            gap: 16px;
        }

        .house360-grid-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .house360-grid-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        /* =========================================================
           SEARCH
        ========================================================== */

        .house360-search {
            background: linear-gradient(
                135deg,
                #ffffff 0%,
                #f8fafc 100%
            );
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .05);
        }

        .house360-search-title {
            font-size: 19px;
            font-weight: 750;
            color: #111827;
        }

        .house360-search-description {
            margin-top: 5px;
            font-size: 13px;
            color: #6b7280;
        }

        .house360-search-row {
            display: flex;
            gap: 10px;
            align-items: stretch;
            margin-top: 18px;
            max-width: 850px;
        }

        .house360-input {
            flex: 1;
            min-width: 0;
            min-height: 44px;
            padding: 9px 12px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            background: #ffffff;
            color: #111827;
            outline: none;
        }

        .house360-input:focus {
            border-color: #9ca3af;
            box-shadow: 0 0 0 3px rgba(156, 163, 175, .18);
        }

        /* =========================================================
           PROFILE
        ========================================================== */

        .house360-profile {
            background: linear-gradient(
                135deg,
                #ffffff 0%,
                #f8fafc 100%
            );
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .05);
        }

        .house360-profile-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            flex-wrap: wrap;
        }

        .house360-profile-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            background: #f3f4f6;
            font-size: 25px;
            margin-bottom: 13px;
        }

        .house360-profile-title {
            font-size: 25px;
            line-height: 1.2;
            font-weight: 800;
            color: #111827;
        }

        .house360-profile-subtitle {
            margin-top: 5px;
            font-size: 13px;
            color: #6b7280;
        }

        .house360-profile-badges {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .house360-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            color: #374151;
            font-size: 12px;
            font-weight: 650;
        }

        /* =========================================================
           CARDS
        ========================================================== */

        .house360-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 20px;
            min-width: 0;
            box-shadow: 0 1px 3px rgba(15, 23, 42, .05);
            transition:
                transform .18s ease,
                box-shadow .18s ease,
                border-color .18s ease;
        }

        .house360-card:hover {
            transform: translateY(-2px);
            border-color: #d1d5db;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .08);
        }

        .house360-icon {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #f3f4f6;
            font-size: 21px;
            margin-bottom: 13px;
        }

        .house360-label {
            font-size: 13px;
            font-weight: 500;
            color: #6b7280;
        }

        .house360-value {
            margin-top: 6px;
            font-size: 16px;
            font-weight: 700;
            color: #111827;
        }

        .house360-number {
            margin-top: 7px;
            font-size: 30px;
            line-height: 1.1;
            font-weight: 800;
            color: #111827;
        }

        .house360-subtitle {
            margin-top: 6px;
            font-size: 12px;
            color: #9ca3af;
        }

        /* =========================================================
           SECTIONS
        ========================================================== */

        .house360-section {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(15, 23, 42, .04);
        }

        .house360-section-header {
            padding: 17px 20px;
            border-bottom: 1px solid #eef0f2;
        }

        .house360-section-title {
            font-size: 15px;
            font-weight: 750;
            color: #111827;
        }

        .house360-section-description {
            margin-top: 4px;
            font-size: 12px;
            color: #6b7280;
        }

        .house360-section-body {
            padding: 16px;
        }

        /* =========================================================
           POLITICAL INTELLIGENCE
        ========================================================== */

        .house360-political-card {
            position: relative;
            overflow: hidden;
        }

        .house360-political-card::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
            background: #d1d5db;
        }

        .house360-strong-support::after {
            background: #22c55e;
        }

        .house360-moderate-support::after {
            background: #84cc16;
        }

        .house360-leaning-support::after {
            background: #eab308;
        }

        .house360-neutral::after {
            background: #9ca3af;
        }

        .house360-undecided::after {
            background: #f97316;
        }

        .house360-leaning-opposition::after {
            background: #fb923c;
        }

        .house360-moderate-opposition::after {
            background: #ef4444;
        }

        .house360-strong-opposition::after {
            background: #dc2626;
        }

        /* =========================================================
           TABLE
        ========================================================== */

        .house360-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .house360-table {
            width: 100%;
            border-collapse: collapse;
        }

        .house360-table th,
        .house360-table td {
            padding: 13px 11px;
            text-align: left;
            border-bottom: 1px solid #eef0f2;
            font-size: 13px;
            white-space: nowrap;
        }

        .house360-table th {
            color: #6b7280;
            font-weight: 650;
            background: #fafafa;
        }

        .house360-table tbody tr:hover {
            background: #f9fafb;
        }

        .house360-table td {
            color: #374151;
        }

        .house360-table td strong {
            color: #111827;
        }

        .house360-table td a {
            color: #374151;
            text-decoration: none;
        }

        .house360-table td a:hover {
            text-decoration: underline;
        }

        .house360-support-badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 9px;
            border-radius: 999px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            color: #374151;
            font-size: 11px;
            font-weight: 650;
        }

        /* =========================================================
           EMPTY STATE
        ========================================================== */

        .house360-empty {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 1px 4px rgba(15, 23, 42, .04);
        }

        .house360-empty-inner {
            padding: 70px 20px;
            text-align: center;
        }

        .house360-empty-icon {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            border-radius: 18px;
            background: #f3f4f6;
            font-size: 34px;
        }

        .house360-empty-title {
            font-size: 21px;
            font-weight: 800;
            color: #111827;
        }

        .house360-empty-description {
            margin-top: 7px;
            font-size: 13px;
            color: #6b7280;
        }

        /* =========================================================
           QUICK ACTIONS
        ========================================================== */

        .house360-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* =========================================================
           DARK MODE
        ========================================================== */

        html.dark .house360-search,
        html.dark .house360-profile {
            border-color: rgba(168, 85, 247, .20);
            background:
                radial-gradient(circle at 92% 0%, rgba(147, 51, 234, .18), transparent 34%),
                linear-gradient(135deg, #1b1723, #14111a);
            box-shadow: 0 18px 46px -32px rgba(168, 85, 247, .50);
        }

        html.dark .house360-card,
        html.dark .house360-section,
        html.dark .house360-empty {
            border-color: rgba(168, 85, 247, .16);
            background: linear-gradient(145deg, #1a1721, #15121b);
            box-shadow: 0 16px 38px -30px rgba(168, 85, 247, .46);
        }

        html.dark .house360-card:hover {
            border-color: rgba(168, 85, 247, .42);
            background: linear-gradient(145deg, #211a2b, #18131f);
            box-shadow: 0 20px 38px -25px rgba(168, 85, 247, .58);
        }

        html.dark .house360-search-title,
        html.dark .house360-profile-title,
        html.dark .house360-value,
        html.dark .house360-number,
        html.dark .house360-section-title,
        html.dark .house360-empty-title,
        html.dark .house360-table td,
        html.dark .house360-table td strong,
        html.dark .house360-table td a {
            color: #f7f4fb;
        }

        html.dark .house360-search-description,
        html.dark .house360-profile-subtitle,
        html.dark .house360-label,
        html.dark .house360-section-description,
        html.dark .house360-empty-description {
            color: #a8a1b3;
        }

        html.dark .house360-subtitle {
            color: #7f778b;
        }

        html.dark .house360-input {
            border-color: rgba(168, 85, 247, .22);
            background: #100e15;
            color: #f7f4fb;
            color-scheme: dark;
        }

        html.dark .house360-input::placeholder {
            color: #746d7f;
        }

        html.dark .house360-input:focus {
            border-color: rgba(168, 85, 247, .72);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, .14);
        }

        html.dark .house360-profile-icon,
        html.dark .house360-icon,
        html.dark .house360-empty-icon {
            border: 1px solid rgba(168, 85, 247, .18);
            background: linear-gradient(135deg, rgba(124, 58, 237, .20), rgba(30, 22, 39, .94));
        }

        html.dark .house360-badge,
        html.dark .house360-support-badge {
            border-color: rgba(168, 85, 247, .20);
            background: #211a29;
            color: #d8d1e1;
        }

        html.dark .house360-section-header {
            border-bottom-color: rgba(168, 85, 247, .14);
            background: linear-gradient(110deg, #1a1622, #131019 65%);
        }

        html.dark .house360-section-body {
            background: #121018;
        }

        html.dark .house360-table th {
            border-bottom-color: rgba(168, 85, 247, .18);
            background: #17131d;
            color: #aaa2b5;
        }

        html.dark .house360-table td {
            border-bottom-color: rgba(168, 85, 247, .10);
        }

        html.dark .house360-table tbody tr:hover {
            background: rgba(124, 58, 237, .08);
        }

        /* =========================================================
           RESPONSIVE
        ========================================================== */

        @media (max-width: 1024px) {

            .house360-grid-4,
            .house360-grid-3 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

        }

        @media (max-width: 640px) {

            .house360-grid-4,
            .house360-grid-3 {
                grid-template-columns: 1fr;
            }

            .house360-search-row {
                flex-direction: column;
            }

            .house360-table {
                min-width: 1000px;
            }

            .house360-card {
                padding: 17px;
            }

            .house360-number {
                font-size: 27px;
            }

            .house360-profile {
                padding: 19px;
            }

            .house360-profile-title {
                font-size: 22px;
            }

        }
    </style>


    <div class="house360">


        {{-- =========================================================
             SEARCH HOUSE
        ========================================================== --}}

        <div class="house360-search">

            <div class="house360-search-title">
                🔍 Search House
            </div>

            <div class="house360-search-description">
                Search by House Number, Head of Family or Mobile Number
            </div>

            <div class="house360-search-row">

                <input
                    type="text"
                    wire:model="search"
                    wire:keydown.enter="searchHouse"
                    placeholder="House No / Head of Family / Mobile"
                    class="house360-input"
                />

                <x-filament::button
                    wire:click="searchHouse"
                    icon="heroicon-o-magnifying-glass"
                >
                    Search
                </x-filament::button>

            </div>

        </div>


        @if($house)


            {{-- =====================================================
                 HOUSE PROFILE
            ====================================================== --}}

            <div class="house360-profile">

                <div class="house360-profile-top">

                    <div>

                        <div class="house360-profile-icon">
                            🏠
                        </div>

                        <div class="house360-profile-title">
                            House {{ $house->house_no }}
                        </div>

                        <div class="house360-profile-subtitle">
                            Household Intelligence Profile
                        </div>

                    </div>


                    <div class="house360-profile-badges">

                        <span class="house360-badge">
                            👥 {{ $house->voters->count() }} Family Members
                        </span>

                        @if($house->voters->where('is_active', true)->count())

                            <span class="house360-badge">
                                🟢
                                {{ $house->voters->where('is_active', true)->count() }}
                                Active
                            </span>

                        @endif

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 HOUSEHOLD OVERVIEW
            ====================================================== --}}

            <div class="house360-grid house360-grid-4">

                <div class="house360-card">

                    <div class="house360-icon">
                        🏠
                    </div>

                    <div class="house360-label">
                        House Number
                    </div>

                    <div class="house360-number">
                        {{ $house->house_no ?: '-' }}
                    </div>

                    <div class="house360-subtitle">
                        Household
                    </div>

                </div>


                <div class="house360-card">

                    <div class="house360-icon">
                        👤
                    </div>

                    <div class="house360-label">
                        Head of Family
                    </div>

                    <div class="house360-value">
                        {{ $house->head_of_family ?: '-' }}
                    </div>

                    <div class="house360-subtitle">
                        Primary household contact
                    </div>

                </div>


                <div class="house360-card">

                    <div class="house360-icon">
                        👥
                    </div>

                    <div class="house360-label">
                        Family Members
                    </div>

                    <div class="house360-number">
                        {{ $house->voters->count() }}
                    </div>

                    <div class="house360-subtitle">
                        Registered voters
                    </div>

                </div>


                <div class="house360-card">

                    <div class="house360-icon">
                        📞
                    </div>

                    <div class="house360-label">
                        Mobile
                    </div>

                    <div class="house360-value">
                        {{ $house->mobile ?: '-' }}
                    </div>

                    <div class="house360-subtitle">
                        Household contact
                    </div>

                </div>

            </div>


            {{-- =====================================================
                 ELECTORAL LOCATION
            ====================================================== --}}

            <div class="house360-section">

                <div class="house360-section-header">

                    <div class="house360-section-title">
                        📍 Electoral Location
                    </div>

                    <div class="house360-section-description">
                        Constituency → Village → Booth → House
                    </div>

                </div>

                <div class="house360-section-body">

                    <div class="house360-grid house360-grid-4">

                        <div class="house360-card">

                            <div class="house360-label">
                                Constituency
                            </div>

                            <div class="house360-value">
                                {{ $house->booth?->village?->constituency?->name ?? '-' }}
                            </div>

                        </div>


                        <div class="house360-card">

                            <div class="house360-label">
                                Village
                            </div>

                            <div class="house360-value">
                                {{ $house->booth?->village?->name ?? '-' }}
                            </div>

                        </div>


                        <div class="house360-card">

                            <div class="house360-label">
                                Booth
                            </div>

                            <div class="house360-value">
                                {{ $house->booth?->booth_no ?? $house->booth?->booth_name ?? '-' }}
                            </div>

                        </div>


                        <div class="house360-card">

                            <div class="house360-label">
                                House
                            </div>

                            <div class="house360-value">
                                {{ $house->house_no ?: '-' }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 POLITICAL INTELLIGENCE
            ====================================================== --}}

            <div class="house360-section">

                <div class="house360-section-header">

                    <div class="house360-section-title">
                        📊 Political Intelligence
                    </div>

                    <div class="house360-section-description">
                        Household-level voter sentiment and political support
                    </div>

                </div>

                <div class="house360-section-body">

                    <div class="house360-grid house360-grid-4">


                        {{-- Strong Support --}}

                        <div class="house360-card house360-political-card house360-strong-support">

                            <div class="house360-icon">
                                🟢
                            </div>

                            <div class="house360-label">
                                Strong Support
                            </div>

                            <div class="house360-number">
                                {{ number_format($politicalSummary['strong_support'] ?? 0) }}
                            </div>

                        </div>


                        {{-- Moderate Support --}}

                        <div class="house360-card house360-political-card house360-moderate-support">

                            <div class="house360-icon">
                                🟢
                            </div>

                            <div class="house360-label">
                                Moderate Support
                            </div>

                            <div class="house360-number">
                                {{ number_format($politicalSummary['moderate_support'] ?? 0) }}
                            </div>

                        </div>


                        {{-- Leaning Support --}}

                        <div class="house360-card house360-political-card house360-leaning-support">

                            <div class="house360-icon">
                                📈
                            </div>

                            <div class="house360-label">
                                Leaning Support
                            </div>

                            <div class="house360-number">
                                {{ number_format($politicalSummary['leaning_support'] ?? 0) }}
                            </div>

                        </div>


                        {{-- Neutral --}}

                        <div class="house360-card house360-political-card house360-neutral">

                            <div class="house360-icon">
                                ⚪
                            </div>

                            <div class="house360-label">
                                Neutral
                            </div>

                            <div class="house360-number">
                                {{ number_format($politicalSummary['neutral'] ?? 0) }}
                            </div>

                        </div>


                        {{-- Undecided --}}

                        <div class="house360-card house360-political-card house360-undecided">

                            <div class="house360-icon">
                                ❓
                            </div>

                            <div class="house360-label">
                                Undecided
                            </div>

                            <div class="house360-number">
                                {{ number_format($politicalSummary['undecided'] ?? 0) }}
                            </div>

                        </div>


                        {{-- Leaning Opposition --}}

                        <div class="house360-card house360-political-card house360-leaning-opposition">

                            <div class="house360-icon">
                                📉
                            </div>

                            <div class="house360-label">
                                Leaning Opposition
                            </div>

                            <div class="house360-number">
                                {{ number_format($politicalSummary['leaning_opposition'] ?? 0) }}
                            </div>

                        </div>


                        {{-- Moderate Opposition --}}

                        <div class="house360-card house360-political-card house360-moderate-opposition">

                            <div class="house360-icon">
                                🟠
                            </div>

                            <div class="house360-label">
                                Moderate Opposition
                            </div>

                            <div class="house360-number">
                                {{ number_format($politicalSummary['moderate_opposition'] ?? 0) }}
                            </div>

                        </div>


                        {{-- Strong Opposition --}}

                        <div class="house360-card house360-political-card house360-strong-opposition">

                            <div class="house360-icon">
                                🔴
                            </div>

                            <div class="house360-label">
                                Strong Opposition
                            </div>

                            <div class="house360-number">
                                {{ number_format($politicalSummary['strong_opposition'] ?? 0) }}
                            </div>

                        </div>


                        {{-- Total Voters --}}

                        <div class="house360-card">

                            <div class="house360-icon">
                                👥
                            </div>

                            <div class="house360-label">
                                Total Voters
                            </div>

                            <div class="house360-number">
                                {{ number_format($politicalSummary['total_voters'] ?? $house->voters->count()) }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 HOUSEHOLD VOTERS
            ====================================================== --}}

            <div class="house360-section">

                <div class="house360-section-header">

                    <div class="house360-section-title">
                        👨‍👩‍👧‍👦 Household Voters
                    </div>

                    <div class="house360-section-description">
                        All voters registered under this household
                    </div>

                </div>


                @if($house->voters->count())

                    <div class="house360-table-wrapper">

                        <table class="house360-table">

                            <thead>

                                <tr>

                                    <th>
                                        Voter
                                    </th>

                                    <th>
                                        Mobile
                                    </th>

                                    <th>
                                        Age
                                    </th>

                                    <th>
                                        Gender
                                    </th>

                                    <th>
                                        Party
                                    </th>

                                    <th>
                                        Support
                                    </th>

                                    <th>
                                        Volunteer
                                    </th>

                                    <th>
                                        Influencer
                                    </th>

                                    <th>
                                        Priority
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach($house->voters as $voter)

                                    <tr>

                                        {{-- Voter --}}

                                        <td>
                                            <strong>
                                                {{ $voter->name }}
                                            </strong>
                                        </td>


                                        {{-- Mobile --}}

                                        <td>

                                            @if($voter->mobile)

                                                <a
                                                    href="tel:{{ $voter->mobile }}"
                                                >
                                                    📞 {{ $voter->mobile }}
                                                </a>

                                            @else

                                                <span style="color:#9ca3af;">
                                                    -
                                                </span>

                                            @endif

                                        </td>


                                        {{-- Age --}}

                                        <td>
                                            {{ $voter->age ?: '-' }}
                                        </td>


                                        {{-- Gender --}}

                                        <td>
                                            {{ $voter->gender ?: '-' }}
                                        </td>


                                        {{-- Party --}}

                                        <td>
                                            {{ $voter->politicalParty?->short_name ?? '-' }}
                                        </td>


                                        {{-- Support --}}

                                        <td>

                                            <span class="house360-support-badge">
                                                {{ $voter->support_level ?: '-' }}
                                            </span>

                                        </td>


                                        {{-- Volunteer --}}

                                        <td>
                                            {{ $voter->is_volunteer ? '✅' : '—' }}
                                        </td>


                                        {{-- Influencer --}}

                                        <td>
                                            {{ $voter->is_influencer ? '⭐' : '—' }}
                                        </td>


                                        {{-- Priority --}}

                                        <td>
                                            {{ $voter->priority ?: '-' }}
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div style="
                        padding:35px 20px;
                        text-align:center;
                        color:#6b7280;
                    ">
                        No family members found.
                    </div>

                @endif

            </div>

{{-- =====================================================
     AI FAMILY INTELLIGENCE
===================================================== --}}

@if($this->houseIntelligence)


<div class="house360-section">


    <div class="house360-section-header">

        <div class="house360-section-title">
            🤖 AI Family Intelligence
        </div>

        <div class="house360-section-description">
            AI based household analysis and campaign recommendation
        </div>

    </div>


    <div class="house360-section-body">


        <div class="house360-grid house360-grid-4">


            {{-- Family Score --}}

            <div class="house360-card">

                <div class="house360-icon">
                    🧠
                </div>

                <div class="house360-label">
                    Family Score
                </div>

                <div class="house360-number">

                    {{
                        $this->houseIntelligence['family_score']
                        ?? 0
                    }}/100

                </div>

            </div>



            {{-- Family Type --}}

            <div class="house360-card">

                <div class="house360-icon">
                    👨‍👩‍👧
                </div>

                <div class="house360-label">
                    Family Type
                </div>

                <div class="house360-value">

                    {{
                        $this->houseIntelligence['family_type']
                        ?? '-'
                    }}

                </div>

            </div>



            {{-- Conversion --}}

            <div class="house360-card">

                <div class="house360-icon">
                    🎯
                </div>

                <div class="house360-label">
                    Conversion Probability
                </div>

                <div class="house360-number">

                    {{
                        $this->houseIntelligence['conversion_probability']
                        ?? 0
                    }}%

                </div>

            </div>



            {{-- Priority --}}

            <div class="house360-card">

                <div class="house360-icon">
                    🚦
                </div>

                <div class="house360-label">
                    AI Priority
                </div>

                <div class="house360-value">

                    {{
                        $this->houseIntelligence['priority']
                        ?? '-'
                    }}

                </div>

            </div>


        </div>



        <br>



        {{-- AI Recommendation --}}

        <div class="house360-card">


            <div class="house360-label">
                🤖 AI Recommendation
            </div>


            <div class="house360-value"
                 style="margin-top:12px;">

                {{
                    $this->houseIntelligence['recommendation']
                    ?? 'No recommendation available'
                }}

            </div>


        </div>


    </div>


</div>


@endif
            {{-- =====================================================
                 CAMPAIGN INTELLIGENCE
            ====================================================== --}}

            <div class="house360-section">

                <div class="house360-section-header">

                    <div class="house360-section-title">
                        🎯 Campaign Intelligence
                    </div>

                    <div class="house360-section-description">
                        Household campaign engagement indicators
                    </div>

                </div>

                <div class="house360-section-body">

                    <div class="house360-grid house360-grid-4">


                        {{-- Volunteers --}}

                        <div class="house360-card">

                            <div class="house360-icon">
                                🙋
                            </div>

                            <div class="house360-label">
                                Volunteers
                            </div>

                            <div class="house360-number">
                                {{ $house->voters->where('is_volunteer', true)->count() }}
                            </div>

                            <div class="house360-subtitle">
                                Family volunteers
                            </div>

                        </div>


                        {{-- Influencers --}}

                        <div class="house360-card">

                            <div class="house360-icon">
                                ⭐
                            </div>

                            <div class="house360-label">
                                Influencers
                            </div>

                            <div class="house360-number">
                                {{ $house->voters->where('is_influencer', true)->count() }}
                            </div>

                            <div class="house360-subtitle">
                                Family influencers
                            </div>

                        </div>


                        {{-- Active Voters --}}

                        <div class="house360-card">

                            <div class="house360-icon">
                                🟢
                            </div>

                            <div class="house360-label">
                                Active Voters
                            </div>

                            <div class="house360-number">
                                {{ $house->voters->where('is_active', true)->count() }}
                            </div>

                            <div class="house360-subtitle">
                                Active electoral records
                            </div>

                        </div>


                        {{-- Follow-up --}}

                        <div class="house360-card">

                            <div class="house360-icon">
                                ❓
                            </div>

                            <div class="house360-label">
                                Follow-up Needed
                            </div>

                            <div class="house360-number">
                                {{ $house->voters->where('support_level', 'Undecided')->count() }}
                            </div>

                            <div class="house360-subtitle">
                                Undecided voters
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 HOUSEHOLD CONTACT
            ====================================================== --}}

            <div class="house360-section">

                <div class="house360-section-header">

                    <div class="house360-section-title">
                        📞 Household Contact
                    </div>

                    <div class="house360-section-description">
                        Primary household contact information
                    </div>

                </div>

                <div class="house360-section-body">

                    <div class="house360-grid house360-grid-3">

                        <div class="house360-card">

                            <div class="house360-label">
                                Head of Family
                            </div>

                            <div class="house360-value">
                                {{ $house->head_of_family ?: '-' }}
                            </div>

                        </div>


                        <div class="house360-card">

                            <div class="house360-label">
                                Mobile
                            </div>

                            <div class="house360-value">

                                @if($house->mobile)

                                    <a
                                        href="tel:{{ $house->mobile }}"
                                        style="text-decoration:none;"
                                    >
                                        📞 {{ $house->mobile }}
                                    </a>

                                @else

                                    -

                                @endif

                            </div>

                        </div>


                        <div class="house360-card">

                            <div class="house360-label">
                                Address
                            </div>

                            <div class="house360-value">
                                {{ $house->address ?: '-' }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 QUICK ACTIONS
            ====================================================== --}}

            <div class="house360-section">

                <div class="house360-section-header">

                    <div class="house360-section-title">
                        ⚡ Quick Actions
                    </div>

                    <div class="house360-section-description">
                        Actions available for this household
                    </div>

                </div>

                <div class="house360-section-body">

                    <div class="house360-actions">

                        <x-filament::button
                            color="primary"
                            wire:click="startSurvey"
                            icon="heroicon-o-clipboard-document-check"
                        >
                            Start Survey
                        </x-filament::button>


                        @if($house->mobile)

                            <a
                                href="tel:{{ $house->mobile }}"
                                style="text-decoration:none;"
                            >

                                <x-filament::button
                                    color="success"
                                    icon="heroicon-o-phone"
                                >
                                    Call Head
                                </x-filament::button>

                            </a>

                        @endif


                        @if($house->mobile)

                            <a
                                href="https://wa.me/{{ preg_replace('/\D+/', '', $house->mobile) }}"
                                target="_blank"
                                rel="noopener"
                                style="text-decoration:none;"
                            >

                                <x-filament::button
                                    color="warning"
                                    icon="heroicon-o-chat-bubble-left-right"
                                >
                                    WhatsApp
                                </x-filament::button>

                            </a>

                        @endif

                    </div>

                </div>

            </div>


        @else


            {{-- =====================================================
                 EMPTY STATE
            ====================================================== --}}

            <div class="house360-empty">

                <div class="house360-empty-inner">

                    <div class="house360-empty-icon">
                        🏠
                    </div>

                    <div class="house360-empty-title">
                        House 360
                    </div>

                    <div class="house360-empty-description">
                        Search for a household to view its complete
                        household intelligence profile.
                    </div>

                </div>

            </div>

        @endif

    </div>

</x-filament-panels::page>
