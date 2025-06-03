<?php

use App\Models\Equipment; // For accessing constants
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure the Equipment model and its constants are accessible.
        // It's good practice to define fallbacks if the model/constants might not be available during migration,
        // but for defaults using model constants, the model must be loadable.

        $defaultAssetType = 'other_ict'; // Fallback default
        if (class_exists(Equipment::class) && defined(Equipment::class.'::ASSET_TYPE_OTHER_ICT')) {
            $defaultAssetType = Equipment::ASSET_TYPE_OTHER_ICT;
        }

        $defaultStatus = 'available'; // Fallback default
        if (class_exists(Equipment::class) && defined(Equipment::class.'::STATUS_AVAILABLE')) {
            $defaultStatus = Equipment::STATUS_AVAILABLE;
        }

        $defaultCondition = 'good'; // Fallback default
        if (class_exists(Equipment::class) && defined(Equipment::class.'::CONDITION_GOOD')) {
            $defaultCondition = Equipment::CONDITION_GOOD;
        }

        Schema::create('equipment', function (Blueprint $table) use ($defaultAssetType, $defaultStatus, $defaultCondition) {
            $table->id();

            $table->foreignId('equipment_category_id')->nullable()->constrained('equipment_categories')->onDelete('set null');
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories')->onDelete('set null');

            $table->string('item_code')->nullable()->unique()->comment('Unique internal identifier (from HRMS template)');
            $table->string('tag_id')->nullable()->unique()->comment('MOTAC asset tag / No. Aset (from MOTAC Design)');
            $table->string('serial_number')->nullable()->unique()->comment('Manufacturer Serial Number');

            // Corrected constant name and added fallback/safer constant access
            $table->string('asset_type')->default($defaultAssetType)->comment('Specific type of asset (e.g., laptop, projector - from MOTAC Design)');

            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->text('description')->nullable()->comment('Detailed description of the equipment');

            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry_date')->nullable();

            // Ensured constants are accessed safely
            $table->string('status')->default($defaultStatus)->comment('Operational status (e.g., available, on_loan - from MOTAC Design)');
            $table->string('condition_status')->default($defaultCondition)->comment('Physical condition (e.g., good, fair - from MOTAC Design)');

            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null')->comment('Physical location (from HRMS template)');
            $table->string('current_location')->nullable()->comment('Free-text current location details (from MOTAC Design)');

            $table->text('notes')->nullable();

            $table->string('classification')->nullable()->comment('Broad classification (from HRMS template)');
            $table->string('acquisition_type')->nullable()->comment('How the equipment was acquired (from HRMS template)');
            $table->string('funded_by')->nullable()->comment('e.g., Project Name, Grant ID (from HRMS template)');
            $table->string('supplier_name')->nullable()->comment('Supplier name (from HRMS template)');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null')->comment('Owning or assigned department (from HRMS template)');

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            $table->index('asset_type');
            $table->index('status');
            $table->index('condition_status');
            $table->index('brand');
            $table->index('model');
            $table->index('equipment_category_id');
            $table->index('location_id');
            $table->index('department_id');
            $table->index('classification');
            $table->index('acquisition_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $foreignKeysToDropByColumn = [
                'equipment_category_id', 'sub_category_id', 'location_id', 'department_id',
                'created_by', 'updated_by', 'deleted_by',
            ];
            foreach ($foreignKeysToDropByColumn as $column) {
                if (Schema::hasColumn('equipment', $column)) {
                    try {
                        $table->dropForeign([$column]);
                    } catch (\Exception $e) {
                        Log::warning("Could not drop foreign key for column '{$column}' on 'equipment' table during migration rollback (it might not exist or have a non-conventional name): " . $e->getMessage());
                    }
                }
            }
        });
        Schema::dropIfExists('equipment');
    }
};
