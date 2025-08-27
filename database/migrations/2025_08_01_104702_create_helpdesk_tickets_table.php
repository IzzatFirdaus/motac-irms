<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'helpdesk_tickets' table for Helpdesk system.
 * Main table for all Helpdesk tickets.
 */

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('helpdesk_tickets', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('category_id')->constrained('helpdesk_categories')->onDelete('restrict');
            $table->string('status')->default('open'); // open, in_progress, resolved, closed
            $table->foreignId('priority_id')->constrained('helpdesk_priorities')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Applicant user');
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->onDelete('set null')->comment('Assigned agent');
            $table->foreignId('closed_by_id')->nullable()->constrained('users')->onDelete('set null')->comment('User who closed ticket');
            $table->timestamp('closed_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamp('sla_due_at')->nullable()->comment('SLA due date');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('helpdesk_tickets');
    }
};
