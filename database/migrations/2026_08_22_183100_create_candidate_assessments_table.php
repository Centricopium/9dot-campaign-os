<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_assessments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessor_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('winnability_score')->default(0);
            $table->unsignedTinyInteger('constituency_connect_score')->default(0);
            $table->unsignedTinyInteger('organisation_strength_score')->default(0);
            $table->unsignedTinyInteger('public_work_score')->default(0);
            $table->unsignedTinyInteger('integrity_score')->default(0);
            $table->unsignedTinyInteger('leadership_score')->default(0);
            $table->unsignedTinyInteger('party_loyalty_score')->default(0);
            $table->unsignedTinyInteger('campaign_readiness_score')->default(0);
            $table->unsignedTinyInteger('compliance_score')->default(0);
            $table->decimal('overall_score', 5, 2)->default(0);
            $table->unsignedTinyInteger('risk_score')->default(0);
            $table->unsignedTinyInteger('confidence_score')->default(0);
            $table->text('remarks')->nullable();
            $table->text('evidence_notes')->nullable();
            $table->boolean('is_submitted')->default(true);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['candidate_id', 'assessor_id']);
            $table->index(['candidate_id', 'is_submitted']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_assessments');
    }
};
