<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('mobile')->nullable()->after('email');

            $table->string('designation')->nullable()->after('mobile');

            $table->string('employee_code')->nullable()->unique()->after('designation');

            $table->string('profile_photo')->nullable()->after('employee_code');

            $table->foreignId('constituency_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('village_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('booth_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->boolean('is_super_admin')
                ->default(false);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamp('last_login_at')
                ->nullable();

            $table->text('notes')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropConstrainedForeignId('booth_id');

            $table->dropConstrainedForeignId('village_id');

            $table->dropConstrainedForeignId('constituency_id');

            $table->dropColumn([
                'mobile',
                'designation',
                'employee_code',
                'profile_photo',
                'is_super_admin',
                'is_active',
                'last_login_at',
                'notes',
            ]);
        });
    }
};