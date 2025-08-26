<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for loan_transaction_items table.
 * Stores each item transacted (issued/returned) as part of a loan transaction.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Must match App\Models\LoanTransactionItem::$STATUSES_LABELS keys
        $itemStatuses = [
            'issued',
            'returned', // General returned status (added to match model constant)
            'returned_pending_inspection',
            'returned_good',
            'returned_minor_damage',
            'returned_major_damage',
            'reported_lost',
            'unserviceable_on_return',
        ];
        $defaultStatus = 'issued';

        $conditionStatuses = [
            'new', 'good', 'fair', 'minor_damage',
            'major_damage', 'unserviceable', 'lost',
        ];

        Schema::create('loan_transaction_items', function (Blueprint $table) use (
            $itemStatuses, $defaultStatus, $conditionStatuses
        ): void {
            $table->id();
            $table->foreignId('loan_transaction_id')->constrained('loan_transactions')->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->foreignId('loan_application_item_id')->nullable()->comment('Link to requested item')->constrained('loan_application_items')->nullOnDelete();
            $table->unsignedInteger('quantity_transacted')->default(1)->comment('Usually 1 for serialized items');
            $table->enum('status', $itemStatuses)->default($defaultStatus)->comment('Status of this item in this transaction');
            $table->enum('condition_on_return', $conditionStatuses)->nullable();
            $table->json('accessories_checklist_issue')->nullable();
            $table->json('accessories_checklist_return')->nullable();
            $table->text('item_notes')->nullable();

            // Blameable
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('loan_transaction_items', function (Blueprint $table): void {
            $foreignKeys = [
                'loan_transaction_id', 'equipment_id', 'loan_application_item_id',
                'created_by', 'updated_by', 'deleted_by',
            ];
            foreach ($foreignKeys as $key) {
                if (Schema::hasColumn('loan_transaction_items', $key)) {
                    try {
                        $table->dropForeign([$key]);
                    } catch (\Exception $e) {
                        // ignore
                    }
                }
            }
        });
        Schema::dropIfExists('loan_transaction_items');
    }
};
