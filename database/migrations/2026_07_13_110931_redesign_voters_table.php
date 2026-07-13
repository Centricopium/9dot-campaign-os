<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('voters', function (Blueprint $table) {

            $table->string('part_no')->nullable()->after('serial_no');

            $table->string('email')->nullable()->after('whatsapp');

            $table->string('photo')->nullable()->after('email');

            $table->string('category')->nullable()->after('religion');

            $table->string('party_preference')->nullable()->after('support_level');

            $table->boolean('is_volunteer')->default(false)->after('is_influencer');

            $table->date('last_contact_date')->nullable()->after('is_volunteer');

        });
    }

    public function down(): void
    {
        Schema::table('voters', function (Blueprint $table) {

            $table->dropColumn([
                'part_no',
                'email',
                'photo',
                'category',
                'party_preference',
                'is_volunteer',
                'last_contact_date',
            ]);

        });
    }
};