<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// Remove: use App\Models\LoanApplication; // Not needed for hardcoded enums

return new class extends Migration
{
  public function up(): void
  {
    // Define ENUM values directly in the migration
    $statuses = [
      'draft',
      'pending_support',
      'pending_hod_review',
      'pending_bpm_review',
      'approved',
      'rejected',
      'partially_issued',
      'issued',
      'returned',
      'overdue',
      'cancelled',
      'partially_returned_pending_inspection'
    ];
    $defaultStatus = 'draft';

    Schema::create('loan_applications', function (Blueprint $table) use ($statuses, $defaultStatus) {
      $table->id();
      $table->foreignId('user_id')->comment('Applicant User ID')->constrained('users')->cascadeOnDelete();
      $table->foreignId('responsible_officer_id')->nullable()->comment('User ID of the officer responsible, if not applicant')->constrained('users')->onDelete('set null');
      // Added supporting_officer_id as per Revision 3
      $table->foreignId('supporting_officer_id')->nullable()->comment('Supporting officer for the application')->constrained('users')->onDelete('set null');


      $table->text('purpose');
      $table->string('location')->comment('Location where equipment will be used');
      // Added return_location as per Revision 3
      $table->string('return_location')->nullable()->comment('Location where equipment will be returned');

      // Changed to datetime as per Revision 3
      $table->dateTime('loan_start_date');
      $table->dateTime('loan_end_date');

      $table->enum('status', $statuses)->default($defaultStatus);
      $table->text('rejection_reason')->nullable();
      $table->timestamp('applicant_confirmation_timestamp')->nullable()->comment('Timestamp for applicant PART 4 confirmation');

      // Fields from Revision 3
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
    Schema::table('loan_applications', function (Blueprint $table) {
      // Add supporting_officer_id to dropForeign if it was added
      $foreignKeys = ['user_id', 'responsible_officer_id', 'supporting_officer_id', 'approved_by', 'rejected_by', 'cancelled_by', 'issued_by', 'returned_by', 'current_approval_officer_id', 'created_by', 'updated_by', 'deleted_by'];
      foreach ($foreignKeys as $key) {
        if (Schema::hasColumn('loan_applications', $key)) {
          try {
            $table->dropForeign([$key]);
          } catch (\Exception $e) {
            Log::warning("Failed to drop FK {$key} on loan_applications: " . $e->getMessage());
          }
        }
      }
    });
    Schema::dropIfExists('loan_applications');
  }
};
