<?php

namespace App\Http\Controllers;

use App\Models\PayrollItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PayrollPayslipController extends Controller
{
    public function __invoke(PayrollItem $payrollItem): Response
    {
        abort_unless(auth()->user()?->can('payroll.export'), 403);

        $payrollItem->load(['employee.constituency', 'payrollRun.constituency']);

        return Pdf::loadView('reports.payroll-payslip', ['item' => $payrollItem])
            ->setPaper('a4')
            ->download(sprintf(
                'payslip-%s-%s.pdf',
                $payrollItem->employee_code_snapshot,
                $payrollItem->payrollRun->period_start->format('Y-m')
            ));
    }
}
