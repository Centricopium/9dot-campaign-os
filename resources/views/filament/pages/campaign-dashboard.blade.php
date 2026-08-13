<x-filament-panels::page>

    @php
        /*
        |--------------------------------------------------------------------------
        | Core Database Metrics
        |--------------------------------------------------------------------------
        */

        $constituencies = \App\Models\Constituency::count();
        $villages = \App\Models\Village::count();
        $booths = \App\Models\Booth::count();
        $houses = \App\Models\House::count();
        $voters = \App\Models\Voter::count();

        /*
        |--------------------------------------------------------------------------
        | Campaign Intelligence
        |--------------------------------------------------------------------------
        */

        $volunteers = \App\Models\Voter::where('is_volunteer', true)->count();

        $influencers = \App\Models\Voter::where('is_influencer', true)->count();

        $neutral = \App\Models\Voter::where(
            'support_level',
            'Neutral'
        )->count();

        $undecided = \App\Models\Voter::where(
            'support_level',
            'Undecided'
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Database / Survey Health
        |--------------------------------------------------------------------------
        */

        $activeVoters = \App\Models\Voter::where(
            'is_active',
            true
        )->count();

        $surveys = \App\Models\Survey::count();

        $responses = \App\Models\SurveyResponse::count();

        $activeVoterPercentage = $voters > 0
            ? round(($activeVoters / $voters) * 100)
            : 0;

        $responsePercentage = $voters > 0
            ? round(($responses / $voters) * 100)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Active Political Parties
        |--------------------------------------------------------------------------
        */

        $parties = \App\Models\PoliticalParty::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Quick Access URLs
        |--------------------------------------------------------------------------
        */

        $constituencyUrl = url('/admin/constituencies');
        $villageUrl = url('/admin/villages');
        $boothUrl = url('/admin/booths');
        $houseUrl = url('/admin/houses');
        $voterUrl = url('/admin/voters');

        $constituencyDashboardUrl = url('/admin/constituency-dashboard');
        $villageDashboardUrl = url('/admin/village-dashboard');
        $boothDashboardUrl = url('/admin/booth-dashboard');

    @endphp


    <style>
        .command-center {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .command-header {
            border: 1px solid rgba(75, 85, 99, .45);
            border-radius: 16px;
            padding: 24px;
            background:
                linear-gradient(
                    135deg,
                    rgba(31, 41, 55, .72),
                    rgba(17, 24, 39, .58)
                );
        }

        .command-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .command-title {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.2;
        }

        .command-subtitle {
            margin-top: 6px;
            font-size: 14px;
            color: rgb(156 163 175);
        }

        .command-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 18px;
        }

        .command-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(107, 114, 128, .45);
            background: rgba(39, 39, 42, .72);
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
            color: rgb(229 231 235);
        }

        .command-section {
            border: 1px solid rgba(75, 85, 99, .42);
            border-radius: 16px;
            overflow: hidden;
            background: rgba(24, 24, 27, .58);
        }

        .command-section-header {
            padding: 17px 20px;
            border-bottom: 1px solid rgba(75, 85, 99, .35);
        }

        .command-section-title {
            font-size: 15px;
            font-weight: 700;
        }

        .command-section-description {
            margin-top: 3px;
            font-size: 12px;
            color: rgb(156 163 175);
        }

        .command-grid {
            display: grid;
            gap: 12px;
            padding: 14px;
        }

        .command-grid-5 {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .command-grid-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .command-grid-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .command-card {
            border: 1px solid rgba(75, 85, 99, .38);
            border-radius: 12px;
            padding: 18px;
            background: rgba(24, 24, 27, .68);
            transition: transform .15s ease, border-color .15s ease;
        }

        .command-card:hover {
            transform: translateY(-2px);
            border-color: rgba(156, 163, 175, .55);
        }

        .command-card-icon {
            font-size: 24px;
            line-height: 1;
        }

        .command-card-label {
            margin-top: 12px;
            font-size: 13px;
            color: rgb(156 163 175);
        }

        .command-card-number {
            margin-top: 5px;
            font-size: 28px;
            font-weight: 800;
            line-height: 1.15;
        }

        .command-card-description {
            margin-top: 6px;
            font-size: 11px;
            color: rgb(156 163 175);
        }

        .command-intelligence {
            position: relative;
            overflow: hidden;
        }

        .command-intelligence::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
        }

        .intelligence-volunteer::after {
            background: rgb(34 197 94);
        }

        .intelligence-influencer::after {
            background: rgb(234 179 8);
        }

        .intelligence-neutral::after {
            background: rgb(249 115 22);
        }

        .intelligence-undecided::after {
            background: rgb(239 68 68);
        }

        .health-card {
            min-height: 150px;
        }

        .health-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .health-progress {
            height: 7px;
            margin-top: 18px;
            overflow: hidden;
            border-radius: 999px;
            background: rgb(55 65 81);
        }

        .health-progress-fill {
            height: 100%;
            border-radius: inherit;
            background: rgb(34 197 94);
        }

        .health-progress-neutral {
            background: rgb(75 85 99);
        }

        .quick-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            min-height: 90px;
            text-decoration: none;
            color: inherit;
        }

        .quick-card:hover {
            text-decoration: none;
        }

        .quick-left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .quick-icon {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 22px;
            background: rgba(55, 65, 81, .45);
        }

        .quick-title {
            font-size: 14px;
            font-weight: 700;
        }

        .quick-description {
            margin-top: 4px;
            font-size: 11px;
            color: rgb(156 163 175);
        }

        .quick-arrow {
            font-size: 20px;
            color: rgb(156 163 175);
        }

        .system-dot {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            background: rgb(34 197 94);
            box-shadow: 0 0 10px rgba(34, 197, 94, .65);
        }

        .system-status {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .system-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .party-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(75, 85, 99, .3);
        }

        .party-row:last-child {
            border-bottom: 0;
        }

        @media (max-width: 1200px) {
            .command-grid-5 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 900px) {
            .command-grid-4,
            .command-grid-3 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .command-header-top {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 640px) {
            .command-grid-5,
            .command-grid-4,
            .command-grid-3 {
                grid-template-columns: 1fr;
            }

            .command-title {
                font-size: 22px;
            }
        }
    </style>


    <div class="command-center">


        {{-- ============================================================= --}}
        {{-- HEADER --}}
        {{-- ============================================================= --}}

        <div class="command-header">

            <div class="command-header-top">

                <div>

                    <div class="command-title">
                        🗳️ 9Dot Campaign Command Center
                    </div>

                    <div class="command-subtitle">
                        Election Campaign Operating System — Super Admin Overview
                    </div>

                </div>

                <div class="text-sm text-gray-500">
                    Campaign Intelligence
                </div>

            </div>

            <div class="command-pills">

                <span class="command-pill">
                    🏛️ {{ number_format($constituencies) }} Constituencies
                </span>

                <span class="command-pill">
                    🏘️ {{ number_format($villages) }} Villages
                </span>

                <span class="command-pill">
                    🗳️ {{ number_format($booths) }} Booths
                </span>

                <span class="command-pill">
                    👥 {{ number_format($voters) }} Voters
                </span>

            </div>

        </div>


        {{-- ============================================================= --}}
        {{-- CAMPAIGN COVERAGE --}}
        {{-- ============================================================= --}}

        <div class="command-section">

            <div class="command-section-header">

                <div class="command-section-title">
                    📊 Campaign Coverage
                </div>

                <div class="command-section-description">
                    Complete electoral database coverage
                </div>

            </div>

            <div class="command-grid command-grid-5">

                @foreach ([
                    ['🏛️', 'Constituencies', $constituencies, 'Assembly constituencies'],
                    ['🏘️', 'Villages', $villages, 'Electoral villages'],
                    ['🗳️', 'Booths', $booths, 'Polling booths'],
                    ['🏠', 'Houses', $houses, 'Household database'],
                    ['👥', 'Voters', $voters, 'Electoral roll'],
                ] as $item)

                    <div class="command-card">

                        <div class="command-card-icon">
                            {{ $item[0] }}
                        </div>

                        <div class="command-card-label">
                            {{ $item[1] }}
                        </div>

                        <div class="command-card-number">
                            {{ number_format($item[2]) }}
                        </div>

                        <div class="command-card-description">
                            {{ $item[3] }}
                        </div>

                    </div>

                @endforeach

            </div>

        </div>


        {{-- ============================================================= --}}
        {{-- CAMPAIGN INTELLIGENCE --}}
        {{-- ============================================================= --}}

        <div class="command-section">

            <div class="command-section-header">

                <div class="command-section-title">
                    🎯 Campaign Intelligence
                </div>

                <div class="command-section-description">
                    People and voter intelligence currently available
                </div>

            </div>

            <div class="command-grid command-grid-4">

                <div class="command-card command-intelligence intelligence-volunteer">

                    <div class="command-card-icon">
                        🙋
                    </div>

                    <div class="command-card-label">
                        Volunteers
                    </div>

                    <div class="command-card-number">
                        {{ number_format($volunteers) }}
                    </div>

                    <div class="command-card-description">
                        Identified campaign volunteers
                    </div>

                </div>


                <div class="command-card command-intelligence intelligence-influencer">

                    <div class="command-card-icon">
                        ⭐
                    </div>

                    <div class="command-card-label">
                        Influencers
                    </div>

                    <div class="command-card-number">
                        {{ number_format($influencers) }}
                    </div>

                    <div class="command-card-description">
                        Identified local influencers
                    </div>

                </div>


                <div class="command-card command-intelligence intelligence-neutral">

                    <div class="command-card-icon">
                        🤝
                    </div>

                    <div class="command-card-label">
                        Neutral
                    </div>

                    <div class="command-card-number">
                        {{ number_format($neutral) }}
                    </div>

                    <div class="command-card-description">
                        Neutral voters
                    </div>

                </div>


                <div class="command-card command-intelligence intelligence-undecided">

                    <div class="command-card-icon">
                        ❓
                    </div>

                    <div class="command-card-label">
                        Undecided
                    </div>

                    <div class="command-card-number">
                        {{ number_format($undecided) }}
                    </div>

                    <div class="command-card-description">
                        Voters requiring follow-up
                    </div>

                </div>

            </div>

        </div>


        {{-- ============================================================= --}}
        {{-- DATABASE & SURVEY HEALTH --}}
        {{-- ============================================================= --}}

        <div class="command-section">

            <div class="command-section-header">

                <div class="command-section-title">
                    📈 Database & Survey Health
                </div>

                <div class="command-section-description">
                    Current operational health of campaign data
                </div>

            </div>

            <div class="command-grid command-grid-3">


                <div class="command-card health-card">

                    <div class="health-row">

                        <div>

                            <div class="command-card-label" style="margin-top:0;">
                                👥 Active Voters
                            </div>

                            <div class="command-card-number">
                                {{ number_format($activeVoters) }}
                            </div>

                        </div>

                        <div class="text-sm text-gray-400">
                            {{ $activeVoterPercentage }}%
                        </div>

                    </div>

                    <div class="command-card-description">
                        {{ $activeVoterPercentage }}% of total voters
                    </div>

                    <div class="health-progress">

                        <div
                            class="health-progress-fill"
                            style="width: {{ min(100, $activeVoterPercentage) }}%;"
                        ></div>

                    </div>

                </div>


                <div class="command-card health-card">

                    <div class="command-card-label" style="margin-top:0;">
                        📋 Surveys
                    </div>

                    <div class="command-card-number">
                        {{ number_format($surveys) }}
                    </div>

                    <div class="command-card-description">
                        Active survey definitions
                    </div>

                    <div class="health-progress">

                        <div
                            class="health-progress-neutral"
                            style="width: {{ $surveys > 0 ? '100' : '0' }}%;"
                        ></div>

                    </div>

                </div>


                <div class="command-card health-card">

                    <div class="command-card-label" style="margin-top:0;">
                        📝 Survey Responses
                    </div>

                    <div class="command-card-number">
                        {{ number_format($responses) }}
                    </div>

                    <div class="command-card-description">
                        {{ $responsePercentage }} responses per 100 voters
                    </div>

                    <div class="health-progress">

                        <div
                            class="health-progress-neutral"
                            style="width: {{ min(100, $responsePercentage) }}%;"
                        ></div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ============================================================= --}}
        {{-- QUICK ACCESS --}}
        {{-- ============================================================= --}}

        <div class="command-section">

            <div class="command-section-header">

                <div class="command-section-title">
                    ⚡ Quick Access
                </div>

                <div class="command-section-description">
                    Jump directly to campaign management modules
                </div>

            </div>

            <div class="command-grid command-grid-4">


                @foreach ([
                    [$constituencyUrl, '🏛️', 'Constituencies', 'Manage assembly constituencies'],
                    [$villageUrl, '🏘️', 'Villages', 'Manage village master'],
                    [$boothUrl, '🗳️', 'Booths', 'Manage polling booths'],
                    [$houseUrl, '🏠', 'Houses', 'Household intelligence'],
                    [$voterUrl, '👥', 'Voters', 'Voter 360° database'],
                    [$constituencyDashboardUrl, '📊', 'Constituency Dashboard', 'Constituency-level intelligence'],
                    [$villageDashboardUrl, '📍', 'Village Dashboard', 'Village-level intelligence'],
                    [$boothDashboardUrl, '🎯', 'Booth Dashboard', 'Booth-level intelligence'],
                ] as $item)

                    <a
                        href="{{ $item[0] }}"
                        class="command-card quick-card"
                    >

                        <div class="quick-left">

                            <div class="quick-icon">
                                {{ $item[1] }}
                            </div>

                            <div>

                                <div class="quick-title">
                                    {{ $item[2] }}
                                </div>

                                <div class="quick-description">
                                    {{ $item[3] }}
                                </div>

                            </div>

                        </div>

                        <div class="quick-arrow">
                            →
                        </div>

                    </a>

                @endforeach

            </div>

        </div>


        {{-- ============================================================= --}}
        {{-- PARTY SNAPSHOT --}}
        {{-- ============================================================= --}}

        <div class="command-section">

            <div class="command-section-header">

                <div class="command-section-title">
                    🗳️ Party Database Snapshot
                </div>

                <div class="command-section-description">
                    Voter records currently mapped to political parties
                </div>

            </div>

            <div class="command-grid command-grid-4">

                @forelse ($parties as $party)

                    @php
                        $partyCount = \App\Models\Voter::where(
                            'political_party_id',
                            $party->id
                        )->count();
                    @endphp

                    <div class="command-card">

                        <div class="command-card-icon">
                            {{ $party->symbol ?: '🏛️' }}
                        </div>

                        <div class="party-row">

                            <div>

                                <div class="font-semibold">
                                    {{ $party->short_name }}
                                </div>

                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $party->name }}
                                </div>

                            </div>

                            <div class="text-2xl font-bold">
                                {{ number_format($partyCount) }}
                            </div>

                        </div>

                    </div>

                @empty

                    <div class="command-card">
                        No active political parties configured.
                    </div>

                @endforelse

            </div>

        </div>


        {{-- ============================================================= --}}
        {{-- SYSTEM STATUS --}}
        {{-- ============================================================= --}}

        <div class="command-section">

            <div class="command-section-header">

                <div class="command-section-title">
                    🟢 System Status
                </div>

                <div class="command-section-description">
                    Real-time status of core campaign modules
                </div>

            </div>

            <div class="command-grid command-grid-4">


                @foreach ([
                    ['👥', 'Voter Database', 'Operational'],
                    ['🏠', 'House 360', 'Operational'],
                    ['🎯', 'Booth Dashboard', 'Operational'],
                    ['📋', 'Survey Engine', 'Operational'],
                ] as $system)

                    <div class="command-card">

                        <div class="system-status">

                            <div class="system-left">

                                <div class="command-card-icon">
                                    {{ $system[0] }}
                                </div>

                                <div>

                                    <div class="font-semibold">
                                        {{ $system[1] }}
                                    </div>

                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $system[2] }}
                                    </div>

                                </div>

                            </div>

                            <div class="system-dot"></div>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>


    </div>

</x-filament-panels::page>