<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for notifications table.
 * Stores all notifications for notifiable models with audit fields and soft deletes.
 */

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table): void {
            if (Schema::hasColumn('notifications', 'created_by')) {
                $table->dropForeign(['created_by']);
            }
            if (Schema::hasColumn('notifications', 'updated_by')) {
                $table->dropForeign(['updated_by']);
            }
            if (Schema::hasColumn('notifications', 'deleted_by')) {
                $table->dropForeign(['deleted_by']);
            }
        });
        Schema::dropIfExists('notifications');
    }
};
