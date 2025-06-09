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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            // Aligns with MOTAC design: Bahagian/Unit (Ibu Pejabat / Negeri)
            $table->enum('branch_type', ['state', 'headquarters'])->nullable()->comment('Corresponds to MOTAC Negeri/Bahagian distinction');
            $table->string('code')->nullable()->unique()->comment('Optional department code');

            $table->boolean('is_active')->default(true);

            $table->foreignId('head_of_department_id')->nullable()->constrained('users')->onDelete('set null');

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
        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'head_of_department_id')) {
                $table->dropForeign(['head_of_department_id']);
            }
            if (Schema::hasColumn('departments', 'created_by')) {
                $table->dropForeign(['created_by']);
            }
            if (Schema::hasColumn('departments', 'updated_by')) {
                $table->dropForeign(['updated_by']);
            }
            if (Schema::hasColumn('departments', 'deleted_by')) {
                $table->dropForeign(['deleted_by']);
            }
        });
        Schema::dropIfExists('departments');
    }
};
