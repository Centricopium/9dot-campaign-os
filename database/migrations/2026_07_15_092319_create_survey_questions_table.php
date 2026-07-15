<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_questions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('survey_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('question');

            $table->enum('type', [
                'text',
                'textarea',
                'number',
                'dropdown',
                'radio',
                'checkbox',
                'date',
                'rating',
                'yes_no',
            ]);

            $table->json('options')->nullable();

            $table->boolean('required')->default(false);

            $table->integer('sort_order')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_questions');
    }
};