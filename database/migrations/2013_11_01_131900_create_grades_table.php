<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log; // For logging in down method if needed

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('grades', function (Blueprint $table) {
      $table->id();
      $table->string('name')->unique()->comment('e.g., "41", "N19", "JUSA C"');
      $table->integer('level')->nullable()->comment('Numeric level for comparison/sorting');

      $table->foreignId('min_approval_grade_id')
        ->nullable()
        ->constrained('grades') // Self-referencing
        ->onDelete('set null'); // Or cascade if appropriate

      $table->boolean('is_approver_grade')
        ->default(false)
        ->comment('Can users of this grade approve applications?');

      // ADDED these columns to match the model's $fillable
      $table->text('description')->nullable()->comment('Optional description for the grade');
      $table->string('service_scheme')->nullable()->comment('Optional service scheme, e.g., Perkhidmatan Tadbir dan Diplomatik');

      // Blameable columns
      $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
      $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
      $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

      $table->timestamps();
      $table->softDeletes();
    });
  }

  public function down(): void
  {
    Schema::table('grades', function (Blueprint $table) {
      // Drop foreign keys before dropping the table or columns
      // Note: Laravel's default FK name is <table>_<column>_foreign
      $foreignKeys = ['min_approval_grade_id', 'created_by', 'updated_by', 'deleted_by'];
      foreach ($foreignKeys as $fkColumn) {
        // Check if column exists, as FK might not exist if column was never added or dropped previously
        if (Schema::hasColumn('grades', $fkColumn)) {
          $constraintName = 'grades_' . $fkColumn . '_foreign';
          // Check if the constraint actually exists before trying to drop it
          // This requires a more complex check using Schema::getConnection()->getDoctrineSchemaManager()
          // For simplicity in `down`, often just trying to drop is fine if it doesn't halt execution on failure.
          // However, a try-catch is safer.
          try {
            // Check if the foreign key exists (this check is a bit simplified)
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $foreignKeysList = $sm->listTableForeignKeys('grades');
            $fkExists = false;
            foreach ($foreignKeysList as $fk) {
              if (in_array($fkColumn, $fk->getLocalColumns())) {
                $fkExists = true;
                break;
              }
            }
            if ($fkExists) {
              $table->dropForeign([$fkColumn]);
            }
          } catch (\Exception $e) {
            Log::warning("Could not drop foreign key for '{$fkColumn}' on 'grades' table: " . $e->getMessage());
          }
        }
      }
    });
    Schema::dropIfExists('grades');
  }
};
