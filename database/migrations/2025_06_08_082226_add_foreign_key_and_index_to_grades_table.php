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
        Schema::table('grades', function (Blueprint $table): void {
            // Now that the 'positions' table exists, we can add the foreign key.
            $table->foreign('position_id')
                ->references('id')
                ->on('positions')
                ->onDelete('set null');

            // We also add the unique constraint here.
            $table->unique(['name', 'position_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table): void {
            // Drop in reverse order of creation
            $table->dropUnique(['name', 'position_id']);
            $table->dropForeign(['position_id']);
        });
    }
};
