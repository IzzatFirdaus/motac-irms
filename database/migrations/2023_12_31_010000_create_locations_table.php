<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for the locations table.
 * Represents physical offices, branches, or asset locations.
 */

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique()->comment('Location name');
            $table->text('description')->nullable()->comment('Optional description');
            $table->string('address')->nullable()->comment('Street or premise address');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->boolean('is_active')->default(true); // Used for filtering only active locations
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table): void {
            // Drop foreign keys for blameable fields before dropping table
            if (Schema::hasColumn('locations', 'created_by')) {
                $table->dropForeign(['created_by']);
            }
            if (Schema::hasColumn('locations', 'updated_by')) {
                $table->dropForeign(['updated_by']);
            }
            if (Schema::hasColumn('locations', 'deleted_by')) {
                $table->dropForeign(['deleted_by']);
            }
        });
        Schema::dropIfExists('locations');
    }
};
