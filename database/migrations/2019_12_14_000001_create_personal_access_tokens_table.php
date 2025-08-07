<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the personal_access_tokens table.
 * Used by Laravel Sanctum for API token management.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create table for storing personal access tokens
        Schema::create('personal_access_tokens', function (Blueprint $table): void {
            $table->id();                      // Primary key
            $table->morphs('tokenable');       // Polymorphic relation (user, etc.)
            $table->string('name');            // Token name/label
            $table->string('token', 64)->unique(); // Hashed token string
            $table->text('abilities')->nullable(); // JSON list of abilities/permissions
            $table->timestamp('last_used_at')->nullable(); // Last usage timestamp
            $table->timestamp('expires_at')->nullable();   // Expiration timestamp
            $table->timestamps();              // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the personal_access_tokens table
        Schema::dropIfExists('personal_access_tokens');
    }
};
