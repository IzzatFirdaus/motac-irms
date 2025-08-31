<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMotacColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add foreign key columns
            $table->string('employee_id')->nullable()->after('id');
            $table->unsignedBigInteger('department_id')->nullable()->after('employee_id');
            $table->unsignedBigInteger('position_id')->nullable()->after('department_id');
            $table->unsignedBigInteger('grade_id')->nullable()->after('position_id');

            // Add tracking columns
            $table->unsignedBigInteger('created_by')->nullable()->after('remember_token');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');

            // Add foreign key constraints
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('set null');
            $table->foreign('grade_id')->references('id')->on('grades')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');

            // Add unique constraint on employee_id
            $table->unique('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Define columns to drop
            $columnsToDrop = [
                'employee_id', 'department_id', 'position_id', 'grade_id',
                'created_by', 'updated_by', 'deleted_by',
            ];

            // Drop employee_id foreign key if exists
            if (Schema::hasColumn('users', 'employee_id')) {
                try {
                    $table->dropForeign(['employee_id']);
                } catch (\Exception $e) {
                    // Foreign key may not exist, continue
                }

                try {
                    $table->dropUnique(['employee_id']);
                } catch (\Exception $e) {
                    // Unique constraint may not exist, continue
                }
            }

            // Drop foreign key constraints for other columns
            foreach (['department_id', 'position_id', 'grade_id', 'created_by', 'updated_by', 'deleted_by'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    try {
                        $table->dropForeign(['users_' . $column . '_foreign']);
                    } catch (\Exception $e) {
                        // Foreign key may not exist, continue
                    }
                }
            }

            // Drop columns if they exist
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
