<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for storing webhook calls and their payloads/results.
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
            $table->string('name');      // Name/label for the webhook (e.g., "Notify Slack")
            $table->string('url');       // Target URL for the webhook
            $table->json('headers')->nullable(); // HTTP headers sent
            $table->json('payload')->nullable(); // JSON payload sent
            $table->text('exception')->nullable(); // Any exception or error message
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Drops the webhook_calls table if it exists.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_calls'); // Properly drops table on rollback
    }
};
