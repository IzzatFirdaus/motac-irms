<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'ticket_attachments' table for generic helpdesk/ticketing.
 * Stores files attached to tickets/comments (polymorphic).
 */

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_attachments', function (Blueprint $table): void {
            $table->id();
            $table->morphs('attachable'); // attachable_type, attachable_id
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size')->comment('File size in bytes');
            $table->string('file_type')->comment('MIME type');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_attachments');
    }
};
