<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('survey_questions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('survey_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('question');

            $table->string('field_name');

            $table->enum('field_type', [
                'text',
                'textarea',
                'number',
                'date',
                'dropdown',
                'radio',
                'checkbox',
                'yes_no',
                'rating',
            ]);

            $table->boolean('required')->default(false);

            $table->integer('sort_order')->default(0);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_questions');
    }
};