<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('e.g., "41", "N19", "JUSA C"');
            $table->integer('level')->nullable()->comment('Numeric level for comparison/sorting');

            // MODIFIED: Just create the column now.
            // The foreign key constraint will be added in a separate, later migration
            // after the 'positions' table is guaranteed to exist.
            $table->unsignedBigInteger('position_id')->nullable();

            $table->foreignId('min_approval_grade_id')
                ->nullable()
                ->constrained('grades') // Self-referencing
                ->onDelete('set null');

            $table->boolean('is_approver_grade')
                ->default(false)
                ->comment('Can users of this grade approve applications?');

            $table->text('description')->nullable()->comment('Optional description for the grade');
            $table->string('service_scheme')->nullable()->comment('Optional service scheme, e.g., Perkhidmatan Tadbir dan Diplomatik');

            // Blameable columns
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // The unique constraint must also be moved to the new migration,
            // as it often implicitly relies on the index created by the foreign key.
        });
    }

    public function down(): void
    {
        // ... (The down method can be simplified or left as is, as `dropIfExists` will handle it)
        Schema::dropIfExists('grades');
    }
};
