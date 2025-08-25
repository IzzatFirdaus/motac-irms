<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
<<<<<<< HEAD
 * Migration to create the webhook_calls table for storing webhook events.
=======
 * Migration for storing webhook calls and their payloads/results.
>>>>>>> release/v4.0
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the webhook_calls table with necessary columns.
     */
    public function up(): void
    {
        Schema::create('webhook_calls', function (Blueprint $table): void {
            $table->bigIncrements('id');
<<<<<<< HEAD
            $table->string('name');
            $table->string('url');
            $table->json('headers')->nullable();
            $table->json('payload')->nullable();
            $table->text('exception')->nullable();
=======
            $table->string('name');      // Name/label for the webhook (e.g., "Notify Slack")
            $table->string('url');       // Target URL for the webhook
            $table->json('headers')->nullable(); // HTTP headers sent
            $table->json('payload')->nullable(); // JSON payload sent
            $table->text('exception')->nullable(); // Any exception or error message
>>>>>>> release/v4.0
            $table->timestamps();
        });
    }

<<<<<<< HEAD
    /**
     * Reverse the migrations.
     * Drops the webhook_calls table if it exists.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_calls'); // Properly drops table on rollback
=======
    public function down(): void
    {
        Schema::dropIfExists('webhook_calls');
>>>>>>> release/v4.0
    }
};
