<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('villages', function (Blueprint $table) {

            $table->id();

            $table->foreignId('constituency_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('village_code')->nullable();
            $table->string('taluka');
            $table->string('district');

            $table->integer('population')->default(0);
            $table->integer('total_voters')->default(0);
            $table->integer('total_booths')->default(0);

            $table->enum('category', [
                'General',
                'SC',
                'ST',
                'OBC',
            ])->default('General');

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villages');
    }
};