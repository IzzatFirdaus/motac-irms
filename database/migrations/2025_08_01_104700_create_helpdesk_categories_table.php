<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'helpdesk_categories' table for Helpdesk ticketing module.
 * E.g., Hardware, Software, Network, etc.
 */

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('helpdesk_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique()->comment('Category name, e.g., Hardware, Software');
            $table->text('description')->nullable()->comment('Category description');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('helpdesk_categories');
    }
};
