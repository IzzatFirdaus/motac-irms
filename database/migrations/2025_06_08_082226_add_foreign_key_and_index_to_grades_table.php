<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds position_id foreign key and unique index to the grades table.
 */

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table): void {
            $table->foreign('position_id')
                ->references('id')
                ->on('positions')
                ->onDelete('set null');
            $table->unique(['name', 'position_id']);
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table): void {
            $table->dropUnique(['name', 'position_id']);
            $table->dropForeign(['position_id']);
        });
    }
};
