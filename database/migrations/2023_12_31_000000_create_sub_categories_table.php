<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('sub_categories', function (Blueprint $table) {
      $table->id();
      // Ensure 'equipment_categories' table exists for this constraint
      if (Schema::hasTable('equipment_categories')) {
        $table->foreignId('equipment_category_id')->constrained('equipment_categories')->onDelete('restrict');
      } else {
        $table->unsignedBigInteger('equipment_category_id');
        \Illuminate\Support\Facades\Log::warning('sub_categories table created without equipment_category_id foreign key due to missing equipment_categories table.');
      }

      $table->string('name');
      $table->text('description')->nullable();
      $table->boolean('is_active')->default(true);

      $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
      $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
      $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

      $table->timestamps();
      $table->softDeletes();
    });
  }

  public function down(): void
  {
    Schema::table('sub_categories', function (Blueprint $table) {
      if (Schema::hasColumn('sub_categories', 'equipment_category_id') && Schema::hasTable('equipment_categories')) {
        $table->dropForeign(['equipment_category_id']);
      }
      if (Schema::hasColumn('sub_categories', 'created_by')) {
        $table->dropForeign(['created_by']);
      }
      if (Schema::hasColumn('sub_categories', 'updated_by')) {
        $table->dropForeign(['updated_by']);
      }
      if (Schema::hasColumn('sub_categories', 'deleted_by')) {
        $table->dropForeign(['deleted_by']);
      }
    });
    Schema::dropIfExists('sub_categories');
  }
};
