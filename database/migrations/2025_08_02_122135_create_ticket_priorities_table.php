<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'ticket_priorities' table for generic helpdesk/ticketing.
 * E.g., Low, Medium, High, Critical.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_priorities', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique()->comment('Priority name');
            $table->integer('level')->comment('Numeric level for sorting, e.g. 1=Low, 5=Critical');
            $table->string('color_code')->nullable()->comment('Hex color for UI');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_priorities');
    }
};
