<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'ticket_priorities' table used by the legacy ticketing system.
 * Adds is_active and blameable fields; includes level for sorting.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_priorities', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedTinyInteger('level')->default(1)->comment('Sorting level, 1=low ... higher = more urgent');
            $table->string('color_code')->nullable()->comment('UI color (hex)');

            // Blameable + soft deletes
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->softDeletes();
            $table->timestamps();

            $table->index(['level']);
            $table->index(['name']);
        });
    }

    public function down(): void
    {
        Schema::table('ticket_priorities', function (Blueprint $table): void {
            foreach (['created_by', 'updated_by', 'deleted_by'] as $col) {
                if (Schema::hasColumn('ticket_priorities', $col)) {
                    try {
                        $table->dropForeign([$col]);
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }
            }
        });

        Schema::dropIfExists('ticket_priorities');
    }
};
