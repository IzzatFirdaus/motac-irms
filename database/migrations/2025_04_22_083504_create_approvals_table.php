<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Updates and creates the 'approvals' table for workflow approvals (e.g., loan, etc.).
 * This version supports a richer workflow with multiple status values and action timestamps.
 * Uses polymorphic 'approvable' relationship for reusability.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table): void {
            $table->id();

            // Polymorphic relation to approvable model (e.g., LoanApplication)
            $table->morphs('approvable'); // approvable_type, approvable_id

            // Officer responsible for this approval
            $table->foreignId('officer_id')->constrained('users')->onDelete('cascade')->comment('Officer responsible for this approval task');

            // Approval stage and status
            $table->string('stage')->nullable()->index()->comment('Approval stage: e.g., support_review, hod_review');
            $table->string('status')->default('pending')->index()->comment('Approval status: pending, approved, rejected, canceled, forwarded');

            // Notes and decision timestamps
            $table->text('notes')->nullable()->comment('Notes or comments from officer');
            $table->timestamp('approved_at')->nullable()->comment('Timestamp when approved');
            $table->timestamp('rejected_at')->nullable()->comment('Timestamp when rejected');
            $table->timestamp('canceled_at')->nullable()->comment('Timestamp when canceled');
            $table->timestamp('resubmitted_at')->nullable()->comment('Timestamp when resubmitted/forwarded');

            // Standard blameable/audit columns
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // The following line was removed due to lack of native support in Laravel:
            // $table->check("status IN ('pending','approved','rejected','canceled','forwarded')");
        });
    }

    public function down(): void
    {
        Schema::table('approvals', function (Blueprint $table): void {
            if (Schema::hasColumn('approvals', 'officer_id')) {
                $table->dropForeign(['officer_id']);
            }
            if (Schema::hasColumn('approvals', 'created_by')) {
                $table->dropForeign(['created_by']);
            }
            if (Schema::hasColumn('approvals', 'updated_by')) {
                $table->dropForeign(['updated_by']);
            }
            if (Schema::hasColumn('approvals', 'deleted_by')) {
                $table->dropForeign(['deleted_by']);
            }
        });
        Schema::dropIfExists('approvals');
    }
};
