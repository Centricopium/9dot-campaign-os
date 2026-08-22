<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_daily_briefings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('constituency_id')->constrained()->cascadeOnDelete();
            $table->date('briefing_date');
            $table->string('status')->default('Draft');
            $table->text('priority_message')->nullable();
            $table->text('morning_objectives')->nullable();
            $table->unsignedInteger('target_tasks')->default(0);
            $table->unsignedInteger('target_contacts')->default(0);
            $table->unsignedInteger('target_issues')->default(0);
            $table->text('planned_events')->nullable();
            $table->text('evening_summary')->nullable();
            $table->text('achievements')->nullable();
            $table->text('blockers')->nullable();
            $table->text('next_day_priorities')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->unique(['constituency_id', 'briefing_date'], 'daily_briefing_assembly_date_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_daily_briefings');
    }
};
