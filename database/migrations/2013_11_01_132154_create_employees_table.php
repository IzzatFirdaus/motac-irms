<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'employees' table.
 */

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table): void {
            $table->id();
            $table->string('first_name');
            $table->string('father_name');
            $table->string('last_name');
            $table->string('mother_name');
            $table->string('birth_and_place');
            $table->string('national_number')->unique();
            $table->string('mobile_number')->unique();
            $table->string('degree');
            $table->boolean('gender');
            $table->string('address');
            $table->longText('notes')->nullable();
            $table->integer('max_leave_allowed')->default(0);
            $table->time('delay_counter')->default('00:00:00');
            $table->time('hourly_counter')->default('00:00:00');
            $table->boolean('is_active')->default(true);
            $table->string('profile_photo_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            $foreignKeysToDrop = ['created_by', 'updated_by', 'deleted_by'];
            foreach ($foreignKeysToDrop as $key) {
                if (Schema::hasColumn('employees', $key)) {
                    try {
                        $table->dropForeign([$key]);
                    } catch (\Exception $e) {
                        Log::warning(sprintf('Could not drop foreign key for %s on employees table: ', $key) . $e->getMessage());
                    }
                }
            }
        });
        Schema::dropIfExists('employees');
    }
};
