<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema; // Added for down method

return new class extends Migration
{
    public function up(): void
    {
        // Define ENUM values directly in the migration
        // Updated 'pending_hod_review' to 'pending_approver_review'
        $statuses = [
            'draft',
            'pending_support',
            'pending_approver_review', // MODIFIED from 'pending_hod_review'
            'pending_bpm_review',
            'approved',
            'rejected',
            'partially_issued',
            'issued',
            'returned',
            'overdue',
            'cancelled',
            'partially_returned_pending_inspection',
        ];
        $defaultStatus = 'draft';

        Schema::create('loan_applications', function (Blueprint $table) use ($statuses, $defaultStatus): void {
            $table->id();
            $table->foreignId('user_id')->comment('Applicant User ID')->constrained('users')->cascadeOnDelete();
            $table->foreignId('responsible_officer_id')->nullable()->comment('User ID of the officer responsible, if not applicant')->constrained('users')->onDelete('set null');
            $table->foreignId('supporting_officer_id')->nullable()->comment('Supporting officer for the application')->constrained('users')->onDelete('set null');

            $table->text('purpose');
            $table->string('location')->comment('Location where equipment will be used');
            $table->string('return_location')->nullable()->comment('Location where equipment will be returned');

            $table->dateTime('loan_start_date');
            $table->dateTime('loan_end_date');

            $table->enum('status', $statuses)->default($defaultStatus);
            $table->text('rejection_reason')->nullable();
            $table->timestamp('applicant_confirmation_timestamp')->nullable()->comment('Timestamp for applicant PART 4 confirmation');

            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('cancelled_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('current_approval_officer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('current_approval_stage')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table): void {
            // Note: The 'issued_by' and 'returned_by' keys might not exist on loan_applications table directly
            // if they are part of loan_transactions. Ensure this list is accurate for direct FKs on loan_applications.
            $foreignKeys = [
                'user_id',
                'responsible_officer_id',
                'supporting_officer_id',
                'approved_by',
                'rejected_by',
                'cancelled_by',
                // 'issued_by', // Likely on loan_transactions, not here
                // 'returned_by', // Likely on loan_transactions, not here
                'current_approval_officer_id',
                'created_by',
                'updated_by',
                'deleted_by',
            ];
            foreach ($foreignKeys as $key) {
                // Check if the column exists before trying to drop the foreign key
                // This is a good practice, especially if the schema might vary slightly.
                if (Schema::hasColumn('loan_applications', $key)) {
                    // It's also good to check if the foreign key constraint itself exists by name
                    // For simplicity here, we just try-catch as you did.
                    try {
                        // Laravel convention for foreign key names is table_column_foreign
                        // $foreignKeyName = 'loan_applications_' . $key . '_foreign';
                        // if (collect(DB::select("SHOW CREATE TABLE loan_applications"))[0]->{'Create Table'} LIKE "%CONSTRAINT `{$foreignKeyName}`%") {
                        //   $table->dropForeign([$key]);
                        // }
                        $table->dropForeign([$key]); // This attempts to drop by column name convention
                    } catch (\Exception $e) {
                        Log::warning(sprintf('Failed to drop FK %s on loan_applications during down migration: ', $key).$e->getMessage());
                    }
                }
            }
        });
        Schema::dropIfExists('loan_applications');
    }
};
