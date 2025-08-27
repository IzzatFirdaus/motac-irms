<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Adds MOTAC-specific columns to the users table, including personal, organizational, and employment details.
 */

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            // Personal and organizational details
            $table->string('title')->nullable()->after('name')->comment('e.g., Encik, Puan, Dr.');
            $table->string('identification_number')->unique()->nullable()->after('title')->comment('NRIC');
            $table->string('passport_number')->unique()->nullable()->after('identification_number');
            $table->foreignId('department_id')->nullable()->after('passport_number')->constrained('departments')->onDelete('set null')->comment('Refers to MOTAC Negeri/Bahagian/Unit');
            $table->foreignId('position_id')->nullable()->after('department_id')->constrained('positions')->onDelete('set null')->comment('Refers to Jawatan');
            $table->foreignId('grade_id')->nullable()->after('position_id')->constrained('grades')->onDelete('set null')->comment('Refers to Gred');
            $table->string('level')->nullable()->after('grade_id')->comment('For "Aras" or floor level, as string');
            $table->string('mobile_number')->nullable()->after('level');
            $table->string('personal_email')->unique()->nullable()->after('mobile_number')->comment('If distinct from login email');
            $table->string('motac_email')->unique()->nullable()->after('personal_email');
            $table->string('user_id_assigned')->unique()->nullable()->after('motac_email')->comment('Assigned User ID if different from email');

            // Employment status enums (service_status, appointment_type)
            $serviceStatusEnumValues = [
                User::SERVICE_STATUS_TETAP,
                User::SERVICE_STATUS_KONTRAK_MYSTEP,
                User::SERVICE_STATUS_PELAJAR_INDUSTRI,
                User::SERVICE_STATUS_OTHER_AGENCY,
            ];
            $serviceStatusEnumValues = array_unique(array_filter($serviceStatusEnumValues, fn ($value): bool => ! is_null($value)));
            if ($serviceStatusEnumValues === []) {
                Log::error('Service status enum values could not be determined from User model constants. Using hardcoded fallback in migration. PLEASE CHECK User.php CONSTANTS.');
                $serviceStatusEnumValues = ['1', '2', '3', '4'];
            }
            $table->enum('service_status', $serviceStatusEnumValues)->nullable()->after('user_id_assigned')->comment('Taraf Perkhidmatan. Keys defined in User model.');

            $appointmentTypeEnumValues = [
                User::APPOINTMENT_TYPE_BAHARU,
                User::APPOINTMENT_TYPE_KENAIKAN_PANGKAT_PERTUKARAN,
                User::APPOINTMENT_TYPE_LAIN_LAIN,
            ];
            $appointmentTypeEnumValues = array_unique(array_filter($appointmentTypeEnumValues, fn ($value): bool => ! is_null($value)));
            if ($appointmentTypeEnumValues === []) {
                Log::error('Appointment type enum values could not be determined from User model constants. Using hardcoded fallback in migration. PLEASE CHECK User.php CONSTANTS.');
                $appointmentTypeEnumValues = ['1', '2', '3'];
            }
            $table->enum('appointment_type', $appointmentTypeEnumValues)->nullable()->after('service_status')->comment('Pelantikan. Keys defined in User model.');

            // Previous department details
            $table->string('previous_department_name')->nullable()->after('appointment_type');
            $table->string('previous_department_email')->nullable()->after('previous_department_name');

            // User status
            $statusEnumValues = [
                User::STATUS_ACTIVE,
                User::STATUS_INACTIVE,
                User::STATUS_PENDING,
            ];
            $statusEnumValues = array_unique(array_filter($statusEnumValues, fn ($value): bool => ! is_null($value)));
            if (count($statusEnumValues) < 2) {
                Log::error('Status enum values could not be determined reliably from User model constants. Using hardcoded fallback in migration. PLEASE CHECK User.php CONSTANTS.');
                $statusEnumValues = ['active', 'inactive', 'pending'];
            }
            $defaultUserStatus = defined(User::class . '::STATUS_ACTIVE') ? User::STATUS_ACTIVE : $statusEnumValues[0];
            $table->enum('status', $statusEnumValues)->default($defaultUserStatus)->after('previous_department_email');

            // Boolean flags (prefer Spatie roles, but included for legacy)
            $table->boolean('is_admin')->default(false)->after('status')->comment('Consider using Spatie roles exclusively.');
            $table->boolean('is_bpm_staff')->default(false)->after('is_admin')->comment('Consider using Spatie roles exclusively.');

            $table->string('profile_photo_path', 2048)->nullable()->after('is_bpm_staff');

            // Optional foreign key to employees table
            if (Schema::hasTable('employees')) {
                $table->foreignId('employee_id')->nullable()->after('profile_photo_path')->constrained('employees')->onDelete('set null');
            } else {
                $table->unsignedBigInteger('employee_id')->nullable()->after('profile_photo_path')->comment('Link to employees table (constraint might be added later)');
            }

            // Blameable fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $foreignKeysToDrop = [
                'department_id', 'position_id', 'grade_id',
                'created_by', 'updated_by', 'deleted_by',
            ];

            if (Schema::hasColumn('users', 'employee_id')) {
                try {
                    if (collect(Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys('users'))->pluck('name')->contains('users_employee_id_foreign')) {
                        $table->dropForeign('users_employee_id_foreign');
                    }
                } catch (\Exception $e) {
                    Log::warning('Could not automatically drop foreign key for employee_id on users table: ' . $e->getMessage() . '. Manual check might be needed if schema differs.');
                }
            }

            foreach ($foreignKeysToDrop as $fkColumn) {
                if (Schema::hasColumn('users', $fkColumn)) {
                    try {
                        $foreignKeyName = 'users_' . $fkColumn . '_foreign';
                        if (collect(Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys('users'))->pluck('name')->contains($foreignKeyName)) {
                            $table->dropForeign($foreignKeyName);
                        }
                    } catch (\Exception $e) {
                        Log::warning(sprintf('Could not drop foreign key for %s on users table: ', $fkColumn) . $e->getMessage());
                    }
                }
            }

            $columnsToDrop = [
                'title', 'identification_number', 'passport_number',
                'department_id', 'position_id', 'grade_id', 'level',
                'mobile_number', 'personal_email', 'motac_email', 'user_id_assigned',
                'service_status', 'appointment_type',
                'previous_department_name', 'previous_department_email',
                'status', 'is_admin', 'is_bpm_staff', 'profile_photo_path', 'employee_id',
                'created_by', 'updated_by', 'deleted_by',
            ];

            $existingColumns = Schema::getColumnListing('users');
            foreach ($columnsToDrop as $column) {
                if (in_array($column, $existingColumns) && Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (in_array('deleted_at', $existingColumns) && Schema::hasColumn('users', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
