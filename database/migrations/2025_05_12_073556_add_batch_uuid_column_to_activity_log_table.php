<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBatchUuidColumnToActivityLogTable extends Migration
{
    public function up(): void
    {
        $connection = config('activitylog.database_connection') ?: config('database.default');
        $tableName = config('activitylog.table_name', 'activity_log');

        Schema::connection($connection)->table(
            $tableName,
            function (Blueprint $table): void {
                $table
                    ->uuid('batch_uuid')
                    ->nullable()
                    ->after('properties');
            }
        );
    }

    public function down(): void
    {
        $connection = config('activitylog.database_connection') ?: config('database.default');
        $tableName = config('activitylog.table_name', 'activity_log');

        Schema::connection($connection)->table(
            $tableName,
            function (Blueprint $table): void {
                $table->dropColumn('batch_uuid');
            }
        );
    }
}
