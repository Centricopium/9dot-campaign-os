<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payslip {{ $item->employee_code_snapshot }}</title>
    <style>
        @page { margin: 28px; }
        body { margin:0; font-family:DejaVu Sans, sans-serif; color:#1f1728; font-size:12px; }
        .header { padding:24px; color:white; background:#24102f; border-bottom:7px solid #7c3aed; }
        .brand { font-size:25px; font-weight:bold; }
        .brand span { color:#c4b5fd; }
        .period { float:right; margin-top:-27px; text-align:right; }
        .period strong { font-size:16px; }
        .box { margin-top:18px; border:1px solid #ddd6e6; border-radius:8px; overflow:hidden; }
        .box-title { padding:10px 14px; font-weight:bold; color:#4c1d95; background:#f5f3ff; border-bottom:1px solid #ddd6e6; }
        .info { width:100%; border-collapse:collapse; }
        .info td { width:25%; padding:10px 14px; border-bottom:1px solid #eeeaf2; vertical-align:top; }
        .label { display:block; margin-bottom:3px; color:#776d80; font-size:9px; text-transform:uppercase; }
        .value { font-weight:bold; }
        .pay { width:100%; border-collapse:collapse; }
        .pay th { padding:9px 12px; color:#5b5263; background:#faf9fb; text-align:left; border-bottom:1px solid #ddd6e6; }
        .pay td { padding:8px 12px; border-bottom:1px solid #eeeaf2; }
        .amount { text-align:right !important; }
        .total td { padding:12px; font-size:14px; font-weight:bold; background:#f5f3ff; }
        .net { margin-top:16px; padding:16px; color:white; background:#4c1d95; }
        .net-label { font-size:11px; color:#ddd6fe; }
        .net-value { float:right; margin-top:-20px; font-size:23px; font-weight:bold; }
        .footer { margin-top:28px; padding-top:12px; color:#83798b; font-size:9px; text-align:center; border-top:1px solid #ddd6e6; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">9Dot <span>Campaign OS</span></div>
        <div class="period"><strong>Salary Payslip</strong><br>{{ $item->payrollRun->period_label }}</div>
    </div>

    <div class="box">
        <div class="box-title">Employee & Payroll Details</div>
        <table class="info">
            <tr>
                <td><span class="label">Employee</span><span class="value">{{ $item->employee_name_snapshot }}</span></td>
                <td><span class="label">Employee ID</span><span class="value">{{ $item->employee_code_snapshot }}</span></td>
                <td><span class="label">Designation</span><span class="value">{{ $item->designation_snapshot ?: '-' }}</span></td>
                <td><span class="label">Assembly</span><span class="value">{{ $item->payrollRun->constituency?->name ?: '-' }}</span></td>
            </tr>
            <tr>
                <td><span class="label">Working Days</span><span class="value">{{ number_format($item->working_days, 1) }}</span></td>
                <td><span class="label">Present</span><span class="value">{{ number_format($item->present_days, 1) }}</span></td>
                <td><span class="label">Paid / Unpaid Leave</span><span class="value">{{ number_format($item->paid_leave_days, 1) }} / {{ number_format($item->unpaid_leave_days, 1) }}</span></td>
                <td><span class="label">Payment Status</span><span class="value">{{ $item->payment_status }}</span></td>
            </tr>
        </table>
    </div>

    <div class="box">
        <div class="box-title">Earnings & Deductions</div>
        <table class="pay">
            <thead><tr><th>Earnings</th><th class="amount">Amount</th><th>Deductions</th><th class="amount">Amount</th></tr></thead>
            <tbody>
                <tr><td>Basic Pay</td><td class="amount">Rs. {{ number_format($item->basic_pay, 2) }}</td><td>Deductions</td><td class="amount">Rs. {{ number_format($item->deductions, 2) }}</td></tr>
                <tr><td>Allowances</td><td class="amount">Rs. {{ number_format($item->allowances, 2) }}</td><td>Advances</td><td class="amount">Rs. {{ number_format($item->advances, 2) }}</td></tr>
                <tr><td>Incentives</td><td class="amount">Rs. {{ number_format($item->incentives, 2) }}</td><td></td><td></td></tr>
                <tr><td>Overtime</td><td class="amount">Rs. {{ number_format($item->overtime_pay, 2) }}</td><td></td><td></td></tr>
                <tr><td>Reimbursements</td><td class="amount">Rs. {{ number_format($item->reimbursements, 2) }}</td><td></td><td></td></tr>
                <tr class="total"><td>Gross Pay</td><td class="amount">Rs. {{ number_format($item->gross_pay, 2) }}</td><td>Total Deductions</td><td class="amount">Rs. {{ number_format($item->deductions + $item->advances, 2) }}</td></tr>
            </tbody>
        </table>
    </div>

    <div class="net"><div class="net-label">NET PAYABLE</div><div class="net-value">Rs. {{ number_format($item->net_pay, 2) }}</div></div>
    <div class="footer">Computer-generated payslip · Generated on {{ now()->format('d M Y, h:i A') }} · 9Dot Campaign OS</div>
</body>
</html>
