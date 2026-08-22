<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internal_conversations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('constituency_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject');
            $table->string('type')->default('Direct');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            $table->index(['constituency_id', 'last_message_at'], 'internal_conversation_scope_index');
        });

        Schema::create('internal_conversation_participants', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('internal_conversation_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamp('muted_at')->nullable();
            $table->timestamps();
            $table->foreign('internal_conversation_id', 'internal_participant_conversation_fk')->references('id')->on('internal_conversations')->cascadeOnDelete();
            $table->unique(['internal_conversation_id', 'user_id'], 'internal_conversation_user_unique');
            $table->index(['user_id', 'archived_at'], 'internal_participant_inbox_index');
        });

        Schema::create('internal_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('internal_conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reply_to_id')->nullable()->constrained('internal_messages')->nullOnDelete();
            $table->longText('body');
            $table->string('priority')->default('Normal');
            $table->string('attachment_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['internal_conversation_id', 'created_at'], 'internal_message_thread_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internal_messages');
        Schema::dropIfExists('internal_conversation_participants');
        Schema::dropIfExists('internal_conversations');
    }
};
