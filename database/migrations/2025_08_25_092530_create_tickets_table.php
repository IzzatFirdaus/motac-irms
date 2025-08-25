<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'tickets' table for the legacy ticket system.
 * Aligned with App\Models\Ticket: includes blameable fields and soft deletes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table): void {
            $table->id();
            $table->string('subject');
            $table->text('description')->nullable();
            $table->string('status')->default('open')->index(); // open, in_progress, resolved, closed

            // Reporter / ownership
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('Reporter / applicant');

            // Classification and routing
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            // category_id and priority_id may reference tables created after this migration file
            // create as unsignedBigInteger and add FK conditionally after table creation
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->unsignedBigInteger('priority_id')->nullable()->index();

            // Workflow tracking
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null')->comment('Assigned agent');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null')->comment('User who resolved the ticket');
            $table->timestamp('resolved_at')->nullable()->comment('When the ticket was resolved/closed');

            // SLA / resolution
            $table->timestamp('sla_due_at')->nullable()->comment('SLA due date');
            $table->text('resolution_notes')->nullable();

            // Blameable + soft delete
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->softDeletes();
            $table->timestamps();

            // Useful indexes
            // Note: foreignId() and ->index() on the column already create single-column
            // indexes. Avoid duplicating them here to prevent duplicate key errors.
            // Add composite or additional indexes here if needed in future.
        });

        // Add foreign keys for category_id and priority_id only if those tables already exist
        if (Schema::hasTable('ticket_categories')) {
            Schema::table('tickets', function (Blueprint $table): void {
                try {
                    $table->foreign('category_id')->references('id')->on('ticket_categories')->onDelete('set null');
                } catch (\Throwable $e) {
                    // ignore if cannot create FK now
                }
            });
        }

        if (Schema::hasTable('ticket_priorities')) {
            Schema::table('tickets', function (Blueprint $table): void {
                try {
                    $table->foreign('priority_id')->references('id')->on('ticket_priorities')->onDelete('set null');
                } catch (\Throwable $e) {
                    // ignore if cannot create FK now
                }
            });
        }
    }

    public function down(): void
    {
        // Drop foreign keys defensively before dropping table
        Schema::table('tickets', function (Blueprint $table): void {
            $fks = [
                'user_id', 'department_id', 'category_id', 'priority_id',
                'assigned_to', 'resolved_by', 'created_by', 'updated_by', 'deleted_by',
            ];
            foreach ($fks as $fk) {
                if (Schema::hasColumn('tickets', $fk)) {
                    try {
                        $table->dropForeign([$fk]);
                    } catch (\Throwable $e) {
                        // ignore and continue - defensive rollback
                    }
                }
            }
        });

        Schema::dropIfExists('tickets');
    }
};
