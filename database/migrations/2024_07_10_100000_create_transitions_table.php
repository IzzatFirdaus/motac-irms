<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for the transitions table.
 * Used to log handover/return of equipment to employees.
 */

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transitions', function (Blueprint $table): void {
            $table->id();
            // Foreign keys to equipment and employees, handled conditionally if the referenced table exists
            if (Schema::hasTable('equipment')) {
                $table->foreignId('equipment_id')->constrained('equipment')->onDelete('restrict');
            } else {
                $table->unsignedBigInteger('equipment_id');
                \Illuminate\Support\Facades\Log::warning('transitions table created without equipment_id FK due to missing equipment table.');
            }
            if (Schema::hasTable('employees')) {
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            } else {
                $table->unsignedBigInteger('employee_id');
                \Illuminate\Support\Facades\Log::warning('transitions table created without employee_id FK due to missing employees table.');
            }
            $table->date('handed_date')->nullable()->comment('Date equipment was handed over');
            $table->date('return_date')->nullable()->comment('Date equipment was returned');
            $table->string('center_document_number')->unique()->nullable()->comment('Reference document number');
            $table->string('reason')->nullable()->comment('Reason for handover/return');
            $table->longText('note')->nullable()->comment('Additional notes');
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('transitions', function (Blueprint $table): void {
            if (Schema::hasColumn('transitions', 'equipment_id') && Schema::hasTable('equipment')) {
                $table->dropForeign(['equipment_id']);
            }
            if (Schema::hasColumn('transitions', 'employee_id') && Schema::hasTable('employees')) {
                $table->dropForeign(['employee_id']);
            }
            if (Schema::hasColumn('transitions', 'created_by')) {
                $table->dropForeign(['created_by']);
            }
            if (Schema::hasColumn('transitions', 'updated_by')) {
                $table->dropForeign(['updated_by']);
            }
            if (Schema::hasColumn('transitions', 'deleted_by')) {
                $table->dropForeign(['deleted_by']);
            }
        });
        Schema::dropIfExists('transitions');
    }
};
