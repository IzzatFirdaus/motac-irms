<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'users' table for authentication.
 * Standard Laravel structure, extended by other migrations for custom columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->comment('Display name or username');
            $table->string('email')->unique()->comment('User login email');
            $table->timestamp('email_verified_at')->nullable()->comment('When the email was verified');
            $table->string('password')->comment('Hashed password');
            $table->rememberToken()->comment('Token for "remember me" functionality');
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
