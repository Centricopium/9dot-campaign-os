<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Assembly Booth Organisation</title>

    <style>

        @page {
            size: A4 landscape;
            margin: 14px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 7px;
            color: #111827;
            margin: 0;
            padding: 0;
        }

        h1 {
            font-size: 18px;
            margin: 0 0 4px 0;
        }

        .subtitle {
            font-size: 8px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .filters {
            margin-bottom: 8px;
            padding: 5px 7px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            font-size: 7px;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .summary td {
            width: 25%;
            padding: 6px;
            border: 1px solid #d1d5db;
            text-align: center;
        }

        .summary-label {
            font-size: 7px;
            color: #6b7280;
        }

        .summary-number {
            font-size: 14px;
            font-weight: bold;
            margin-top: 2px;
        }

        table.organisation {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.organisation th {
            background: #f3f4f6;
            border: 1px solid #9ca3af;
            padding: 4px;
            font-size: 6.5px;
            font-weight: bold;
            text-align: left;
            vertical-align: top;
        }

        table.organisation td {
            border: 1px solid #d1d5db;
            padding: 4px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .booth-no {
            font-size: 8px;
            font-weight: bold;
        }

        .booth-name {
            margin-top: 2px;
            font-weight: bold;
            font-size: 7px;
        }

        .village {
            margin-top: 2px;
            color: #6b7280;
            font-size: 6px;
        }

        .person {
            margin-bottom: 4px;
        }

        .person:last-child {
            margin-bottom: 0;
        }

        .person-name {
            font-weight: bold;
            font-size: 7px;
        }

        .person-mobile {
            margin-top: 1px;
            color: #4b5563;
            font-size: 6px;
        }

        .person-house {
            margin-top: 1px;
            color: #6b7280;
            font-size: 5.5px;
        }

        .separator {
            border: 0;
            border-top: 1px solid #e5e7eb;
            margin: 3px 0;
        }

        .empty {
            color: #9ca3af;
            font-size: 6.5px;
        }

        .status {
            font-weight: bold;
            font-size: 6.5px;
        }

        .status-complete {
            color: #15803d;
        }

        .status-pending {
            color: #d97706;
        }

        .role-count {
            display: inline-block;
            margin-bottom: 3px;
            padding: 2px 4px;
            border-radius: 8px;
            background: #eff6ff;
            color: #2563eb;
            font-size: 5.5px;
            font-weight: bold;
        }

        .footer {
            margin-top: 8px;
            font-size: 6px;
            color: #9ca3af;
            text-align: right;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

    </style>

</head>


<body>


<h1>
    Assembly Booth Organisation
</h1>


<div class="subtitle">
    Booth-wise Organisation Report
</div>


{{-- =========================================================
     FILTERS
========================================================= --}}

@if(
    ($search ?? '') !== ''
    ||
    ($villageFilter ?? '') !== ''
    ||
    ($roleFilter ?? '') !== ''
    ||
    (
        ($statusFilter ?? '') !== ''
        &&
        ($statusFilter ?? '') !== 'all'
    )
)

    <div class="filters">

        <strong>Filters:</strong>

        @if(($search ?? '') !== '')
            Search: {{ $search }}
        @endif

        @if(($villageFilter ?? '') !== '')

            @if(($search ?? '') !== '')
                &nbsp; | &nbsp;
            @endif

            Village: {{ $villageFilter }}

        @endif

        @if(($roleFilter ?? '') !== '')

            &nbsp; | &nbsp;

            Role: {{ $roleFilter }}

        @endif

        @if(
            ($statusFilter ?? '') !== ''
            &&
            ($statusFilter ?? '') !== 'all'
        )

            &nbsp; | &nbsp;

            Status: {{ ucfirst($statusFilter) }}

        @endif

    </div>

@endif


{{-- =========================================================
     SUMMARY
========================================================= --}}

<table class="summary">

    <tr>

        <td>

            <div class="summary-label">
                Total Active Booths
            </div>

            <div class="summary-number">
                {{ $summary['total_booths'] ?? 0 }}
            </div>

        </td>


        <td>

            <div class="summary-label">
                Booth Presidents
            </div>

            <div class="summary-number">
                {{ $summary['presidents'] ?? 0 }}
            </div>

        </td>


        <td>

            <div class="summary-label">
                Mahila Presidents
            </div>

            <div class="summary-number">
                {{ $summary['mahila_presidents'] ?? 0 }}
            </div>

        </td>


        <td>

            <div class="summary-label">
                Youth Presidents
            </div>

            <div class="summary-number">
                {{ $summary['youth_presidents'] ?? 0 }}
            </div>

        </td>

    </tr>

</table>


{{-- =========================================================
     ORGANISATION TABLE
========================================================= --}}

<table class="organisation">

    <thead>

        <tr>

            <th style="width: 8%;">
                Booth
            </th>

            <th style="width: 10%;">
                Booth President
            </th>

            <th style="width: 10%;">
                Mahila President
            </th>

            <th style="width: 10%;">
                Youth President
            </th>

            <th style="width: 8%;">
                Vice President
            </th>

            <th style="width: 9%;">
                General Secretary
            </th>

            <th style="width: 8%;">
                Secretary
            </th>

            <th style="width: 8%;">
                Treasurer
            </th>

            <th style="width: 9%;">
                Panna Pramukh
            </th>

            <th style="width: 8%;">
                Polling Agent
            </th>

            <th style="width: 7%;">
                Volunteers
            </th>

            <th style="width: 7%;">
                Status
            </th>

        </tr>

    </thead>


    <tbody>


    @forelse($rows as $row)

        @php

            /*
            |--------------------------------------------------------------------------
            | Convert Everything To Collection
            |--------------------------------------------------------------------------
            */

            $president = collect(
                $row['president'] ?? []
            )->values();

            $mahilaPresident = collect(
                $row['mahila_president'] ?? []
            )->values();

            $youthPresident = collect(
                $row['youth_president'] ?? []
            )->values();

            $vicePresident = collect(
                $row['vice_president'] ?? []
            )->values();

            $generalSecretary = collect(
                $row['general_secretary'] ?? []
            )->values();

            $secretary = collect(
                $row['secretary'] ?? []
            )->values();

            $treasurer = collect(
                $row['treasurer'] ?? []
            )->values();

            $pannaPramukh = collect(
                $row['panna_pramukh'] ?? []
            )->values();

            $pollingAgent = collect(
                $row['polling_agent'] ?? []
            )->values();

            $volunteers = collect(
                $row['volunteers'] ?? []
            )->values();

            $mahilaVolunteers = collect(
                $row['mahila_volunteers'] ?? []
            )->values();

            $youthVolunteers = collect(
                $row['youth_volunteers'] ?? []
            )->values();

            /*
            |--------------------------------------------------------------------------
            | All Volunteers
            |--------------------------------------------------------------------------
            */

            $allVolunteers = collect()
                ->merge($volunteers)
                ->merge($mahilaVolunteers)
                ->merge($youthVolunteers)
                ->values();

            /*
            |--------------------------------------------------------------------------
            | Core Team Status
            |--------------------------------------------------------------------------
            */

            $isComplete =
                $president->isNotEmpty()
                &&
                $mahilaPresident->isNotEmpty()
                &&
                $youthPresident->isNotEmpty();

        @endphp


        <tr>


            {{-- =================================================
                 BOOTH
            ================================================== --}}

            <td>

                <div class="booth-no">
                    Booth {{ $row['booth_no'] ?? '-' }}
                </div>

                <div class="booth-name">
                    {{ $row['booth_name'] ?? '-' }}
                </div>

                <div class="village">
                    {{ $row['village'] ?? '-' }}
                </div>

            </td>


            {{-- =================================================
                 PRESIDENT
            ================================================== --}}

            <td>

                @forelse($president as $person)

                    <div class="person">

                        <div class="person-name">
                            {{ $person['name'] ?? '-' }}
                        </div>

                        <div class="person-mobile">
                            Mobile:
                            {{ $person['mobile'] ?? '-' }}
                        </div>

                        @if(
                            ! empty(
                                $person['house_no'] ?? ''
                            )
                        )

                            <div class="person-house">
                                House:
                                {{ $person['house_no'] }}
                            </div>

                        @endif

                    </div>

                    @if(!$loop->last)
                        <hr class="separator">
                    @endif

                @empty

                    <span class="empty">
                        Not Assigned
                    </span>

                @endforelse

            </td>


            {{-- =================================================
                 MAHILA PRESIDENT
            ================================================== --}}

            <td>

                @forelse($mahilaPresident as $person)

                    <div class="person">

                        <div class="person-name">
                            {{ $person['name'] ?? '-' }}
                        </div>

                        <div class="person-mobile">
                            Mobile:
                            {{ $person['mobile'] ?? '-' }}
                        </div>

                    </div>

                    @if(!$loop->last)
                        <hr class="separator">
                    @endif

                @empty

                    <span class="empty">
                        Not Assigned
                    </span>

                @endforelse

            </td>


            {{-- =================================================
                 YOUTH PRESIDENT
            ================================================== --}}

            <td>

                @forelse($youthPresident as $person)

                    <div class="person">

                        <div class="person-name">
                            {{ $person['name'] ?? '-' }}
                        </div>

                        <div class="person-mobile">
                            Mobile:
                            {{ $person['mobile'] ?? '-' }}
                        </div>

                    </div>

                    @if(!$loop->last)
                        <hr class="separator">
                    @endif

                @empty

                    <span class="empty">
                        Not Assigned
                    </span>

                @endforelse

            </td>


            {{-- =================================================
                 VICE PRESIDENT
            ================================================== --}}

            <td>

                @forelse($vicePresident as $person)

                    <div class="person">

                        <div class="person-name">
                            {{ $person['name'] ?? '-' }}
                        </div>

                        <div class="person-mobile">
                            Mobile:
                            {{ $person['mobile'] ?? '-' }}
                        </div>

                    </div>

                @empty

                    <span class="empty">
                        Not Assigned
                    </span>

                @endforelse

            </td>


            {{-- =================================================
                 GENERAL SECRETARY
            ================================================== --}}

            <td>

                @forelse($generalSecretary as $person)

                    <div class="person">

                        <div class="person-name">
                            {{ $person['name'] ?? '-' }}
                        </div>

                        <div class="person-mobile">
                            Mobile:
                            {{ $person['mobile'] ?? '-' }}
                        </div>

                    </div>

                @empty

                    <span class="empty">
                        Not Assigned
                    </span>

                @endforelse

            </td>


            {{-- =================================================
                 SECRETARY
            ================================================== --}}

            <td>

                @forelse($secretary as $person)

                    <div class="person">

                        <div class="person-name">
                            {{ $person['name'] ?? '-' }}
                        </div>

                        <div class="person-mobile">
                            Mobile:
                            {{ $person['mobile'] ?? '-' }}
                        </div>

                    </div>

                @empty

                    <span class="empty">
                        Not Assigned
                    </span>

                @endforelse

            </td>


            {{-- =================================================
                 TREASURER
            ================================================== --}}

            <td>

                @forelse($treasurer as $person)

                    <div class="person">

                        <div class="person-name">
                            {{ $person['name'] ?? '-' }}
                        </div>

                        <div class="person-mobile">
                            Mobile:
                            {{ $person['mobile'] ?? '-' }}
                        </div>

                    </div>

                @empty

                    <span class="empty">
                        Not Assigned
                    </span>

                @endforelse

            </td>


            {{-- =================================================
                 PANNA PRAMUKH
            ================================================== --}}

            <td>

                @if($pannaPramukh->isNotEmpty())

                    <div class="role-count">
                        {{ $pannaPramukh->count() }} Assigned
                    </div>

                @endif


                @forelse($pannaPramukh as $person)

                    <div class="person">

                        <div class="person-name">
                            {{ $person['name'] ?? '-' }}
                        </div>

                        <div class="person-mobile">
                            Mobile:
                            {{ $person['mobile'] ?? '-' }}
                        </div>

                    </div>

                @empty

                    <span class="empty">
                        Not Assigned
                    </span>

                @endforelse

            </td>


            {{-- =================================================
                 POLLING AGENT
            ================================================== --}}

            <td>

                @forelse($pollingAgent as $person)

                    <div class="person">

                        <div class="person-name">
                            {{ $person['name'] ?? '-' }}
                        </div>

                        <div class="person-mobile">
                            Mobile:
                            {{ $person['mobile'] ?? '-' }}
                        </div>

                    </div>

                @empty

                    <span class="empty">
                        Not Assigned
                    </span>

                @endforelse

            </td>


            {{-- =================================================
                 VOLUNTEERS
            ================================================== --}}

            <td>

                @if($allVolunteers->isNotEmpty())

                    <div class="role-count">
                        {{ $allVolunteers->count() }} Assigned
                    </div>

                @endif


                @forelse($allVolunteers as $person)

                    <div class="person">

                        <div class="person-name">
                            {{ $person['name'] ?? '-' }}
                        </div>

                        <div class="person-mobile">
                            Mobile:
                            {{ $person['mobile'] ?? '-' }}
                        </div>

                    </div>

                @empty

                    <span class="empty">
                        Not Assigned
                    </span>

                @endforelse

            </td>


            {{-- =================================================
                 STATUS
            ================================================== --}}

            <td>

                @if($isComplete)

                    <span class="status status-complete">
                        COMPLETE
                    </span>

                @else

                    <span class="status status-pending">
                        PENDING
                    </span>

                @endif

            </td>


        </tr>


    @empty

        <tr>

            <td colspan="12">

                <div class="empty">
                    No active booths found.
                </div>

            </td>

        </tr>

    @endforelse


    </tbody>

</table>


<div class="footer">
    Assembly Booth Organisation Report
</div>


</body>

</html>