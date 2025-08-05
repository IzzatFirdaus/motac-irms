<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the webhook_calls table for storing webhook events.
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
            $table->string('name');
            $table->string('url');
            $table->json('headers')->nullable();
            $table->json('payload')->nullable();
            $table->text('exception')->nullable();
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
