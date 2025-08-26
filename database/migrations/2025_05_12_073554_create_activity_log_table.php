<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for activity_log table.
 * Used by the spatie/laravel-activitylog package for auditing changes/events.
 */
class CreateActivityLogTable extends Migration
{
    public function up(): void
    {
        $connection = config('activitylog.database_connection') ?: config('database.default');
        $tableName  = config('activitylog.table_name', 'activity_log');

        Schema::connection($connection)->create(
            $tableName,
            function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->string('log_name')->nullable();
                $table->text('description');
                $table->nullableMorphs('subject', 'subject');
                $table->nullableMorphs('causer', 'causer');
                $table->json('properties')->nullable();
                $table->timestamps();
                $table->index('log_name');
            }
        );
    }

    public function down(): void
    {
        $connection = config('activitylog.database_connection') ?: config('database.default');
        $tableName  = config('activitylog.table_name', 'activity_log');

        Schema::connection($connection)->dropIfExists($tableName);
    }
}
