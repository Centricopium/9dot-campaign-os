<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('constituency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('political_party_id')->nullable()->constrained()->nullOnDelete();
            $table->string('election_name')->default('Assembly Election');
            $table->unsignedSmallInteger('election_year');
            $table->string('name');
            $table->string('mobile', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('photo')->nullable();
            $table->string('current_position')->nullable();
            $table->unsignedSmallInteger('political_experience_years')->default(0);
            $table->text('profile_summary')->nullable();
            $table->text('public_work')->nullable();
            $table->text('strengths')->nullable();
            $table->text('concerns')->nullable();
            $table->string('status')->default('Under Assessment');
            $table->boolean('is_final')->default(false)->index();
            $table->foreignId('finalised_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finalised_at')->nullable();
            $table->text('final_decision_notes')->nullable();
            $table->timestamps();

            $table->index(['constituency_id', 'political_party_id', 'election_year'], 'candidate_selection_pool_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
