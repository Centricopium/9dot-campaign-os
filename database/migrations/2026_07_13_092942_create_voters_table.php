<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voters', function (Blueprint $table) {

            $table->id();

            $table->foreignId('booth_id')
                ->constrained()
                ->cascadeOnDelete();

            // Electoral Information
            $table->string('serial_no')->nullable();
            $table->string('epic_no')->nullable()->unique();

            // Personal Details
            $table->string('name');
            $table->string('father_husband_name')->nullable();

            $table->enum('gender', [
                'Male',
                'Female',
                'Other',
            ])->nullable();

            $table->integer('age')->nullable();
            $table->date('dob')->nullable();

            // Contact
            $table->string('mobile')->nullable();
            $table->string('whatsapp')->nullable();

            // Address
            $table->string('house_no')->nullable();
            $table->text('address')->nullable();

            // Social
            $table->string('caste')->nullable();
            $table->string('religion')->nullable();
            $table->string('occupation')->nullable();
            $table->string('education')->nullable();

            // Political
            $table->enum('support_level', [
                'Strong Congress',
                'Congress Leaning',
                'Neutral',
                'Undecided',
                'BJP Leaning',
                'Strong BJP',
                'Other',
            ])->default('Neutral');

            $table->string('party_support')->nullable();
            $table->boolean('is_influencer')->default(false);

            // Survey
            $table->text('remarks')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voters');
    }
};