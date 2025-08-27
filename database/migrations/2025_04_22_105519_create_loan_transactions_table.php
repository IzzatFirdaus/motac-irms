<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for loan_transactions table.
 * Stores issue/return transactions for loan applications.
 */

return new class extends Migration
{
    public function up(): void
    {
        $transactionTypes = ['issue', 'return'];
        $defaultType      = 'issue';

        $transactionStatuses = [
            'pending',
            'issued',
            'returned_pending_inspection',
            'returned_good',
            'returned_damaged',
            'items_reported_lost',
            'completed',
            'cancelled',
            'overdue',
            'returned_with_loss',
            'returned_with_damage_and_loss',
            'returned',
            'partially_returned',
        ];
        $defaultStatus = 'pending';

        Schema::create('loan_transactions', function (Blueprint $table) use (
            $transactionTypes,
            $defaultType,
            $transactionStatuses,
            $defaultStatus
        ): void {
            $table->id();
            $table->foreignId('loan_application_id')->constrained('loan_applications')->cascadeOnDelete();
            $table->enum('type', $transactionTypes)->default($defaultType);
            $table->timestamp('transaction_date')->useCurrent();
            $table->foreignId('issuing_officer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('receiving_officer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('accessories_checklist_on_issue')->nullable();
            $table->text('issue_notes')->nullable();
            $table->timestamp('issue_timestamp')->nullable()->comment('Actual moment of physical issue');
            $table->foreignId('returning_officer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('return_accepting_officer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('accessories_checklist_on_return')->nullable();
            $table->text('return_notes')->nullable();
            $table->timestamp('return_timestamp')->nullable()->comment('Actual moment of physical return');
            $table->foreignId('related_transaction_id')->nullable()->constrained('loan_transactions')->onDelete('set null');
            $table->date('due_date')->nullable()->comment('Applicable for issue transactions');
            $table->enum('status', $transactionStatuses)->default($defaultStatus)->comment('Status of the transaction itself');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('loan_transactions', function (Blueprint $table): void {
            $foreignKeys = [
                'loan_application_id', 'issuing_officer_id', 'receiving_officer_id',
                'returning_officer_id', 'return_accepting_officer_id', 'related_transaction_id',
                'created_by', 'updated_by', 'deleted_by',
            ];
            foreach ($foreignKeys as $key) {
                if (Schema::hasColumn('loan_transactions', $key)) {
                    try {
                        $table->dropForeign([$key]);
                    } catch (\Exception $e) {
                        Log::warning(sprintf('Failed to drop FK %s on loan_transactions: ', $key) . $e->getMessage());
                    }
                }
            }
        });
        Schema::dropIfExists('loan_transactions');
    }
};
