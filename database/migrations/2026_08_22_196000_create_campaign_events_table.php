<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_events', function (Blueprint $table): void {
            $table->id();
            $table->string('event_code', 32)->unique();
            $table->foreignId('constituency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('booth_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('candidate_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('coordinator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('event_type')->default('Public Meeting');
            $table->string('status')->default('Planned');
            $table->string('venue');
            $table->text('address')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('expected_attendance')->default(0);
            $table->unsignedInteger('actual_attendance')->default(0);
            $table->decimal('estimated_budget', 14, 2)->default(0);
            $table->decimal('actual_expense', 14, 2)->default(0);
            $table->string('permission_status')->default('Not Required');
            $table->string('permission_reference')->nullable();
            $table->json('requirements')->nullable();
            $table->json('attachments')->nullable();
            $table->text('description')->nullable();
            $table->text('outcome_notes')->nullable();
            $table->timestamps();
            $table->index(['constituency_id', 'starts_at', 'status'], 'campaign_event_schedule_index');
        });
        Schema::create('campaign_event_team_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('Team Member');
            $table->string('attendance_status')->default('Assigned');
            $table->timestamp('checked_in_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['campaign_event_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_event_team_members');
        Schema::dropIfExists('campaign_events');
    }
};
