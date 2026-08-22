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

        /* ============================================================
           MAIN DASHBOARD
        ============================================================ */

        .command-center {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }


        /* ============================================================
           HERO / COMMAND HEADER
        ============================================================ */

        .command-header {
            position: relative;
            overflow: hidden;

            border: 1px solid #e5e7eb;
            border-radius: 18px;

            padding: 28px;

            background:
                linear-gradient(
                    135deg,
                    #ffffff 0%,
                    #f8faff 55%,
                    #f4f1ff 100%
                );

            box-shadow:
                0 8px 24px rgba(15, 23, 42, 0.06);
        }

        .command-header::before {
            content: '';

            position: absolute;

            top: -80px;
            right: -80px;

            width: 220px;
            height: 220px;

            border-radius: 999px;

            background:
                radial-gradient(
                    circle,
                    rgba(99, 102, 241, 0.12),
                    transparent 70%
                );
        }

        .command-header-top {
            position: relative;

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 20px;
        }

        .command-title {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.2;

            color: #111827;
            letter-spacing: -0.02em;
        }

        .command-subtitle {
            margin-top: 7px;

            font-size: 14px;

            color: #6b7280;
        }

        .command-header-status {
            display: flex;
            align-items: center;
            gap: 8px;

            padding: 8px 12px;

            border: 1px solid #e5e7eb;
            border-radius: 999px;

            background: #ffffff;

            color: #4b5563;

            font-size: 12px;
            font-weight: 600;

            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }

        .command-header-dot {
            width: 8px;
            height: 8px;

            border-radius: 999px;

            background: #22c55e;

            box-shadow:
                0 0 0 4px rgba(34, 197, 94, 0.10);
        }


        /* ============================================================
           HEADER PILLS
        ============================================================ */

        .command-pills {
            position: relative;

            display: flex;
            flex-wrap: wrap;

            gap: 9px;

            margin-top: 20px;
        }

        .command-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;

            border: 1px solid #e5e7eb;

            background: rgba(255, 255, 255, 0.9);

            border-radius: 999px;

            padding: 8px 13px;

            font-size: 12px;
            font-weight: 600;

            color: #374151;

            box-shadow:
                0 2px 7px rgba(15, 23, 42, 0.04);
        }


        /* ============================================================
           SECTIONS
        ============================================================ */

        .command-section {
            overflow: hidden;

            border: 1px solid #e5e7eb;

            border-radius: 16px;

            background: #ffffff;

            box-shadow:
                0 5px 18px rgba(15, 23, 42, 0.045);
        }

        .command-section-header {
            padding: 18px 20px;

            border-bottom: 1px solid #eef0f3;

            background:
                linear-gradient(
                    to right,
                    #ffffff,
                    #fafbfc
                );
        }

        .command-section-title {
            font-size: 15px;
            font-weight: 750;

            color: #111827;
        }

        .command-section-description {
            margin-top: 4px;

            font-size: 12px;

            color: #6b7280;
        }


        /* ============================================================
           GRIDS
        ============================================================ */

        .command-grid {
            display: grid;

            gap: 14px;

            padding: 15px;
        }

        .command-grid-5 {
            grid-template-columns:
                repeat(5, minmax(0, 1fr));
        }

        .command-grid-4 {
            grid-template-columns:
                repeat(4, minmax(0, 1fr));
        }

        .command-grid-3 {
            grid-template-columns:
                repeat(3, minmax(0, 1fr));
        }


        /* ============================================================
           STANDARD CARDS
        ============================================================ */

        .command-card {
            position: relative;

            min-width: 0;

            border: 1px solid #e5e7eb;

            border-radius: 13px;

            padding: 18px;

            background: #ffffff;

            box-shadow:
                0 2px 7px rgba(15, 23, 42, 0.025);

            transition:
                transform .18s ease,
                box-shadow .18s ease,
                border-color .18s ease;
        }

        .command-card:hover {
            transform: translateY(-2px);

            border-color: #d1d5db;

            box-shadow:
                0 8px 20px rgba(15, 23, 42, 0.07);
        }

        .command-card-icon {
            font-size: 24px;
            line-height: 1;
        }

        .command-card-label {
            margin-top: 12px;

            font-size: 13px;
            font-weight: 500;

            color: #6b7280;
        }

        .command-card-number {
            margin-top: 5px;

            font-size: 28px;
            font-weight: 800;

            line-height: 1.15;

            color: #111827;
        }

        .command-card-description {
            margin-top: 6px;

            font-size: 11px;

            color: #9ca3af;
        }


        /* ============================================================
           CAMPAIGN INTELLIGENCE
        ============================================================ */

        .command-intelligence {
            overflow: hidden;

            padding-bottom: 21px;
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
            background: #22c55e;
        }

        .intelligence-influencer::after {
            background: #f59e0b;
        }

        .intelligence-neutral::after {
            background: #f97316;
        }

        .intelligence-undecided::after {
            background: #ef4444;
        }


        /* ============================================================
           HEALTH CARDS
        ============================================================ */

        .health-card {
            min-height: 150px;
        }

        .health-row {
            display: flex;

            align-items: center;
            justify-content: space-between;

            gap: 12px;
        }

        .health-percentage {
            font-size: 13px;
            font-weight: 700;

            color: #6366f1;
        }

        .health-progress {
            height: 7px;

            margin-top: 18px;

            overflow: hidden;

            border-radius: 999px;

            background: #eef2f7;
        }

        .health-progress-fill {
            height: 100%;

            border-radius: inherit;

            background:
                linear-gradient(
                    90deg,
                    #22c55e,
                    #16a34a
                );
        }

        .health-progress-neutral {
            height: 100%;

            border-radius: inherit;

            background:
                linear-gradient(
                    90deg,
                    #6366f1,
                    #8b5cf6
                );
        }


        /* ============================================================
           QUICK ACCESS
        ============================================================ */

        .quick-card {
            display: flex;

            align-items: center;
            justify-content: space-between;

            gap: 14px;

            min-height: 92px;

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
            width: 44px;
            height: 44px;

            flex: 0 0 44px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 11px;

            font-size: 22px;

            background:
                linear-gradient(
                    135deg,
                    #f3f4ff,
                    #eef2ff
                );

            border: 1px solid #e0e7ff;
        }

        .quick-title {
            font-size: 14px;
            font-weight: 700;

            color: #111827;
        }

        .quick-description {
            margin-top: 4px;

            font-size: 11px;

            color: #6b7280;
        }

        .quick-arrow {
            font-size: 20px;

            color: #9ca3af;

            transition:
                transform .18s ease,
                color .18s ease;
        }

        .quick-card:hover .quick-arrow {
            transform: translateX(3px);

            color: #6366f1;
        }


        /* ============================================================
           SYSTEM STATUS
        ============================================================ */

        .system-dot {
            width: 10px;
            height: 10px;

            flex: 0 0 10px;

            border-radius: 999px;

            background: #22c55e;

            box-shadow:
                0 0 0 4px rgba(34, 197, 94, 0.10);
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


        /* ============================================================
           PARTY SNAPSHOT
        ============================================================ */

        .party-row {
            display: flex;

            align-items: center;
            justify-content: space-between;

            gap: 10px;

            padding: 2px 0 10px;

            border-bottom: 1px solid #eef0f3;
        }

        .party-row:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .party-name {
            color: #111827;
        }

        .party-full-name {
            margin-top: 4px;

            font-size: 11px;

            color: #9ca3af;
        }

        .party-count {
            font-size: 24px;
            font-weight: 800;

            color: #111827;
        }


        /* ============================================================
           DARK MODE
        ============================================================ */

        html.dark .command-header {
            border-color: rgba(168, 85, 247, 0.20);
            background:
                radial-gradient(circle at 92% 0%, rgba(147, 51, 234, 0.20), transparent 34%),
                linear-gradient(135deg, #191621 0%, #14111b 58%, #21132e 100%);
            box-shadow: 0 20px 50px -30px rgba(168, 85, 247, 0.48);
        }

        html.dark .command-header::before {
            background: radial-gradient(circle, rgba(168, 85, 247, 0.20), transparent 70%);
        }

        html.dark .command-title,
        html.dark .command-section-title,
        html.dark .command-card-number,
        html.dark .quick-title,
        html.dark .party-name,
        html.dark .party-count {
            color: #f7f4fb;
        }

        html.dark .command-subtitle,
        html.dark .command-section-description,
        html.dark .command-card-label,
        html.dark .quick-description {
            color: #a8a1b3;
        }

        html.dark .command-card-description,
        html.dark .party-full-name,
        html.dark .quick-arrow {
            color: #7f778b;
        }

        html.dark .command-header-status,
        html.dark .command-pill {
            border-color: rgba(168, 85, 247, 0.20);
            background: rgba(24, 20, 32, 0.84);
            color: #d8d1e1;
            box-shadow: 0 8px 24px -18px rgba(168, 85, 247, 0.50);
        }

        html.dark .command-section {
            border-color: rgba(168, 85, 247, 0.16);
            background: #121018;
            box-shadow: 0 18px 48px -34px rgba(168, 85, 247, 0.38);
        }

        html.dark .command-section-header {
            border-bottom-color: rgba(168, 85, 247, 0.14);
            background: linear-gradient(110deg, #18141f, #121018 65%);
        }

        html.dark .command-card {
            border-color: rgba(168, 85, 247, 0.14);
            background: linear-gradient(145deg, #1a1721, #15121b);
            box-shadow: 0 10px 26px -22px rgba(168, 85, 247, 0.42);
        }

        html.dark .command-card:hover {
            border-color: rgba(168, 85, 247, 0.40);
            background: linear-gradient(145deg, #201a2a, #18131f);
            box-shadow: 0 18px 34px -22px rgba(168, 85, 247, 0.55);
        }

        html.dark .quick-icon {
            border-color: rgba(168, 85, 247, 0.20);
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.22), rgba(30, 22, 39, 0.92));
        }

        html.dark .health-progress {
            background: #292330;
        }

        html.dark .health-percentage {
            color: #c084fc;
        }

        html.dark .party-row {
            border-bottom-color: rgba(168, 85, 247, 0.12);
        }


        /* ============================================================
           RESPONSIVE
        ============================================================ */

        @media (max-width: 1200px) {

            .command-grid-5 {
                grid-template-columns:
                    repeat(3, minmax(0, 1fr));
            }

        }


        @media (max-width: 900px) {

            .command-grid-4,
            .command-grid-3 {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
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

            .command-header {
                padding: 20px;
            }

            .command-section-header {
                padding: 16px;
            }

            .command-grid {
                padding: 12px;
            }

        }

        .command-message-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 17px 20px;
            border-bottom: 1px solid #eef0f3;
            background: linear-gradient(110deg, #17111f, #4c1d95 65%, #7c3aed);
            color: #ffffff;
        }

        .command-message-title {
            font-size: 15px;
            font-weight: 850;
        }

        .command-message-summary {
            margin-top: 4px;
            color: #ddd6fe;
            font-size: 10px;
        }

        .command-message-link {
            padding: 7px 11px;
            border: 1px solid rgba(255, 255, 255, .24);
            border-radius: 9px;
            color: #ffffff;
            background: rgba(255, 255, 255, .10);
            font-size: 10px;
            font-weight: 800;
            text-decoration: none;
        }

        .command-message-list {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 0;
        }

        .command-message-item {
            min-width: 0;
            padding: 14px 15px;
            border-right: 1px solid #eef0f3;
            color: inherit;
            text-decoration: none;
            transition: background .18s ease;
        }

        .command-message-item:last-child {
            border-right: 0;
        }

        .command-message-item:hover {
            background: #faf8ff;
        }

        .command-message-from {
            color: #7c3aed;
            font-size: 9px;
            font-weight: 850;
        }

        .command-message-subject {
            margin-top: 5px;
            overflow: hidden;
            color: #111827;
            font-size: 11px;
            font-weight: 850;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .command-message-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 7px;
            margin-top: 7px;
            color: #9ca3af;
            font-size: 8px;
        }

        .command-message-priority {
            padding: 3px 6px;
            border-radius: 999px;
            color: #92400e;
            background: #fef3c7;
            font-size: 7px;
            font-weight: 850;
        }

        .command-message-priority.urgent {
            color: #991b1b;
            background: #fee2e2;
        }

        .command-message-empty {
            grid-column: 1 / -1;
            padding: 22px;
            color: #6b7280;
            text-align: center;
            font-size: 11px;
        }

        html.dark .command-message-item {
            border-color: rgba(168, 85, 247, .12);
        }

        html.dark .command-message-item:hover {
            background: #171023;
        }

        html.dark .command-message-subject {
            color: #f8fafc;
        }

        @media (max-width: 1200px) {
            .command-message-list {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .command-message-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .command-message-list {
                grid-template-columns: 1fr;
            }

            .command-message-item {
                border-right: 0;
                border-bottom: 1px solid #eef0f3;
            }
        }

    </style>


    <div class="command-center">


        {{-- ============================================================
             HEADER
        ============================================================= --}}

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

                <div class="command-header-status">

                    <span class="command-header-dot"></span>

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

        @can('internal_message.view')
            <div class="command-section">
                <div class="command-message-header">
                    <div>
                        <div class="command-message-title">New Internal Messages</div>
                        <div class="command-message-summary">{{ number_format($this->unreadMessageCount) }} unread @if($this->urgentUnreadCount) · {{ number_format($this->urgentUnreadCount) }} important / urgent @endif</div>
                    </div>
                    <a class="command-message-link" href="{{ $this->messageCentreUrl() }}">Open Inbox</a>
                </div>
                <div class="command-message-list">
                    @forelse($this->unreadMessages as $message)
                        <a class="command-message-item" href="{{ $this->messageConversationUrl($message) }}">
                            <div class="command-message-from">{{ $message->sender?->name ?? 'Campaign Team' }}</div>
                            <div class="command-message-subject">{{ $message->conversation?->subject ?? 'Internal Message' }}</div>
                            <div class="command-message-meta"><span>{{ $message->created_at->diffForHumans(short: true) }}</span>@if($message->priority !== 'Normal')<span class="command-message-priority {{ strtolower($message->priority) }}">{{ $message->priority }}</span>@endif</div>
                        </a>
                    @empty
                        <div class="command-message-empty">You are all caught up. No unread internal messages.</div>
                    @endforelse
                </div>
            </div>
        @endcan


        {{-- ============================================================
             CAMPAIGN COVERAGE
        ============================================================= --}}

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


        {{-- ============================================================
             CAMPAIGN INTELLIGENCE
        ============================================================= --}}

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


        {{-- ============================================================
             DATABASE & SURVEY HEALTH
        ============================================================= --}}

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


                {{-- Active Voters --}}

                <div class="command-card health-card">

                    <div class="health-row">

                        <div>

                            <div
                                class="command-card-label"
                                style="margin-top:0;"
                            >
                                👥 Active Voters
                            </div>

                            <div class="command-card-number">
                                {{ number_format($activeVoters) }}
                            </div>

                        </div>

                        <div class="health-percentage">
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


                {{-- Surveys --}}

                <div class="command-card health-card">

                    <div
                        class="command-card-label"
                        style="margin-top:0;"
                    >
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
                            style="
                                width: {{ $surveys > 0 ? '100' : '0' }}%;
                            "
                        ></div>

                    </div>

                </div>


                {{-- Survey Responses --}}

                <div class="command-card health-card">

                    <div
                        class="command-card-label"
                        style="margin-top:0;"
                    >
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
                            style="
                                width: {{ min(100, $responsePercentage) }}%;
                            "
                        ></div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ============================================================
             QUICK ACCESS
        ============================================================= --}}

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


        {{-- ============================================================
             PARTY SNAPSHOT
        ============================================================= --}}

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

                                <div class="party-name font-semibold">
                                    {{ $party->short_name }}
                                </div>

                                <div class="party-full-name">
                                    {{ $party->name }}
                                </div>

                            </div>


                            <div class="party-count">
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


        {{-- ============================================================
             SYSTEM STATUS
        ============================================================= --}}

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

                                    <div
                                        class="font-semibold"
                                        style="color:#111827;"
                                    >
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
