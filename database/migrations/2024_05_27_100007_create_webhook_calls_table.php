<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for storing webhook calls and their payloads/results.
 */
return new class extends Migration
{
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

    public function down(): void
    {
        Schema::dropIfExists('webhook_calls');
    }
};
