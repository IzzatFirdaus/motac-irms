<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'settings' table for application-wide settings.
 */

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table): void {
            $table->id();
            $table->string('site_name')->default('MOTAC Resource Management');
            $table->string('site_logo_path')->nullable();
            $table->string('default_notification_email_from')->nullable();
            $table->string('default_notification_email_name')->nullable();
            $table->string('sms_api_sender')->nullable();
            $table->string('sms_api_username')->nullable();
            $table->string('sms_api_password')->nullable();
            $table->text('terms_and_conditions_loan')->nullable();
            $table->text('terms_and_conditions_email')->nullable();
            $table->string('application_name')->default('MOTAC Integrated Resource Management System')->comment('Official name of the application');
            $table->string('default_system_email')->nullable()->comment('Default email for system-originated non-notification emails');
            $table->unsignedInteger('default_loan_period_days')->default(7)->comment('Default loan period in days');
            $table->unsignedInteger('max_loan_items_per_application')->default(5)->comment('Max items per single loan application');
            $table->string('contact_us_email')->nullable()->comment('Email for contact us inquiries');
            $table->boolean('system_maintenance_mode')->default(false);
            $table->text('system_maintenance_message')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table): void {
            if (Schema::hasColumn('settings', 'created_by')) {
                $table->dropForeign(['created_by']);
            }
            if (Schema::hasColumn('settings', 'updated_by')) {
                $table->dropForeign(['updated_by']);
            }
            if (Schema::hasColumn('settings', 'deleted_by')) {
                $table->dropForeign(['deleted_by']);
            }
        });
        Schema::dropIfExists('settings');
    }
};
