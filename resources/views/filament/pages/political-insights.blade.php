<x-filament-panels::page>

@php

$data = $this->data;

$insights = $data['insights'] ?? [];

$swingVoters = $data['swingVoters'] ?? collect();

$weakBooths = $data['weakBooths'] ?? collect();

$priorityVillages = $data['priorityVillages'] ?? collect();

$strategy = $data['strategy'] ?? [];

$boothRecommendations = $data['boothRecommendations'] ?? collect();

@endphp


<style>

/* =========================================================
   GRID
========================================================= */

.pi-grid{
    display:grid;
    gap:18px;
}

.pi-grid-4{
    grid-template-columns:repeat(4,minmax(0,1fr));
}


/* =========================================================
   CARD
========================================================= */

.pi-card{

    padding:22px;

    border-radius:18px;

    background:rgba(255,255,255,.75);

    border:1px solid rgba(0,0,0,.08);

    box-shadow:
        0 4px 18px rgba(0,0,0,.035);

}


.dark .pi-card{

    background:rgba(24,24,27,.75);

    border-color:rgba(255,255,255,.1);

}


/* =========================================================
   ICON
========================================================= */

.pi-icon{

    font-size:30px;

}


/* =========================================================
   LABEL
========================================================= */

.pi-label{

    margin-top:10px;

    font-size:13px;

    color:#6b7280;

}


.dark .pi-label{

    color:#9ca3af;

}


/* =========================================================
   NUMBER
========================================================= */

.pi-number{

    font-size:32px;

    font-weight:800;

    margin-top:5px;

}


/* =========================================================
   SECTION
========================================================= */

.pi-section{

    margin-top:22px;

    border-radius:18px;

    background:rgba(255,255,255,.75);

    border:1px solid rgba(0,0,0,.08);

    overflow:hidden;

    box-shadow:
        0 4px 18px rgba(0,0,0,.035);

}


.dark .pi-section{

    background:rgba(24,24,27,.75);

    border-color:rgba(255,255,255,.1);

}


/* =========================================================
   HEADER
========================================================= */

.pi-header{

    padding:18px 22px;

    font-size:18px;

    font-weight:700;

    border-bottom:
        1px solid rgba(0,0,0,.08);

}


.dark .pi-header{

    border-color:
        rgba(255,255,255,.08);

}


/* =========================================================
   BODY
========================================================= */

.pi-body{

    padding:20px;

}


/* =========================================================
   ROW
========================================================= */

.pi-row{

    display:flex;

    justify-content:space-between;

    align-items:center;

    gap:20px;

    padding:14px;

    margin-bottom:10px;

    border-radius:12px;

    background:
        rgba(128,128,128,.08);

}


.pi-row:last-child{

    margin-bottom:0;

}


/* =========================================================
   BADGE
========================================================= */

.pi-badge{

    display:inline-flex;

    align-items:center;

    justify-content:center;

    padding:6px 12px;

    border-radius:999px;

    background:
        rgba(128,128,128,.18);

    font-size:12px;

    font-weight:600;

}


/* =========================================================
   PRIORITY BADGES
========================================================= */

.pi-badge-high{

    background:rgba(239,68,68,.12);

    color:#dc2626;

}


.pi-badge-medium{

    background:rgba(245,158,11,.14);

    color:#d97706;

}


.pi-badge-low{

    background:rgba(34,197,94,.12);

    color:#16a34a;

}


/* =========================================================
   BOOTH BUTTON
========================================================= */

.pi-button{

    display:inline-flex;

    align-items:center;

    justify-content:center;

    padding:8px 14px;

    border-radius:999px;

    background:rgba(59,130,246,.12);

    color:#2563eb;

    font-size:12px;

    font-weight:700;

    text-decoration:none;

    border:1px solid rgba(59,130,246,.18);

    transition:
        .2s ease;

}


.pi-button:hover{

    background:rgba(59,130,246,.20);

    transform:translateY(-1px);

}


/* =========================================================
   BOOTH INFO
========================================================= */

.pi-booth-info{

    line-height:1.8;

}


.pi-booth-name{

    font-size:15px;

    font-weight:800;

}


/* =========================================================
   AI SCORE
========================================================= */

.pi-ai-score{

    margin-top:8px;

    font-size:14px;

    font-weight:700;

}


/* =========================================================
   ACTION
========================================================= */

.pi-action{

    margin-top:6px;

    color:#6b7280;

    font-size:12px;

}


.dark .pi-action{

    color:#9ca3af;

}


/* =========================================================
   DATA PENDING
========================================================= */

.pi-pending{

    display:inline-flex;

    align-items:center;

    gap:6px;

    padding:6px 10px;

    border-radius:999px;

    background:rgba(245,158,11,.12);

    color:#d97706;

    font-size:12px;

    font-weight:700;

}


/* =========================================================
   EMPTY
========================================================= */

.pi-empty{

    text-align:center;

    padding:25px;

    color:#9ca3af;

}


/* =========================================================
   DARK THEME — AI PURPLE / CHARCOAL
========================================================= */

html.dark .pi-card,
html.dark .pi-section {
    border-color: rgba(139, 92, 246, .26);
    background: linear-gradient(145deg, #0b0910 0%, #171020 58%, #211032 100%);
    box-shadow: 0 15px 36px rgba(0, 0, 0, .30), 0 0 28px rgba(124, 58, 237, .06);
}

html.dark .pi-card {
    transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
}

html.dark .pi-card:hover {
    transform: translateY(-2px);
    border-color: rgba(167, 139, 250, .62);
    box-shadow: 0 13px 30px rgba(76, 29, 149, .23);
}

html.dark .pi-number,
html.dark .pi-header,
html.dark .pi-booth-name,
html.dark .pi-ai-score,
html.dark .pi-row strong {
    color: #f8fafc;
}

html.dark .pi-label,
html.dark .pi-action,
html.dark .pi-empty,
html.dark .pi-row small {
    color: #a8a3b7;
}

html.dark .pi-header {
    border-color: rgba(139, 92, 246, .20);
    background: linear-gradient(90deg, #0e0b14, #1c102b);
}

html.dark .pi-body {
    background: rgba(7, 6, 11, .40);
}

html.dark .pi-row {
    color: #d8d4e3;
    border: 1px solid rgba(139, 92, 246, .18);
    background: linear-gradient(135deg, rgba(28, 23, 36, .92), rgba(38, 20, 57, .72));
    transition: border-color .18s ease, background .18s ease;
}

html.dark .pi-row:hover {
    border-color: rgba(167, 139, 250, .48);
    background: linear-gradient(135deg, rgba(34, 26, 47, .96), rgba(55, 25, 82, .78));
}

html.dark .pi-badge {
    color: #ddd6fe;
    border: 1px solid rgba(167, 139, 250, .24);
    background: rgba(124, 58, 237, .16);
}

html.dark .pi-badge-high {
    color: #fca5a5;
    border-color: rgba(239, 68, 68, .28);
    background: rgba(153, 27, 27, .22);
}

html.dark .pi-badge-medium,
html.dark .pi-pending {
    color: #fcd34d;
    border-color: rgba(245, 158, 11, .28);
    background: rgba(146, 64, 14, .22);
}

html.dark .pi-badge-low {
    color: #86efac;
    border-color: rgba(34, 197, 94, .28);
    background: rgba(22, 101, 52, .22);
}

html.dark .pi-button {
    color: #ede9fe;
    border-color: rgba(167, 139, 250, .38);
    background: linear-gradient(135deg, #6d28d9, #3b1764);
}

html.dark .pi-button:hover {
    color: #ffffff;
    background: linear-gradient(135deg, #7c3aed, #4c1d95);
    box-shadow: 0 8px 18px rgba(76, 29, 149, .32);
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media(max-width:1000px){

    .pi-grid-4{

        grid-template-columns:
            repeat(2,1fr);

    }

}


@media(max-width:600px){

    .pi-grid-4{

        grid-template-columns:1fr;

    }


    .pi-row{

        flex-direction:column;

        align-items:flex-start;

        gap:12px;

    }


    .pi-row > div:last-child{

        text-align:left !important;

    }

}

</style>



{{-- =========================================================
     AI OVERVIEW
========================================================= --}}

<div class="pi-grid pi-grid-4">


@foreach([

    [
        '👥',
        'Total Voters',
        $insights['total_voters'] ?? 0
    ],

    [
        '🟢',
        'Support %',
        ($insights['support_percentage'] ?? 0) . '%'
    ],

    [
        '⚪',
        'Neutral %',
        ($insights['neutral_percentage'] ?? 0) . '%'
    ],

    [
        '🔴',
        'Opposition %',
        ($insights['opposition_percentage'] ?? 0) . '%'
    ],

] as $card)


<div class="pi-card">

    <div class="pi-icon">
        {{ $card[0] }}
    </div>

    <div class="pi-label">
        {{ $card[1] }}
    </div>

    <div class="pi-number">
        {{ $card[2] }}
    </div>

</div>


@endforeach


</div>



{{-- =========================================================
     AI STRATEGY
========================================================= --}}

<div class="pi-section">

    <div class="pi-header">

        🤖 AI Campaign Recommendation

    </div>


    <div class="pi-body">


        <div class="pi-row">

            <div>

                <strong>
                    Campaign Status
                </strong>

                <br>

                {{ $strategy['campaign_status'] ?? 'Monitoring' }}

            </div>


            <div>

                <span class="pi-badge">

                    {{ $strategy['priority'] ?? 'LOW' }}

                </span>

            </div>

        </div>



        <strong>
            Recommended Actions
        </strong>


        @forelse($strategy['actions'] ?? [] as $action)


            <div class="pi-row">

                ✅ {{ $action }}

            </div>


        @empty


            <div class="pi-empty">

                No campaign actions available.

            </div>


        @endforelse


    </div>

</div>



{{-- =========================================================
     AI BOOTH WAR ROOM
========================================================= --}}

<div class="pi-section">

    <div class="pi-header">

        🤖 AI Booth War Room

    </div>


    <div class="pi-body">


    @forelse($boothRecommendations->take(10) as $booth)


        @php

            $boothId =
                $booth['booth_id']
                ?? null;

            $boothName =
                $booth['booth']
                ?? 'Unknown Booth';

            $totalVoters =
                $booth['total_voters']
                ?? 0;

            $support =
                $booth['support_percentage']
                ?? 0;

            $neutral =
                $booth['neutral_percentage']
                ?? 0;

            $aiScore =
                $booth['ai_score']
                ?? 0;

            $priority =
                strtoupper(
                    $booth['priority']
                    ?? 'LOW'
                );

            $action =
                $booth['action']
                ?? 'Monitor booth';


            $priorityClass = match($priority){

                'HIGH' =>
                    'pi-badge-high',

                'MEDIUM' =>
                    'pi-badge-medium',

                default =>
                    'pi-badge-low',

            };

        @endphp


        <div class="pi-row">


            {{-- BOOTH INFORMATION --}}

            <div class="pi-booth-info">


                <div class="pi-booth-name">

                    🗳️ {{ $boothName }}

                </div>


                <div>

                    Total Voters:
                    <strong>
                        {{ $totalVoters }}
                    </strong>

                </div>


                <div>

                    Support:
                    <strong>
                        {{ $support }}%
                    </strong>

                </div>


                <div>

                    Neutral:
                    <strong>
                        {{ $neutral }}%
                    </strong>

                </div>


                <div class="pi-ai-score">

                    AI Risk Score:
                    {{ $aiScore }}/100

                </div>


                <div class="pi-action">

                    🎯 {{ $action }}

                </div>


            </div>



            {{-- BOOTH ACTION --}}

            <div style="text-align:right;">


                <span
                    class="pi-badge {{ $priorityClass }}"
                >

                    {{ $priority }} PRIORITY

                </span>


                <br><br>


                @if($boothId)


                    <a

                        href="{{ url('/admin/booth-intelligence?booth=' . $boothId . '&source=ai') }}"

                        class="pi-button"

                    >

                        🔍 Open Booth Intelligence

                    </a>


                @else


                    <span class="pi-pending">

                        ⚠️ Booth ID Missing

                    </span>


                @endif


            </div>


        </div>


    @empty


        <div class="pi-empty">

            No booth intelligence found

        </div>


    @endforelse


    </div>

</div>



{{-- =========================================================
     WEAK BOOTHS
========================================================= --}}

<div class="pi-section">

    <div class="pi-header">

        ⚠️ Weak Booth Detection

    </div>


    <div class="pi-body">


    @forelse($weakBooths->take(10) as $booth)


        @php

            $total =
                $booth['total']
                ?? 0;

            $strength =
                $booth['strength']
                ?? 0;

        @endphp


        <div class="pi-row">


            <div>

                <strong>

                    {{ $booth['booth'] ?? 'Unknown Booth' }}

                </strong>


                @if($total > 0)


                    <br>

                    Total:
                    {{ $total }}


                @else


                    <br>

                    <span class="pi-pending">

                        ⚠️ No voter data available

                    </span>


                @endif

            </div>



            <div>


                @if($total > 0)


                    <span class="pi-badge">

                        Strength:
                        {{ $strength }}%

                    </span>


                @else


                    <span class="pi-pending">

                        Data Pending

                    </span>


                @endif


            </div>


        </div>


    @empty


        <div class="pi-empty">

            No booth data found

        </div>


    @endforelse


    </div>

</div>



{{-- =========================================================
     PRIORITY VILLAGES
========================================================= --}}

<div class="pi-section">

    <div class="pi-header">

        🏘️ Priority Villages

    </div>


    <div class="pi-body">


    @forelse($priorityVillages->take(10) as $village)


        <div class="pi-row">


            <div>

                <strong>

                    {{ $village['village'] ?? 'Unknown Village' }}

                </strong>


                <br>

                Total Voters:

                {{ $village['total_voters'] ?? 0 }}

            </div>


            <div>

                <span class="pi-badge">

                    Priority:

                    {{ $village['priority_score'] ?? 0 }}

                </span>

            </div>


        </div>


    @empty


        <div class="pi-empty">

            No village data found

        </div>


    @endforelse


    </div>

</div>



{{-- =========================================================
     SWING VOTERS
========================================================= --}}

<div class="pi-section">

    <div class="pi-header">

        🎯 Swing Voter List

    </div>


    <div class="pi-body">


    @forelse($swingVoters->take(10) as $voter)


        <div class="pi-row">


            <div>

                <strong>

                    {{ $voter->name ?? '-' }}

                </strong>


                <br>

                {{ $voter->support_level ?? 'Not Assigned' }}

            </div>


            <div>

                <span class="pi-badge">

                    {{ $voter->house?->booth?->booth_name ?? '-' }}

                </span>

            </div>


        </div>


    @empty


        <div class="pi-empty">

            No swing voters found

        </div>


    @endforelse


    </div>

</div>



</x-filament-panels::page>
