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
        Schema::create('booth_organisations', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Booth
            |--------------------------------------------------------------------------
            */

            $table->foreignId('booth_id')
                ->constrained('booths')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Voter / Organisation Member
            |--------------------------------------------------------------------------
            */

            $table->foreignId('voter_id')
                ->constrained('voters')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Organisation Role
            |--------------------------------------------------------------------------
            */

            $table->string('role', 100);

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index([
                'booth_id',
                'role',
            ]);

            $table->index([
                'voter_id',
            ]);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booth_organisations');
    }
};