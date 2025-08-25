<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Removes deprecated email provisioning components/tables and related user columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Clean up approvals related to EmailApplication (polymorphic)
        DB::table('approvals')->where('approvable_type', 'App\\Models\\EmailApplication')->delete();

        // Drop the deprecated email_applications table
        Schema::dropIfExists('email_applications');

        // Remove specific columns from users table if they exist.
        // Note: SQLite does not support DROP COLUMN in-place; skip destructive changes there.
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Log::info('Skipping dropping user columns on sqlite (unsupported)');
        } else {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'motac_email')) {
                    try {
                        $sm = Schema::getConnection()->getDoctrineSchemaManager();
                        $indexes = $sm->listTableIndexes('users');
                        if (array_key_exists('users_motac_email_unique', $indexes)) {
                            $table->dropUnique(['motac_email']);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Could not check or drop unique index for motac_email: ' . $e->getMessage());
                    }
                    $table->dropColumn('motac_email');
                }
                if (Schema::hasColumn('users', 'user_id_assigned')) {
                    try {
                        $sm = Schema::getConnection()->getDoctrineSchemaManager();
                        $indexes = $sm->listTableIndexes('users');
                        if (array_key_exists('users_user_id_assigned_unique', $indexes)) {
                            $table->dropUnique(['user_id_assigned']);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Could not check or drop unique index for user_id_assigned: ' . $e->getMessage());
                    }
                    $table->dropColumn('user_id_assigned');
                }
                if (Schema::hasColumn('users', 'previous_department_name')) {
                    $table->dropColumn('previous_department_name');
                }
                if (Schema::hasColumn('users', 'previous_department_email')) {
                    $table->dropColumn('previous_department_email');
                }
                if (Schema::hasColumn('users', 'service_status')) {
                    $table->dropColumn('service_status');
                }
                if (Schema::hasColumn('users', 'appointment_type')) {
                    $table->dropColumn('appointment_type');
                }
            });
        }
    }

    public function down(): void
    {
        // Recreate the email_applications table (basic rollback, may need adjustment if structure changes)
        Schema::create('email_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('application_type'); // e.g., 'new_email', 'reset_password'
            $table->string('email_address')->nullable()->unique();
            $table->string('user_id_requested')->nullable()->unique();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Add back columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('motac_email')->unique()->nullable()->after('email');
            $table->string('user_id_assigned')->unique()->nullable()->after('motac_email');
            $table->string('previous_department_name')->nullable()->after('user_id_assigned');
            $table->string('previous_department_email')->nullable()->after('previous_department_name');
            $table->string('service_status')->nullable()->after('previous_department_email');
            $table->string('appointment_type')->nullable()->after('service_status');
        });

        // Note: Restoration of deleted approvals is not feasible automatically.
    }
};
