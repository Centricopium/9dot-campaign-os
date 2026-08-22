<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_communications', function (Blueprint $table): void {
            $table->id();
            $table->string('communication_code', 32)->unique();
            $table->foreignId('constituency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('channel');
            $table->string('content_type')->default('Post');
            $table->string('audience')->default('General Voters');
            $table->string('language')->default('Gujarati');
            $table->string('priority')->default('Normal');
            $table->string('status')->default('Draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->longText('message')->nullable();
            $table->string('call_to_action')->nullable();
            $table->string('asset_path')->nullable();
            $table->string('published_url')->nullable();
            $table->unsignedInteger('planned_reach')->default(0);
            $table->unsignedInteger('actual_reach')->default(0);
            $table->unsignedInteger('engagement_count')->default(0);
            $table->decimal('estimated_cost', 14, 2)->default(0);
            $table->decimal('actual_cost', 14, 2)->default(0);
            $table->text('approval_notes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['constituency_id', 'scheduled_at', 'status'], 'campaign_communication_schedule_index');
            $table->index(['channel', 'status'], 'campaign_communication_channel_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_communications');
    }
};
