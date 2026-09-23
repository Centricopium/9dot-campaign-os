<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payroll Register {{ $run->run_code }}</title>
    <style>
        @page { margin:22px; }
        body { font-family:DejaVu Sans, sans-serif; color:#21182a; font-size:9px; }
        h1 { margin:0; font-size:20px; color:#4c1d95; }
        .meta { margin:5px 0 16px; color:#766d7f; }
        table { width:100%; border-collapse:collapse; }
        th { padding:7px 5px; color:white; background:#2b1735; text-align:left; }
        td { padding:6px 5px; border-bottom:1px solid #e7e1eb; }
        .num { text-align:right; }
        .total td { font-weight:bold; background:#f3e8ff; border-top:2px solid #7c3aed; }
        .footer { margin-top:14px; color:#84798d; text-align:right; }
    </style>
</head>
<body>
    <h1>Payroll Register</h1>
    <div class="meta">{{ $run->run_code }} · {{ $run->period_label }} · {{ $run->constituency?->name }} · Status: {{ $run->status }}</div>
    <table>
        <thead><tr><th>#</th><th>Employee ID</th><th>Employee</th><th>Designation</th><th class="num">Present</th><th class="num">Basic</th><th class="num">Allowances</th><th class="num">Other Earnings</th><th class="num">Deductions</th><th class="num">Net Pay</th><th>Status</th></tr></thead>
        <tbody>
            @foreach ($run->items as $item)
                <tr><td>{{ $loop->iteration }}</td><td>{{ $item->employee_code_snapshot }}</td><td>{{ $item->employee_name_snapshot }}</td><td>{{ $item->designation_snapshot ?: '-' }}</td><td class="num">{{ number_format($item->present_days, 1) }}</td><td class="num">{{ number_format($item->basic_pay, 2) }}</td><td class="num">{{ number_format($item->allowances, 2) }}</td><td class="num">{{ number_format($item->incentives + $item->overtime_pay + $item->reimbursements, 2) }}</td><td class="num">{{ number_format($item->deductions + $item->advances, 2) }}</td><td class="num">{{ number_format($item->net_pay, 2) }}</td><td>{{ $item->payment_status }}</td></tr>
            @endforeach
            <tr class="total"><td colspan="5">Total · {{ $run->items->count() }} employees</td><td class="num">{{ number_format($run->items->sum('basic_pay'), 2) }}</td><td class="num">{{ number_format($run->items->sum('allowances'), 2) }}</td><td class="num">{{ number_format($run->items->sum(fn ($item) => $item->incentives + $item->overtime_pay + $item->reimbursements), 2) }}</td><td class="num">{{ number_format($run->items->sum(fn ($item) => $item->deductions + $item->advances), 2) }}</td><td class="num">{{ number_format($run->items->sum('net_pay'), 2) }}</td><td></td></tr>
        </tbody>
    </table>
    <div class="footer">Generated {{ now()->format('d M Y, h:i A') }} · 9Dot Campaign OS</div>
</body>
</html>
