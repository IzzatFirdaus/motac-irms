<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'grades' table for job grades.
 */

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->comment('Grade name, e.g., "41", "N19", "JUSA C"');
            $table->integer('level')->nullable()->comment('Numeric level for sorting/comparison');
            $table->unsignedBigInteger('position_id')->nullable()->comment('FK to positions (added as FK later)');
            $table->foreignId('min_approval_grade_id')->nullable()->constrained('grades')->onDelete('set null')->comment('For approval min grade');
            $table->boolean('is_approver_grade')->default(false)->comment('Can users of this grade approve applications?');
            $table->text('description')->nullable();
            $table->string('service_scheme')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
