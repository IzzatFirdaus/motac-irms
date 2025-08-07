<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for loan_application_items table.
 * Stores requested/approved/issued/returned quantities for each equipment type in a loan application.
 */
return new class extends Migration
{
    public function up(): void
    {
        $itemStatuses = [
            'pending_approval',
            'item_approved',
            'item_rejected',
            'awaiting_issuance',
            'fully_issued',
            'partially_issued',
            'fully_returned',
            'item_cancelled',
        ];
        $defaultStatus = 'pending_approval';

        Schema::create('loan_application_items', function (Blueprint $table) use ($itemStatuses, $defaultStatus): void {
            $table->id();
            $table->foreignId('loan_application_id')->constrained('loan_applications')->cascadeOnDelete();
            $table->foreignId('equipment_id')->nullable()->constrained('equipment')->nullOnDelete()->comment('Links to the equipment catalog');
            $table->string('equipment_type')->comment('e.g., Laptop, Projektor, LCD Monitor');
            $table->unsignedInteger('quantity_requested');
            $table->unsignedInteger('quantity_approved')->nullable();
            $table->unsignedInteger('quantity_issued')->default(0);
            $table->unsignedInteger('quantity_returned')->default(0)->comment('Returned quantity');
            $table->enum('status', $itemStatuses)->default($defaultStatus)->comment('Status of this requested item');
            $table->text('notes')->nullable()->comment('Specific requirements or remarks by applicant');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('loan_application_items', function (Blueprint $table): void {
            $foreignKeysToDrop = ['loan_application_id', 'equipment_id', 'created_by', 'updated_by', 'deleted_by'];
            foreach ($foreignKeysToDrop as $key) {
                if (Schema::hasColumn('loan_application_items', $key)) {
                    try {
                        $table->dropForeign([$key]);
                    } catch (\Exception $e) {
                        Log::warning(sprintf('Could not drop foreign key for %s on loan_application_items table: ', $key).$e->getMessage());
                    }
                }
            }
        });
        Schema::dropIfExists('loan_application_items');
    }
};
