<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'ticket_categories' table used by the legacy ticketing system.
 * Adds is_active and blameable fields to align with the rest of the system.
 */

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            // Blameable + soft deletes
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->softDeletes();
            $table->timestamps();

            $table->index(['is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('ticket_categories', function (Blueprint $table): void {
            foreach (['created_by', 'updated_by', 'deleted_by'] as $col) {
                if (Schema::hasColumn('ticket_categories', $col)) {
                    try {
                        $table->dropForeign([$col]);
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }
            }
        });

        Schema::dropIfExists('ticket_categories');
    }
};
