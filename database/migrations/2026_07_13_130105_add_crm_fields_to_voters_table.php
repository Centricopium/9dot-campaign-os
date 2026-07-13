<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('voters', function (Blueprint $table) {

            $table->string('voter_type')->nullable()->after('category');
            $table->string('blood_group')->nullable()->after('education');
            $table->boolean('disability')->default(false)->after('blood_group');

            $table->boolean('government_scheme')->default(false);
            $table->string('government_scheme_name')->nullable();

            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();

            $table->integer('family_members')->default(0);

            $table->string('booth_committee_role')->nullable();
            $table->string('panna_pramukh')->nullable();
            $table->string('polling_agent')->nullable();

            $table->string('priority')->default('Normal');

            $table->date('next_followup')->nullable();

            $table->decimal('latitude',10,7)->nullable();
            $table->decimal('longitude',10,7)->nullable();

            $table->text('internal_notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('voters', function (Blueprint $table) {

            $table->dropColumn([
                'voter_type',
                'blood_group',
                'disability',
                'government_scheme',
                'government_scheme_name',
                'facebook',
                'instagram',
                'twitter',
                'family_members',
                'booth_committee_role',
                'panna_pramukh',
                'polling_agent',
                'priority',
                'next_followup',
                'latitude',
                'longitude',
                'internal_notes',
            ]);

        });
    }
};