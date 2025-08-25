<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'positions' table (Jawatan).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique()->comment('Position name');
            $table->text('description')->nullable()->comment('Description');
            $table->boolean('is_active')->default(true)->comment('Active flag');
            $table->foreignId('grade_id')->nullable()->constrained('grades')->onDelete('set null')->comment('FK to grades');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table): void {
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
