<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE surveys
            MODIFY type ENUM(
                'Door to Door',
                'Membership',
                'Issue',
                'Feedback',
                'Exit Poll',
                'Custom',
                'House Survey',
                'Government Scheme',
                'Voter Survey'
            )
        ");
    }


    public function down(): void
    {
        DB::statement("
            ALTER TABLE surveys
            MODIFY type ENUM(
                'Door to Door',
                'Membership',
                'Issue',
                'Feedback',
                'Exit Poll',
                'Custom'
            )
        ");
    }
};