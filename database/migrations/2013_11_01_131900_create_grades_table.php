<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('grades', function (Blueprint $table) {
      $table->id();
      $table->string('name')->unique(); // e.g., "41", "N19", "JUSA C"
      $table
        ->integer('level')
        ->nullable()
        ->comment('Numeric level for comparison/sorting');

      $table
        ->foreignId('min_approval_grade_id')
        ->nullable()
        ->constrained('grades') // Self-referencing
        ->nullOnDelete();

      $table
        ->boolean('is_approver_grade')
        ->default(false)
        ->comment('Can users of this grade approve applications?');

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
    Schema::table('grades', function (Blueprint $table) {
      if (Schema::hasColumn('grades', 'min_approval_grade_id')) {
        // Check before drop
        $table->dropForeign(['min_approval_grade_id']);
      }
      if (Schema::hasColumn('grades', 'created_by')) {
        $table->dropForeign(['created_by']);
      }
      if (Schema::hasColumn('grades', 'updated_by')) {
        $table->dropForeign(['updated_by']);
      }
      if (Schema::hasColumn('grades', 'deleted_by')) {
        $table->dropForeign(['deleted_by']);
      }
    });
    Schema::dropIfExists('grades');
  }
};
