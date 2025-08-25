<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the password_reset_tokens table.
 * Stores one-time tokens for password reset functionality.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create table for storing password reset tokens
        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();    // User email as primary key
            $table->string('token');               // Token string for reset
            $table->timestamp('created_at')->nullable(); // When the token was generated
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the password_reset_tokens table
        Schema::dropIfExists('password_reset_tokens');
    }
};
