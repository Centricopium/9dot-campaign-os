<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_tasks', function (Blueprint $table): void {
            $table->id();
            $table->string('task_code', 32)->unique();
            $table->foreignId('constituency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('booth_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('category')->default('Field Visit');
            $table->string('priority')->default('Medium');
            $table->string('status')->default('Pending');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('completion_percent')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->boolean('requires_proof')->default(false);
            $table->json('proof_files')->nullable();
            $table->text('field_notes')->nullable();
            $table->text('completion_notes')->nullable();
            $table->text('review_notes')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->index(['constituency_id', 'status', 'due_at'], 'campaign_task_scope_status_due_index');
            $table->index(['assigned_to', 'status'], 'campaign_task_assignee_status_index');
            $table->index(['village_id', 'booth_id'], 'campaign_task_area_index');
        });

        Schema::create('campaign_task_updates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->unsignedTinyInteger('completion_percent')->default(0);
            $table->text('notes')->nullable();
            $table->json('proof_files')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->index(['campaign_task_id', 'created_at'], 'campaign_task_update_timeline_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_task_updates');
        Schema::dropIfExists('campaign_tasks');
    }
};
