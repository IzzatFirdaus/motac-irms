<?php

// Remove: use App\Models\LoanTransactionItem; // Not needed for hardcoded enums
use App\Models\Equipment; // For condition statuses
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Define ENUM values directly in the migration
        // Based on LoanTransactionItem model constants from Revision 3
        $itemStatuses = [
            'issued', 'returned_pending_inspection', 'returned_good',
            'returned_minor_damage', 'returned_major_damage',
            'reported_lost', 'unserviceable_on_return',
        ];
        $defaultStatus = 'issued'; // Or another suitable default from the list

        // Condition statuses should match keys from Equipment::$CONDITION_STATUSES_LABELS
        $conditionStatuses = [
            'new', 'good', 'fair', 'minor_damage',
            'major_damage', 'unserviceable', 'lost',
            // 'as_issued' was in migration default, but not in Equipment model constants.
            // Using Equipment model constants/keys for consistency.
        ];

        Schema::create('loan_transaction_items', function (Blueprint $table) use (
            $itemStatuses,
            $defaultStatus,
            $conditionStatuses
        ): void {
            $table->id();
            $table->foreignId('loan_transaction_id')->constrained('loan_transactions')->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->foreignId('loan_application_item_id')->nullable()->comment('Link back to the requested item in application')->constrained('loan_application_items')->onDelete('set null');

            $table->unsignedInteger('quantity_transacted')->default(1)->comment('Typically 1 for serialized items');
            $table->enum('status', $itemStatuses)->default($defaultStatus)->comment('Status of this item in this transaction');
            $table->enum('condition_on_return', $conditionStatuses)->nullable(); // Values should be keys from Equipment model conditions

            // Fields from Revision 3
            $table->json('accessories_checklist_issue')->nullable();
            $table->json('accessories_checklist_return')->nullable();
            $table->text('item_notes')->nullable();

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
            $foreignKeys = ['loan_transaction_id', 'equipment_id', 'loan_application_item_id', 'created_by', 'updated_by', 'deleted_by'];
            foreach ($foreignKeys as $key) {
                if (Schema::hasColumn('loan_transaction_items', $key)) {
                    try {
                        $table->dropForeign([$key]);
                    } catch (\Exception $e) {
                        Log::warning(sprintf('Failed to drop FK %s on loan_transaction_items: ', $key).$e->getMessage());
                    }
                }
            }
        });
        Schema::dropIfExists('loan_transaction_items');
    }
};
