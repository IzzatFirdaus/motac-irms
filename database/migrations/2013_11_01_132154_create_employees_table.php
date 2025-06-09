<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema; // For down method logging

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            // Removed: $table->foreignId('contract_id')->constrained();
            $table->string('first_name');
            $table->string('father_name');
            $table->string('last_name');
            $table->string('mother_name');
            $table->string('birth_and_place');
            $table->string('national_number')->unique();
            $table->string('mobile_number')->unique();
            $table->string('degree');
            $table->boolean('gender'); // Consider 'male'/'female' enum or string for clarity if needed
            $table->string('address');
            $table->longText('notes')->nullable();
            $table->integer('max_leave_allowed')->length(2)->default(0); // Note: length() is for some DBs, not always effective for integer
            $table->time('delay_counter')->default('00:00:00'); // Removed .00 as time type might not support it directly
            $table->time('hourly_counter')->default('00:00:00'); // Removed .00
            $table->boolean('is_active')->default(true); // Changed default to boolean true
            $table->string('profile_photo_path')->nullable(); // Made nullable as path might not always exist initially

            // Revised Blameable fields
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
        Schema::table('employees', function (Blueprint $table) {
            // Drop foreign keys for blameable fields if they were added
            $foreignKeysToDrop = ['created_by', 'updated_by', 'deleted_by'];
            foreach ($foreignKeysToDrop as $key) {
                if (Schema::hasColumn('employees', $key)) {
                    try {
                        // Laravel generates FK names like employees_created_by_foreign
                        $foreignKeyName = 'employees_'.$key.'_foreign';
                        // Check if constraint exists before dropping (more robust for some DBs)
                        // This part can be tricky as actual FK name might vary based on Laravel version/DB
                        // For simplicity, direct dropForeign is often used with try-catch.
                        $table->dropForeign([$key]);
                    } catch (\Exception $e) {
                        Log::warning("Could not drop foreign key for {$key} on employees table: ".$e->getMessage());
                    }
                }
            }
        });
        Schema::dropIfExists('employees');
    }
};
