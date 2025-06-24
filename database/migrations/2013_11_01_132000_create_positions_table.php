<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique(); // As per Revision 3 and existing migration
            $table->text('description')->nullable(); // As per Revision 3 and existing migration

            // 'vacancies_count' removed as it's not in Revision 3's positions schema
            // 'department_id' removed as it's not in Revision 3's positions schema

            $table->boolean('is_active')->default(true); // Added as per Revision 3 positions schema

            $table->foreignId('grade_id') // As per Revision 3 and existing migration
                ->nullable()
                ->constrained('grades') // Assumes 'grades' table exists
                ->onDelete('set null');

            // Blameable fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table): void {
            // Drop foreign keys first
            if (Schema::hasColumn('positions', 'grade_id')) {
                $table->dropForeign(['grade_id']);
            }

            if (Schema::hasColumn('positions', 'created_by')) {
                $table->dropForeign(['created_by']);
            }

            if (Schema::hasColumn('positions', 'updated_by')) {
                $table->dropForeign(['updated_by']);
            }

            if (Schema::hasColumn('positions', 'deleted_by')) {
                $table->dropForeign(['deleted_by']);
            }
        });
        Schema::dropIfExists('positions');
    }
};
