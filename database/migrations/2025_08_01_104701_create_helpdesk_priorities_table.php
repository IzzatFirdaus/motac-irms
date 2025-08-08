<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'helpdesk_priorities' table for Helpdesk ticketing module.
 * Priority levels like Low, Medium, High.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('helpdesk_priorities', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique()->comment('Priority name (Low, High, etc.)');
            $table->integer('level')->default(0)->comment('Sorting level, e.g., 1=Low, 5=Critical');
            $table->string('color_code')->nullable()->comment('Hex color for UI (optional)');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('helpdesk_priorities');
    }
};
