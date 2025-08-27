<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'ticket_comments' table for the legacy ticket system.
 * Comments can be internal (agent-only) or external.
 */

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_comments', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->text('comment');
            $table->boolean('is_internal')->default(false);

            // Blameable + soft delete
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->softDeletes();
            $table->timestamps();

            $table->index(['ticket_id']);
            $table->index(['user_id']);
            $table->index(['is_internal']);
        });
    }

    public function down(): void
    {
        Schema::table('ticket_comments', function (Blueprint $table): void {
            foreach (['ticket_id', 'user_id', 'created_by', 'updated_by', 'deleted_by'] as $col) {
                if (Schema::hasColumn('ticket_comments', $col)) {
                    try {
                        $table->dropForeign([$col]);
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }
            }
        });

        Schema::dropIfExists('ticket_comments');
    }
};
