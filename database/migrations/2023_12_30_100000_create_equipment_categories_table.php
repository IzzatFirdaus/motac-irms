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
        Schema::create('equipment_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true); // Added field as per Revision 3 design
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
        Schema::table('equipment_categories', function (Blueprint $table) {
            // Drop foreign keys first
            if (Schema::hasColumn('equipment_categories', 'created_by')) {
                $table->dropForeign(['created_by']);
            }
            if (Schema::hasColumn('equipment_categories', 'updated_by')) {
                $table->dropForeign(['updated_by']);
            }
            if (Schema::hasColumn('equipment_categories', 'deleted_by')) {
                $table->dropForeign(['deleted_by']);
            }
            // It's good practice to also drop columns if they were added in `up()`
            // However, the main goal of `down()` is to reverse `up()`.
            // If `is_active` was added, strictly it should be dropped here.
            // But Schema::dropIfExists('equipment_categories') at the end handles full table removal.
        });
        Schema::dropIfExists('equipment_categories');
    }
};
