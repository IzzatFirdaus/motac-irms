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
        Schema::create('helpdesk_attachments', function (Blueprint $table) {
            $table->id();
            $table->morphs('attachable'); // This will add attachable_id and attachable_type
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_size'); // Store as string for flexibility (e.g., "1.5 MB")
            $table->string('file_type'); // MIME type
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helpdesk_attachments');
    }
};
