<?php

namespace App\Filament\Resources\PayrollRuns\Pages;

use App\Filament\Resources\PayrollRuns\PayrollRunResource;
use App\Services\Payroll\PayrollCalculationService;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;

class CreatePayrollRun extends CreateRecord
{
    protected static string $resource = PayrollRunResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $start = Carbon::create((int) $data['period_year'], (int) $data['period_month'], 1)->startOfMonth();
        $data['period_start'] = $start->toDateString();
        $data['period_end'] = $start->copy()->endOfMonth()->toDateString();
        $data['working_days'] = app(PayrollCalculationService::class)->workingDays($data['period_start'], $data['period_end']);
        $data['status'] = 'Draft';

        return $data;
    }
}
