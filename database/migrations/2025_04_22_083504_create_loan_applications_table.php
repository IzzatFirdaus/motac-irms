<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'loan_applications' table for ICT equipment loan requests.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ENUM status values for loan applications
        $statuses = [
            'draft',
            'pending_support',
            'pending_approver_review',
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
            $table->foreignId('responsible_officer_id')->nullable()->comment('Officer responsible, if not applicant')->constrained('users')->onDelete('set null');
            $table->foreignId('supporting_officer_id')->nullable()->comment('Supporting officer')->constrained('users')->onDelete('set null');
            $table->text('purpose');
            $table->string('location')->comment('Usage location');
            $table->string('return_location')->nullable()->comment('Return location');
            $table->dateTime('loan_start_date');
            $table->dateTime('loan_end_date');
            $table->enum('status', $statuses)->default($defaultStatus);
            $table->text('rejection_reason')->nullable();
            $table->timestamp('applicant_confirmation_timestamp')->nullable()->comment('Applicant confirmation timestamp');
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
            $foreignKeys = [
                'user_id', 'responsible_officer_id', 'supporting_officer_id', 'approved_by',
                'rejected_by', 'cancelled_by', 'current_approval_officer_id',
                'created_by', 'updated_by', 'deleted_by',
            ];
            foreach ($foreignKeys as $key) {
                if (Schema::hasColumn('loan_applications', $key)) {
                    try {
                        $table->dropForeign([$key]);
                    } catch (\Exception $e) {
                        Log::warning(sprintf('Failed to drop FK %s on loan_applications during down migration: ', $key).$e->getMessage());
                    }
                }
            }
        });
        Schema::dropIfExists('loan_applications');
    }
};
