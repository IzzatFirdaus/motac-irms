<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the failed_jobs table.
 * Stores queue jobs that have failed for retry or inspection.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create table for logging failed queue jobs
        Schema::create('failed_jobs', function (Blueprint $table): void {
            $table->id();                       // Primary key
            $table->string('uuid')->unique();   // Unique job identifier
            $table->text('connection');         // Queue connection name
            $table->text('queue');              // Queue name
            $table->longText('payload');        // Job payload data
            $table->longText('exception');      // Exception stack trace or message
            $table->timestamp('failed_at')->useCurrent(); // When the job failed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the failed_jobs table
        Schema::dropIfExists('failed_jobs');
    }
};
