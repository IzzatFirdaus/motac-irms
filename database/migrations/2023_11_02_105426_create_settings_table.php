<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('settings', function (Blueprint $table) {
      $table->id();
      // Example settings - add more as needed by your application
      $table->string('site_name')->default('MOTAC Resource Management');
      $table->string('site_logo_path')->nullable();
      $table->string('default_notification_email_from')->nullable();
      $table->string('default_notification_email_name')->nullable();

      $table->string('sms_api_sender')->nullable();
      $table->string('sms_api_username')->nullable();
      $table->string('sms_api_password')->nullable(); // Consider encrypting if storing real credentials
      $table->text('terms_and_conditions_loan')->nullable();
      $table->text('terms_and_conditions_email')->nullable();

      $table
        ->foreignId('created_by')
        ->nullable()
        ->constrained('users')
        ->onDelete('set null');
      $table
        ->foreignId('updated_by')
        ->nullable()
        ->constrained('users')
        ->onDelete('set null');
      $table
        ->foreignId('deleted_by')
        ->nullable()
        ->constrained('users')
        ->onDelete('set null');

      $table->timestamps();
      $table->softDeletes();
    });
  }

  public function down(): void
  {
    Schema::table('settings', function (Blueprint $table) {
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
