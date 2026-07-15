<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voters', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relationships
            |--------------------------------------------------------------------------
            */

            $table->foreignId('house_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->index();

            /*
            |--------------------------------------------------------------------------
            | Electoral Information
            |--------------------------------------------------------------------------
            */

            $table->string('serial_no')->nullable()->index();
            $table->string('part_no')->nullable()->index();
            $table->string('epic_no')->nullable()->unique();

            /*
            |--------------------------------------------------------------------------
            | Personal Information
            |--------------------------------------------------------------------------
            */

            $table->string('name')->index();
            $table->string('father_husband_name')->nullable();

            $table->enum('gender', [
                'Male',
                'Female',
                'Other',
            ])->nullable()->index();

            $table->unsignedTinyInteger('age')->nullable()->index();

            $table->date('dob')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Contact
            |--------------------------------------------------------------------------
            */

            $table->string('mobile')->nullable()->index();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Address
            |--------------------------------------------------------------------------
            */

            $table->string('house_no')->nullable();
            $table->text('address')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Photo
            |--------------------------------------------------------------------------
            */

            $table->string('photo')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Social Information
            |--------------------------------------------------------------------------
            */

            $table->string('caste')->nullable()->index();
            $table->string('religion')->nullable()->index();
            $table->string('category')->nullable()->index();
            $table->string('occupation')->nullable();
            $table->string('education')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Political Information
            |--------------------------------------------------------------------------
            */

            $table->enum('support_level', [
                'Strong Congress',
                'Congress Leaning',
                'Neutral',
                'Undecided',
                'BJP Leaning',
                'Strong BJP',
                'Other',
            ])->default('Neutral')->index();

            $table->string('party_support')->nullable();
            $table->string('party_preference')->nullable();

            $table->boolean('is_influencer')->default(false)->index();
            $table->boolean('is_volunteer')->default(false)->index();

            /*
            |--------------------------------------------------------------------------
            | CRM
            |--------------------------------------------------------------------------
            */

            $table->date('last_contact_date')->nullable();

            $table->string('voter_type')->nullable();
            $table->string('blood_group')->nullable();

            $table->boolean('disability')->default(false);

            $table->boolean('government_scheme')->default(false);

            $table->string('government_scheme_name')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Social Media
            |--------------------------------------------------------------------------
            */

            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Family
            |--------------------------------------------------------------------------
            */

            $table->unsignedTinyInteger('family_members')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Organisation
            |--------------------------------------------------------------------------
            */

            $table->string('booth_committee_role')->nullable();
            $table->string('panna_pramukh')->nullable();
            $table->string('polling_agent')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Campaign
            |--------------------------------------------------------------------------
            */

            $table->enum('priority', [
                'Low',
                'Medium',
                'High',
                'Critical',
            ])->default('Medium')->index();

            $table->date('next_followup')->nullable();

            /*
            |--------------------------------------------------------------------------
            | GPS
            |--------------------------------------------------------------------------
            */

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            $table->longText('internal_notes')->nullable();
            $table->text('remarks')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Composite Indexes
            |--------------------------------------------------------------------------
            */

            $table->index(['house_id', 'support_level']);
            $table->index(['part_no', 'serial_no']);
            $table->index(['name', 'mobile']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voters');
    }
};