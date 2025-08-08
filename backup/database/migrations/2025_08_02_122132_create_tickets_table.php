<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'tickets' table for generic helpdesk/ticketing.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table): void {
            $table->id();
            $table->string('subject');
            $table->text('description');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('User reporting/creating ticket');
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->onDelete('set null')->comment('Assigned agent');
            $table->foreignId('category_id')->constrained('ticket_categories')->onDelete('restrict');
            $table->foreignId('priority_id')->constrained('ticket_priorities')->onDelete('restrict');
            $table->enum('status', ['open', 'in_progress', 'pending_user_feedback', 'resolved', 'closed', 'reopened'])->default('open');
            $table->dateTime('due_date')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
