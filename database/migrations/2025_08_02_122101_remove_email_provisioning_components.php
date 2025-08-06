<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Added for potential logging in down() method

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, clean up approvals related to EmailApplication
        // This is important before dropping the email_applications table
        DB::table('approvals')->where('approvable_type', 'App\\Models\\EmailApplication')->delete();

        // Drop the email_applications table
        Schema::dropIfExists('email_applications');

        // Remove specific columns from users table
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'motac_email')) {
                // Drop unique constraint first, if it exists and is explicitly named
                // Laravel typically names unique constraints as tablename_columnname_unique
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the email_applications table
        Schema::create('email_applications', function (Blueprint $table) {
            $table->id();
            // Assuming basic fields for rollback, adjust if needed
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
            // These should match the types and constraints they had previously
            // Refer to your 2013_11_01_132200_add_motac_columns_to_users_table.php for exact definitions
            $table->string('motac_email')->unique()->nullable()->after('email');
            $table->string('user_id_assigned')->unique()->nullable()->after('motac_email');
            $table->string('previous_department_name')->nullable()->after('user_id_assigned');
            $table->string('previous_department_email')->nullable()->after('previous_department_name');
            $table->string('service_status')->nullable()->after('previous_department_email');
            $table->string('appointment_type')->nullable()->after('service_status');
        });

        // Revert approvals table data is generally not feasible or recommended in a down() method,
        // as it's hard to distinguish which records belonged to EmailApplication before deletion.
        // If strict data integrity on rollback for approvals is crucial, a manual database restore
        // or a more complex data archival/restoration strategy would be needed.
        // For this automated rollback, we assume a full backup is available if historical approval data needs to be fully restored.
    }
};
