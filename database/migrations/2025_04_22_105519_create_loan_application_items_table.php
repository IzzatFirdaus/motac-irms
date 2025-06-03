<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log; // For logging

return new class extends Migration
{
  public function up(): void
  {
    // Define ENUM values for status directly in the migration
    $itemStatuses = [
      'pending_approval',
      'item_approved',
      'item_rejected',
      'awaiting_issuance',
      'fully_issued',
      'partially_issued',
      'fully_returned',
      'item_cancelled',
    ];
    $defaultStatus = 'pending_approval';

    Schema::create('loan_application_items', function (Blueprint $table) use ($itemStatuses, $defaultStatus) {
      $table->id();
      $table->foreignId('loan_application_id')->constrained('loan_applications')->cascadeOnDelete();

      $table->foreignId('equipment_id')
            ->nullable()
            ->constrained('equipment')
            ->nullOnDelete()
            ->comment('Links to the equipment catalog');

      $table->string('equipment_type')->comment('e.g., Laptop, Projektor, LCD Monitor');
      $table->unsignedInteger('quantity_requested');
      $table->unsignedInteger('quantity_approved')->nullable();
      $table->unsignedInteger('quantity_issued')->default(0);
      // Revised line: Removed ->after('quantity_issued')
      $table->unsignedInteger('quantity_returned')->default(0)->comment('Added as per System Design');

      $table->enum('status', $itemStatuses)->default($defaultStatus)->comment('Status of this specific requested item');
      $table->text('notes')->nullable()->comment('Specific requirements or remarks by applicant');

      $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
      $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
      $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

      $table->timestamps();
      $table->softDeletes();
    });
  }

  public function down(): void
  {
    Schema::table('loan_application_items', function (Blueprint $table) {
      $foreignKeysToDrop = ['loan_application_id', 'equipment_id', 'created_by', 'updated_by', 'deleted_by']; // Added 'equipment_id'
      foreach ($foreignKeysToDrop as $key) {
        if (Schema::hasColumn('loan_application_items', $key)) {
          try {
            // Check if the foreign key exists before trying to drop it
            // Note: Laravel's default foreign key naming convention is table_column_foreign
            // However, constrained() might use a different one if the column name is complex.
            // A more robust check might involve inspecting the schema manager for existing foreign keys.
            // For simplicity, we'll assume the default naming or that dropForeign handles non-existent keys gracefully in newer Laravel.
            $table->dropForeign([$key]);
          } catch (\Exception $e) {
            Log::warning("Could not drop foreign key for {$key} on loan_application_items table: " . $e->getMessage());
          }
        }
      }
    });
    Schema::dropIfExists('loan_application_items');
  }
};
