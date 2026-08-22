<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Event Planner Report</title>
    <style>
        @page { margin: 28px 30px 34px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #17111f; font-family: DejaVu Sans, sans-serif; font-size: 9px; }
        .header { padding: 18px 20px; border-radius: 14px; color: #fff; background: #251137; }
        .eyebrow { margin-bottom: 5px; color: #c4b5fd; font-size: 8px; font-weight: bold; letter-spacing: 1.2px; text-transform: uppercase; }
        h1 { margin: 0; font-size: 23px; line-height: 1.1; }
        .subtitle { margin-top: 7px; color: #ddd6fe; font-size: 9px; }
        .meta { float: right; width: 32%; margin-top: -46px; text-align: right; color: #ede9fe; line-height: 1.6; }
        .filters { margin: 13px 0 10px; padding: 9px 12px; border: 1px solid #ddd6fe; border-radius: 8px; background: #faf9ff; color: #4c1d95; }
        .stats { width: 100%; margin-bottom: 13px; border-collapse: separate; border-spacing: 6px 0; }
        .stats td { width: 16.66%; padding: 10px 11px; border: 1px solid #e9d5ff; border-radius: 8px; background: #faf7ff; }
        .stats span { display: block; color: #7c3aed; font-size: 7px; font-weight: bold; letter-spacing: .6px; text-transform: uppercase; }
        .stats strong { display: block; margin-top: 4px; color: #17111f; font-size: 16px; }
        h2 { margin: 0 0 7px; font-size: 13px; }
        table.schedule { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .schedule th { padding: 8px 6px; color: #fff; background: #4c1d95; font-size: 7px; letter-spacing: .3px; text-align: left; text-transform: uppercase; }
        .schedule td { padding: 7px 6px; border-bottom: 1px solid #e5e7eb; vertical-align: top; line-height: 1.35; overflow-wrap: break-word; }
        .schedule tr:nth-child(even) td { background: #fafafa; }
        .event-title { font-weight: bold; color: #111827; }
        .muted { color: #6b7280; font-size: 7.5px; }
        .status { display: inline-block; padding: 2px 5px; border-radius: 8px; color: #5b21b6; background: #ede9fe; font-size: 7px; font-weight: bold; }
        .pending { color: #92400e; background: #fef3c7; }
        .empty { padding: 32px; border: 1px dashed #c4b5fd; border-radius: 10px; color: #6b7280; text-align: center; }
        .footer { position: fixed; right: 0; bottom: -22px; left: 0; color: #9ca3af; font-size: 7px; }
        .footer .brand { color: #6d28d9; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="eyebrow">9Dot Campaign Operating System</div>
        <h1>Event, Rally & Candidate Tour Planner</h1>
        <div class="subtitle">Operational schedule, permissions, field team, attendance and budget overview</div>
        <div class="meta">
            <strong>{{ $assemblyLabel }}</strong><br>
            {{ $typeLabel }}<br>
            Generated {{ $generatedAt->format('d M Y, h:i A') }}
        </div>
    </div>

    <div class="filters"><strong>Applied Filters:</strong> Assembly - {{ $assemblyLabel }} &nbsp;|&nbsp; Event Type - {{ $typeLabel }}</div>

    <table class="stats">
        <tr>
            <td><span>Total Events</span><strong>{{ number_format($stats['total']) }}</strong></td>
            <td><span>Upcoming</span><strong>{{ number_format($stats['upcoming']) }}</strong></td>
            <td><span>Completed</span><strong>{{ number_format($stats['completed']) }}</strong></td>
            <td><span>Permission Pending</span><strong>{{ number_format($stats['permissions_pending']) }}</strong></td>
            <td><span>Expected Audience</span><strong>{{ number_format($stats['expected_attendance']) }}</strong></td>
            <td><span>Actual Attendance</span><strong>{{ number_format($stats['actual_attendance']) }}</strong></td>
        </tr>
    </table>

    <h2>Event Schedule</h2>
    @if($events->isEmpty())
        <div class="empty">No events found for the selected filters.</div>
    @else
        <table class="schedule">
            <thead>
                <tr>
                    <th style="width: 4%">#</th>
                    <th style="width: 10%">Date & Time</th>
                    <th style="width: 15%">Event</th>
                    <th style="width: 12%">Area</th>
                    <th style="width: 12%">Venue</th>
                    <th style="width: 13%">Leadership</th>
                    <th style="width: 9%">Permission</th>
                    <th style="width: 9%">Attendance</th>
                    <th style="width: 9%">Budget</th>
                    <th style="width: 7%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ $event->starts_at?->format('d M Y') }}</strong><br>
                            <span class="muted">{{ $event->starts_at?->format('h:i A') }}@if($event->ends_at) - {{ $event->ends_at->format('h:i A') }}@endif</span>
                        </td>
                        <td>
                            <span class="event-title">{{ $event->title }}</span><br>
                            <span class="muted">{{ $event->event_code }} | {{ $event->event_type }}</span>
                        </td>
                        <td>
                            {{ $event->constituency?->name ?? '-' }}<br>
                            <span class="muted">{{ $event->village?->name ?? 'All villages' }}@if($event->booth) | Booth {{ $event->booth->booth_no }}@endif</span>
                        </td>
                        <td>{{ $event->venue ?: '-' }}@if($event->address)<br><span class="muted">{{ $event->address }}</span>@endif</td>
                        <td>
                            Candidate: {{ $event->candidate?->name ?? 'Not assigned' }}<br>
                            <span class="muted">Coordinator: {{ $event->coordinator?->name ?? 'Not assigned' }} | Team: {{ $event->team_members_count }}</span>
                        </td>
                        <td>
                            <span class="status {{ $event->permission_status === 'Pending' ? 'pending' : '' }}">{{ $event->permission_status ?: 'Not set' }}</span>
                            @if($event->permission_reference)<br><span class="muted">Ref: {{ $event->permission_reference }}</span>@endif
                        </td>
                        <td>
                            {{ number_format((int) $event->actual_attendance) }} / {{ number_format((int) $event->expected_attendance) }}<br>
                            <span class="muted">{{ $event->attendance_percent }}% achieved</span>
                        </td>
                        <td>
                            Est. Rs. {{ number_format((float) $event->estimated_budget, 0) }}<br>
                            <span class="muted">Spent Rs. {{ number_format((float) $event->actual_expense, 0) }}</span>
                        </td>
                        <td><span class="status">{{ $event->status }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer"><span class="brand">9Dot Campaign OS</span> - Confidential campaign operations document</div>
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font('DejaVu Sans', 'normal');
            $pdf->page_text(720, 570, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, 7, array(0.42, 0.45, 0.50));
        }
    </script>
</body>
</html>
