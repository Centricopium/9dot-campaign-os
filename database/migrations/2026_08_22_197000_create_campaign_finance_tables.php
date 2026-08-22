<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_budgets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('constituency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('category');
            $table->decimal('allocated_amount', 15, 2)->default(0);
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->string('status')->default('Active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['constituency_id', 'category', 'status'], 'campaign_budget_scope_index');
        });

        Schema::create('campaign_expenses', function (Blueprint $table): void {
            $table->id();
            $table->string('expense_code', 32)->unique();
            $table->foreignId('constituency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_budget_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_event_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('expense_date');
            $table->string('category');
            $table->string('vendor')->nullable();
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->default('Cash');
            $table->string('payment_status')->default('Pending');
            $table->string('approval_status')->default('Submitted');
            $table->timestamp('approved_at')->nullable();
            $table->string('receipt_path')->nullable();
            $table->string('invoice_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['constituency_id', 'expense_date', 'approval_status'], 'campaign_expense_scope_index');
            $table->index(['campaign_budget_id', 'payment_status'], 'campaign_expense_budget_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_expenses');
        Schema::dropIfExists('campaign_budgets');
    }
};
