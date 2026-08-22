<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('house_intelligence', function (Blueprint $table) {

            $table->id();

            $table->foreignId('house_id')
                ->constrained()
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | AI Family Score
            |--------------------------------------------------------------------------
            */

            $table->integer('family_score')
                ->default(0);


            $table->string('family_type')
                ->nullable();


            $table->integer('conversion_probability')
                ->default(0);



            /*
            |--------------------------------------------------------------------------
            | AI Priority
            |--------------------------------------------------------------------------
            */

            $table->enum('ai_priority', [
                'LOW',
                'MEDIUM',
                'HIGH'
            ])
            ->default('MEDIUM');



            /*
            |--------------------------------------------------------------------------
            | Family Influencer
            |--------------------------------------------------------------------------
            */

            $table->foreignId('family_influencer_id')
                ->nullable()
                ->constrained('voters')
                ->nullOnDelete();



            /*
            |--------------------------------------------------------------------------
            | Campaign Follow Up
            |--------------------------------------------------------------------------
            */

            $table->date('last_visit_date')
                ->nullable();


            $table->date('next_followup_date')
                ->nullable();



            /*
            |--------------------------------------------------------------------------
            | AI Output
            |--------------------------------------------------------------------------
            */

            $table->text('ai_recommendation')
                ->nullable();



            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('house_intelligence');
    }
};