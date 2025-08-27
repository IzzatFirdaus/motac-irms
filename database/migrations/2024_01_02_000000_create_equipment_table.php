<?php

use App\Models\Equipment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the 'equipment' table for ICT asset inventory.
 */

return new class extends Migration
{
    public function up(): void
    {
        $defaultAssetType = class_exists(Equipment::class) && defined(Equipment::class . '::ASSET_TYPE_OTHER_ICT') ? Equipment::ASSET_TYPE_OTHER_ICT : 'other_ict';
        $defaultStatus    = class_exists(Equipment::class)    && defined(Equipment::class . '::STATUS_AVAILABLE') ? Equipment::STATUS_AVAILABLE : 'available';
        $defaultCondition = class_exists(Equipment::class) && defined(Equipment::class . '::CONDITION_GOOD') ? Equipment::CONDITION_GOOD : 'good';

        Schema::create('equipment', function (Blueprint $table) use ($defaultAssetType, $defaultStatus, $defaultCondition): void {
            $table->id();
            $table->foreignId('equipment_category_id')->nullable()->constrained('equipment_categories')->onDelete('set null');
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories')->onDelete('set null');
            $table->string('item_code')->nullable()->unique()->comment('Unique internal identifier');
            $table->string('tag_id')->nullable()->unique()->comment('Asset tag / No. Aset');
            $table->string('serial_number')->nullable()->unique()->comment('Manufacturer Serial Number');
            $table->string('asset_type')->default($defaultAssetType)->comment('Specific type of asset');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->text('description')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry_date')->nullable();
            $table->string('status')->default($defaultStatus)->comment('Operational status');
            $table->string('condition_status')->default($defaultCondition)->comment('Physical condition');
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->string('current_location')->nullable();
            $table->text('notes')->nullable();
            $table->string('classification')->nullable();
            $table->string('acquisition_type')->nullable();
            $table->string('funded_by')->nullable();
            $table->string('supplier_name')->nullable();
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for common queries
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

    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table): void {
            $foreignKeysToDropByColumn = [
                'equipment_category_id', 'sub_category_id', 'location_id', 'department_id',
                'created_by', 'updated_by', 'deleted_by',
            ];
            foreach ($foreignKeysToDropByColumn as $column) {
                if (Schema::hasColumn('equipment', $column)) {
                    try {
                        $table->dropForeign([$column]);
                    } catch (\Exception $e) {
                        Log::warning(sprintf("Could not drop foreign key for column '%s' on 'equipment' table during migration rollback: ", $column) . $e->getMessage());
                    }
                }
            }
        });
        Schema::dropIfExists('equipment');
    }
};
