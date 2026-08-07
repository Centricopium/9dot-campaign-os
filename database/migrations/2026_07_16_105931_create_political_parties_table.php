<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('political_parties', function (Blueprint $table) {

            $table->id();

            $table->string('name');

            $table->string('short_name', 20)->unique();

            $table->string('symbol')->nullable();

            $table->string('color', 20)->default('#2563EB');

            $table->boolean('is_active')->default(true);

            $table->integer('sort_order')->default(1);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('political_parties');
    }
};