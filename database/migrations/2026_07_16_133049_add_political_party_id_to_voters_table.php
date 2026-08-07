<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('voters', function (Blueprint $table) {

            $table->foreignId('political_party_id')
                ->nullable()
                ->after('party_support')
                ->constrained('political_parties')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('voters', function (Blueprint $table) {

            $table->dropConstrainedForeignId('political_party_id');

        });
    }
};