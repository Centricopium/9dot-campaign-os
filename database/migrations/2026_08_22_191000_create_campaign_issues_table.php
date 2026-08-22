<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_issues', function (Blueprint $table): void {
            $table->id();
            $table->string('issue_code', 32)->unique();
            $table->foreignId('constituency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('booth_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('house_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('voter_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_task_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('category')->default('Other');
            $table->string('source')->default('Field Visit');
            $table->string('priority')->default('Medium');
            $table->string('status')->default('Open');
            $table->text('description');
            $table->string('contact_name')->nullable();
            $table->string('contact_mobile', 20)->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->json('attachments')->nullable();
            $table->json('resolution_proof')->nullable();
            $table->boolean('is_confidential')->default(false);
            $table->timestamps();

            $table->index(['constituency_id', 'status', 'due_at'], 'campaign_issue_scope_status_due_index');
            $table->index(['assigned_to', 'status'], 'campaign_issue_assignee_status_index');
            $table->index(['category', 'priority'], 'campaign_issue_category_priority_index');
        });

        Schema::create('campaign_issue_updates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_issue_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->index(['campaign_issue_id', 'created_at'], 'campaign_issue_update_timeline_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_issue_updates');
        Schema::dropIfExists('campaign_issues');
    }
};
