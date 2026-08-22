<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('internal_conversation_participants', function (Blueprint $table): void {
            $table->unsignedBigInteger('last_read_message_id')->nullable()->after('last_read_at')->index('internal_participant_last_read_index');
        });
    }

    public function down(): void
    {
        Schema::table('internal_conversation_participants', function (Blueprint $table): void {
            $table->dropIndex('internal_participant_last_read_index');
            $table->dropColumn('last_read_message_id');
        });
    }
};
