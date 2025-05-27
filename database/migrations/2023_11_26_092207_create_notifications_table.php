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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable'); // notifiable_id, notifiable_type
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps(); // created_at, updated_at

            // Custom additions by user: Audit columns and Soft Deletes
            $table
                ->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table
                ->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table
                ->foreignId('deleted_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Drop custom audit foreign keys if they exist
            if (Schema::hasColumn('notifications', 'created_by')) {
                $table->dropForeign(['created_by']);
            }
            if (Schema::hasColumn('notifications', 'updated_by')) {
                $table->dropForeign(['updated_by']);
            }
            if (Schema::hasColumn('notifications', 'deleted_by')) {
                $table->dropForeign(['deleted_by']);
            }
            // SoftDeletes column `deleted_at` is dropped by dropIfExists
        });
        Schema::dropIfExists('notifications');
    }
};
