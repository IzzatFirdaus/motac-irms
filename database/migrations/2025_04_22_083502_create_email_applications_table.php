<?php

use App\Models\EmailApplication;
// Not directly used in schema but good for context
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $applicationStatuses = [];
        if (method_exists(EmailApplication::class, 'getStatuses')) { // Ensure model method exists
            $applicationStatuses = EmailApplication::getStatuses();
        } else {
            // Fallback if method doesn't exist, though it should be defined in the model.
            Log::error('EmailApplication::getStatuses() method not found. Using fallback statuses for migration. Please define this method in the model.');
            $applicationStatuses = ['draft', 'pending_support', 'pending_admin', 'approved', 'rejected', 'processing', 'provision_failed', 'completed', 'cancelled'];
        }

        $defaultStatus = '';
        if (defined(EmailApplication::class.'::STATUS_DRAFT')) {
            $defaultStatus = EmailApplication::STATUS_DRAFT;
        } else {
            Log::error('EmailApplication::STATUS_DRAFT constant not found. Using fallback default status for migration.');
            $defaultStatus = 'draft'; // Fallback
        }

        Schema::create('email_applications', function (Blueprint $table) use (
            $applicationStatuses,
            $defaultStatus
        ): void {
            $table->id();
            $table->foreignId('user_id')->comment('Applicant User ID')->constrained('users')->onDelete('cascade');

            // Applicant Snapshot fields (as per your provided migration)
            $table->string('applicant_title')->nullable()->comment("Snapshot: Applicant's title (e.g., Encik, Puan)");
            $table->string('applicant_name')->nullable()->comment("Snapshot: Applicant's full name");
            $table->string('applicant_identification_number')->nullable()->comment("Snapshot: Applicant's NRIC");
            $table->string('applicant_passport_number')->nullable()->comment("Snapshot: Applicant's Passport No");
            $table->string('applicant_jawatan_gred')->nullable()->comment("Snapshot: Applicant's Jawatan & Gred text");
            $table->string('applicant_bahagian_unit')->nullable()->comment("Snapshot: Applicant's Bahagian/Unit text");
            $table->string('applicant_level_aras')->nullable()->comment("Snapshot: Applicant's Aras (Level) text");
            $table->string('applicant_mobile_number')->nullable()->comment("Snapshot: Applicant's mobile number");
            $table->string('applicant_personal_email')->nullable()->comment("Snapshot: Applicant's personal email");

            $table->string('service_status')->nullable()->comment('Key for Taraf Perkhidmatan, from User model options');
            $table->string('appointment_type')->nullable()->comment('Key for Pelantikan, from User model options');

            $table->string('previous_department_name')->nullable()->comment('For Kenaikan Pangkat/Pertukaran');
            $table->string('previous_department_email')->nullable()->comment('For Kenaikan Pangkat/Pertukaran');
            $table->date('service_start_date')->nullable()->comment('For contract/intern');
            $table->date('service_end_date')->nullable()->comment('For contract/intern');

            $table->text('purpose')->nullable()->comment('Purpose of application / Notes (Tujuan/Catatan)'); // Renamed from application_reason_notes
            $table->string('proposed_email')->nullable()->comment("Applicant's proposed email or user ID"); // Removed unique() here as per your provided migration. Add if needed.

            $table->string('group_email')->nullable()->comment('Requested group email address');
            $table->string('group_admin_name')->nullable()->comment('Name of Admin/EO/CC for group email'); // Renamed from contact_person_name
            $table->string('group_admin_email')->nullable()->comment('Email of Admin/EO/CC for group email'); // Renamed from contact_person_email

            $table->foreignId('supporting_officer_id')->nullable()->comment('FK to users table if system user')->constrained('users')->onDelete('set null');
            $table->string('supporting_officer_name')->nullable()->comment('Manually entered supporting officer name');
            $table->string('supporting_officer_grade')->nullable()->comment('Manually entered supporting officer grade');
            $table->string('supporting_officer_email')->nullable()->comment('Manually entered supporting officer email');

            $table->enum('status', $applicationStatuses)->default($defaultStatus);

            $table->boolean('cert_info_is_true')->default(false)->comment('Semua maklumat adalah BENAR');
            $table->boolean('cert_data_usage_agreed')->default(false)->comment('BERSETUJU maklumat diguna pakai oleh BPM');
            $table->boolean('cert_email_responsibility_agreed')->default(false)->comment('BERSETUJU bertanggungjawab ke atas e-mel');
            $table->timestamp('certification_timestamp')->nullable();
            $table->timestamp('submitted_at')->nullable();

            $table->text('rejection_reason')->nullable();
            $table->string('final_assigned_email')->nullable()->unique();
            $table->string('final_assigned_user_id')->nullable()->unique();

            $table->foreignId('processed_by')->nullable()->comment('FK to users, IT Admin who processed')->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('email_applications', function (Blueprint $table): void {
            $foreignKeysToDrop = ['user_id', 'supporting_officer_id', 'processed_by', 'created_by', 'updated_by', 'deleted_by'];
            foreach ($foreignKeysToDrop as $key) {
                if (Schema::hasColumn('email_applications', $key)) {
                    try {
                        $table->dropForeign([$key]);
                    } catch (\Exception $e) {
                        Log::warning(sprintf('Could not drop foreign key for %s on email_applications table: %s', $key, $e->getMessage()));
                    }
                }
            }
        });
        Schema::dropIfExists('email_applications');
    }
};
