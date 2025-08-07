<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'helpdesk_comments' table for Helpdesk system.
 * Stores comments on tickets (can be internal/external).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('helpdesk_comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ticket_id')->constrained('helpdesk_tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('comment');
            $table->boolean('is_internal')->default(false)->comment('True if only visible to agents/staff');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('helpdesk_comments');
    }
};
