<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('constituency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('employee_code', 40)->unique();
            $table->string('name');
            $table->string('mobile', 20)->nullable();
            $table->string('email')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('department')->nullable();
            $table->string('designation')->nullable();
            $table->string('employment_type')->default('Full Time');
            $table->string('salary_type')->default('Monthly');
            $table->decimal('base_salary', 13, 2)->default(0);
            $table->decimal('daily_rate', 11, 2)->default(0);
            $table->decimal('hourly_rate', 11, 2)->default(0);
            $table->decimal('default_allowance', 11, 2)->default(0);
            $table->decimal('default_deduction', 11, 2)->default(0);
            $table->date('joining_date');
            $table->date('leaving_date')->nullable();
            $table->string('status')->default('Active');
            $table->string('photo_path')->nullable();
            $table->string('identity_type')->nullable();
            $table->text('identity_number')->nullable();
            $table->string('identity_document_path')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->text('bank_account_number')->nullable();
            $table->string('ifsc_code', 20)->nullable();
            $table->text('upi_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['constituency_id', 'status', 'department'], 'employee_scope_index');
        });

        Schema::create('employee_attendances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('attendance_date');
            $table->string('status')->default('Present');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->decimal('overtime_hours', 6, 2)->default(0);
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'attendance_date'], 'employee_attendance_unique');
            $table->index(['attendance_date', 'status'], 'attendance_date_status_index');
        });

        Schema::create('employee_leaves', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('leave_type')->default('Casual');
            $table->date('starts_on');
            $table->date('ends_on');
            $table->decimal('days', 6, 2)->default(1);
            $table->boolean('is_paid')->default(false);
            $table->string('status')->default('Pending');
            $table->text('reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->timestamps();
            $table->index(['employee_id', 'starts_on', 'ends_on'], 'employee_leave_period_index');
        });

        Schema::create('payroll_runs', function (Blueprint $table): void {
            $table->id();
            $table->string('run_code', 40)->unique();
            $table->foreignId('constituency_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->date('period_start');
            $table->date('period_end');
            $table->unsignedSmallInteger('working_days')->default(0);
            $table->string('status')->default('Draft');
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['constituency_id', 'period_year', 'period_month'], 'payroll_period_unique');
            $table->index(['constituency_id', 'status'], 'payroll_run_scope_index');
        });

        Schema::create('payroll_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->restrictOnDelete();
            $table->string('employee_code_snapshot', 40);
            $table->string('employee_name_snapshot');
            $table->string('designation_snapshot')->nullable();
            $table->string('salary_type_snapshot');
            $table->decimal('salary_rate_snapshot', 13, 2)->default(0);
            $table->decimal('present_days', 6, 2)->default(0);
            $table->decimal('paid_leave_days', 6, 2)->default(0);
            $table->decimal('unpaid_leave_days', 6, 2)->default(0);
            $table->decimal('worked_hours', 8, 2)->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('basic_pay', 13, 2)->default(0);
            $table->decimal('allowances', 13, 2)->default(0);
            $table->decimal('incentives', 13, 2)->default(0);
            $table->decimal('overtime_pay', 13, 2)->default(0);
            $table->decimal('reimbursements', 13, 2)->default(0);
            $table->decimal('deductions', 13, 2)->default(0);
            $table->decimal('advances', 13, 2)->default(0);
            $table->decimal('gross_pay', 13, 2)->default(0);
            $table->decimal('net_pay', 13, 2)->default(0);
            $table->string('payment_status')->default('Pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->date('payment_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['payroll_run_id', 'employee_id'], 'payroll_employee_unique');
            $table->index(['payroll_run_id', 'payment_status'], 'payroll_item_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
        Schema::dropIfExists('payroll_runs');
        Schema::dropIfExists('employee_leaves');
        Schema::dropIfExists('employee_attendances');
        Schema::dropIfExists('employees');
    }
};
