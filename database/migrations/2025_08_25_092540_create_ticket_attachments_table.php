<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'ticket_attachments' table for the legacy ticket system.
 * Attachments are linked to tickets and (optionally) comments. Polymorphic avoided for simpler model mapping.
 */

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_attachments', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('comment_id')->nullable()->constrained('ticket_comments')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('filename');
            $table->string('filepath');   // matches model property
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable()->comment('File size in bytes');

            // Blameable + soft delete
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->softDeletes();
            $table->timestamps();

            $table->index(['ticket_id']);
            $table->index(['comment_id']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('ticket_attachments', function (Blueprint $table): void {
            foreach (['ticket_id', 'comment_id', 'user_id', 'created_by', 'updated_by', 'deleted_by'] as $col) {
                if (Schema::hasColumn('ticket_attachments', $col)) {
                    try {
                        $table->dropForeign([$col]);
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }
            }
        });

        Schema::dropIfExists('ticket_attachments');
    }
};
