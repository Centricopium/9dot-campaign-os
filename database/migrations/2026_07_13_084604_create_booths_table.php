<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booths', function (Blueprint $table) {

            $table->id();

            $table->foreignId('village_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('booth_no');
            $table->string('booth_name');

            $table->integer('total_voters')->default(0);
            $table->integer('male_voters')->default(0);
            $table->integer('female_voters')->default(0);
            $table->integer('other_voters')->default(0);

            $table->enum('category', [
                'Urban',
                'Rural'
            ])->default('Rural');

            $table->string('gps_latitude')->nullable();
            $table->string('gps_longitude')->nullable();

            $table->string('google_map')->nullable();

            $table->string('president')->nullable();
            $table->string('worker')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booths');
    }
};