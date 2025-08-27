<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the password_resets table.
 * Alternative table for password reset functionality used by Laravel.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create table for password reset entries
        Schema::create('password_resets', function (Blueprint $table): void {
            $table->string('email')->index();     // Index on email for lookups
            $table->string('token');              // Token string for reset
            $table->timestamp('created_at')->nullable(); // Timestamp when token was created
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the password_resets table
        Schema::dropIfExists('password_resets');
    }
};
