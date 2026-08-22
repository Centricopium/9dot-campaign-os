<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('
            ALTER TABLE survey_questions
            MODIFY type VARCHAR(50) NOT NULL
        ');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("
            ALTER TABLE survey_questions
            MODIFY type ENUM(
                'text',
                'textarea',
                'number',
                'radio',
                'checkbox',
                'select',
                'boolean'
            ) NOT NULL
        ");
    }
};
