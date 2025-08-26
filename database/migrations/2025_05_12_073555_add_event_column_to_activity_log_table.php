<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the 'event' column to activity_log table for event type tracking.
 */
class AddEventColumnToActivityLogTable extends Migration
{
    public function up(): void
    {
        $connection = config('activitylog.database_connection') ?: config('database.default');
        $tableName  = config('activitylog.table_name', 'activity_log');

        Schema::connection($connection)->table(
            $tableName,
            function (Blueprint $table): void {
                $table
                    ->string('event')
                    ->nullable()
                    ->after('subject_type');
            }
        );
    }

    public function down(): void
    {
        $connection = config('activitylog.database_connection') ?: config('database.default');
        $tableName  = config('activitylog.table_name', 'activity_log');

        Schema::connection($connection)->table(
            $tableName,
            function (Blueprint $table): void {
                $table->dropColumn('event');
            }
        );
    }
}
