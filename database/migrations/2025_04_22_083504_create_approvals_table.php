<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'approvals' table for workflow approvals (e.g., loan, etc.).
 * Uses polymorphic 'approvable' relationship for reusability.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table): void {
            $table->id();
            $table->morphs('approvable'); // approvable_type, approvable_id (polymorphic)
            $table->foreignId('officer_id')->constrained('users')->onDelete('cascade')->comment('Officer responsible for this approval');
            $table->string('stage')->nullable()->index()->comment('Approval stage: e.g., support_review, hod_review');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->text('comments')->nullable();
            $table->timestamp('approval_timestamp')->nullable()->comment('Timestamp when approval was made');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('approvals', function (Blueprint $table): void {
            if (Schema::hasColumn('approvals', 'officer_id')) {
                $table->dropForeign(['officer_id']);
            }
            if (Schema::hasColumn('approvals', 'created_by')) {
                $table->dropForeign(['created_by']);
            }
            if (Schema::hasColumn('approvals', 'updated_by')) {
                $table->dropForeign(['updated_by']);
            }
            if (Schema::hasColumn('approvals', 'deleted_by')) {
                $table->dropForeign(['deleted_by']);
            }
        });
        Schema::dropIfExists('approvals');
    }
};
