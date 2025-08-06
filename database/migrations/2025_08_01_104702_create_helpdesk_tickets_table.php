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
        Schema::create('helpdesk_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('category_id')->constrained('helpdesk_categories')->onDelete('restrict');
            $table->string('status')->default('open'); // e.g., 'open', 'in_progress', 'resolved', 'closed'
            $table->foreignId('priority_id')->constrained('helpdesk_priorities')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Applicant
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->onDelete('set null'); // Agent
            $table->timestamp('closed_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamp('sla_due_at')->nullable(); // For SLA tracking
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helpdesk_tickets');
    }
};
