<?php

namespace App\Http\Controllers;

use App\Models\PayrollRun;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PayrollRegisterController extends Controller
{
    public function __invoke(PayrollRun $payrollRun): Response
    {
        abort_unless(auth()->user()?->can('payroll.export'), 403);

        $payrollRun->load([
            'constituency:id,name,district,state',
            'items' => fn ($query) => $query->orderBy('employee_name_snapshot'),
        ]);

        return Pdf::loadView('reports.payroll-register', ['run' => $payrollRun])
            ->setPaper('a4', 'landscape')
            ->download(sprintf('payroll-register-%s.pdf', $payrollRun->run_code));
    }
}
