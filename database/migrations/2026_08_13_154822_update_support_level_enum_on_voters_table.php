<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
         * Convert existing old support values before
         * changing the database enum.
         */
        DB::table('voters')
            ->where('support_level', 'Congress Leaning')
            ->update([
                'support_level' => 'Neutral',
            ]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("
            ALTER TABLE voters
            MODIFY support_level ENUM(
                'Strong Support',
                'Moderate Support',
                'Leaning Support',
                'Neutral',
                'Undecided',
                'Leaning Opposition',
                'Moderate Opposition',
                'Strong Opposition'
            ) NOT NULL DEFAULT 'Neutral'
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*
         * Convert new values back to values supported
         * by the old enum before restoring it.
         */
        DB::table('voters')
            ->whereIn('support_level', [
                'Strong Support',
                'Moderate Support',
                'Leaning Support',
                'Leaning Opposition',
                'Moderate Opposition',
                'Strong Opposition',
            ])
            ->update([
                'support_level' => 'Neutral',
            ]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("
            ALTER TABLE voters
            MODIFY support_level ENUM(
                'Strong Congress',
                'Congress Leaning',
                'Neutral',
                'Undecided',
                'BJP Leaning',
                'Strong BJP',
                'Other'
            ) NOT NULL DEFAULT 'Neutral'
            ");
        }
    }
};
