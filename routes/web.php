<?php

use App\Http\Controllers\AssemblyBoothOrganisationExportController;
use App\Http\Controllers\InternalMessageAttachmentController;
use App\Http\Controllers\PayrollPayslipController;
use App\Http\Controllers\PayrollRegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Default
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/admin/login');

/*
|--------------------------------------------------------------------------
| Assembly Booth Organisation PDF Export
|--------------------------------------------------------------------------
*/

Route::get(
    '/assembly-booth-organisation/export-pdf',
    [
        AssemblyBoothOrganisationExportController::class,
        'pdf',
    ]
)
    ->name(
        'assembly-booth-organisation.pdf'
    );

Route::get('/internal-messages/{message}/attachment', InternalMessageAttachmentController::class)
    ->middleware('auth')
    ->name('internal-messages.attachment');

Route::middleware('auth')->group(function (): void {
    Route::get('/hr/payroll-items/{payrollItem}/payslip', PayrollPayslipController::class)
        ->name('hr.payroll.payslip');

    Route::get('/hr/payroll-runs/{payrollRun}/register', PayrollRegisterController::class)
        ->name('hr.payroll.register');
});
