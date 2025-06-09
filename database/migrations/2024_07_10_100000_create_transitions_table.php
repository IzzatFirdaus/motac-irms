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
        Schema::create('transitions', function (Blueprint $table) {
            $table->id();
            // Ensure 'equipment' and 'employees' tables exist for these constraints
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

            $table->date('handed_date')->nullable();
            $table->date('return_date')->nullable();
            $table->string('center_document_number')->unique()->nullable();
            $table->string('reason')->nullable();
            $table->longText('note')->nullable();
            $table->timestamps();

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transitions', function (Blueprint $table) {
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
