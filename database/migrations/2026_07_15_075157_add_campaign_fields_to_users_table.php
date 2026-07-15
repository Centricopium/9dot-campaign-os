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
        Schema::table('users', function (Blueprint $table) {

            // Assignment
            $table->foreignId('constituency_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('village_id')
                ->nullable()
                ->after('constituency_id')
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('booth_id')
                ->nullable()
                ->after('village_id')
                ->constrained()
                ->nullOnDelete();

            // Profile
            $table->string('mobile')->nullable()->after('email');

            $table->string('designation')->nullable()->after('mobile');

            $table->string('employee_code')->nullable()->unique()->after('designation');

            $table->string('profile_photo')->nullable()->after('employee_code');

            // Status
            $table->boolean('is_active')->default(true)->after('profile_photo');

            $table->timestamp('last_login_at')->nullable()->after('is_active');

            // Notes
            $table->longText('notes')->nullable()->after('last_login_at');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropConstrainedForeignId('constituency_id');
            $table->dropConstrainedForeignId('village_id');
            $table->dropConstrainedForeignId('booth_id');

            $table->dropColumn([
                'mobile',
                'designation',
                'employee_code',
                'profile_photo',
                'is_active',
                'last_login_at',
                'notes',
            ]);

        });
    }
};