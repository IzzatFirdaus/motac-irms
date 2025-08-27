<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the sessions table.
 * Tracks user sessions for the web application.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create table for storing session data
        Schema::create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary();      // Session ID
            $table->foreignId('user_id')->nullable()->index(); // Associated user
            $table->string('ip_address', 45)->nullable();     // IP address of session
            $table->text('user_agent')->nullable();           // Browser user agent
            $table->longText('payload');                      // Session payload
            $table->integer('last_activity')->index();        // Timestamp of last activity
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the sessions table
        Schema::dropIfExists('sessions');
    }
};
