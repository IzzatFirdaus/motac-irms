<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Equipment;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException; // Standard PHP exception
use RuntimeException;         // Standard PHP exception
use Throwable;

/**
 * Service class for managing ICT Equipment.
 * Handles business logic related to equipment CRUD, status changes, and querying.
 * System Design Reference: Sections 3.1 (Services), 9.3.
 */
final class EquipmentService
{
    private const LOG_AREA = 'EquipmentService:';

    /**
     * Get all equipment with optional filters and eager loading.
     *
     * @param array<string, mixed> $filters       Available filters: 'status', 'condition_status', 'asset_type', 'search', 'location_id', 'department_id', 'classification', 'acquisition_type'.
     * @param array<int, string>   $with          Relationships to eager load.
     * @param int                  $perPage       Items per page. Use -1 for all results (returns EloquentCollection).
     * @param string               $sortBy        Column to sort by.
     * @param string               $sortDirection Sort direction ('asc' or 'desc').
     *
     * @return LengthAwarePaginator<Equipment>|EloquentCollection<int, Equipment>
     */
    public function getAllEquipment(
        array $filters = [],
        array $with = [],
        int $perPage = 15, // Default to 15 items per page
        string $sortBy = 'tag_id', // Default sort field
        string $sortDirection = 'asc' // Default sort direction
    ): LengthAwarePaginator|EloquentCollection {
        Log::debug(self::LOG_AREA . ' getAllEquipment called', ['filters' => $filters, 'with' => $with, 'perPage' => $perPage, 'sortBy' => $sortBy, 'sortDirection' => $sortDirection]);

        $query = Equipment::query();

        // Default relationships to eager load for general listings
        // MODIFIED: Removed 'grade:id,name' as Equipment model does not have a direct 'grade' relationship.
        $defaultWith = ['department:id,name', 'equipmentCategory:id,name', 'subCategory:id,name', 'definedLocation:id,name'];
        $finalWith   = array_unique(array_merge($defaultWith, $with));

        if ($finalWith !== []) {
            $query->with($finalWith);
        }

        // Apply filters
        if (! empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['condition_status']) && $filters['condition_status'] !== 'all') {
            $query->where('condition_status', $filters['condition_status']);
        }

        if (! empty($filters['asset_type']) && $filters['asset_type'] !== 'all') {
            $query->where('asset_type', $filters['asset_type']);
        }

        if (! empty($filters['location_id'])) {
            $query->where('location_id', $filters['location_id']);
        }

        if (! empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (! empty($filters['classification']) && $filters['classification'] !== 'all') {
            $query->where('classification', $filters['classification']);
        }

        if (! empty($filters['acquisition_type']) && $filters['acquisition_type'] !== 'all') {
            $query->where('acquisition_type', $filters['acquisition_type']);
        }

        // Apply search term across multiple fields
        if (isset($filters['search']) && is_string($filters['search']) && trim($filters['search']) !== '') {
            $searchTerm = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($searchTerm): void {
                // Assuming 'name' field might exist in future or is contextually relevant for search.
                // If 'name' is not on equipment model, remove it or alias to a searchable field like 'item_code' or 'description'.
                // For now, let's assume 'item_code' or 'description' are primary textual identifiers.
                $q->where('item_code', 'like', $searchTerm)
                    ->orWhere('tag_id', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm)
                    ->orWhere('asset_type', 'like', $searchTerm)
                    ->orWhere('brand', 'like', $searchTerm)
                    ->orWhere('model', 'like', $searchTerm)
                    ->orWhere('serial_number', 'like', $searchTerm)
                    ->orWhere('notes', 'like', $searchTerm)
                    ->orWhereHas('definedLocation', function ($queryLocation) use ($searchTerm): void {
                        $queryLocation->where('name', 'like', $searchTerm);
                    })
                    ->orWhereHas('department', function ($queryDepartment) use ($searchTerm): void {
                        $queryDepartment->where('name', 'like', $searchTerm);
                    })
                    ->orWhereHas('equipmentCategory', function ($queryCategory) use ($searchTerm): void {
                        $queryCategory->where('name', 'like', $searchTerm);
                    })
                    ->orWhereHas('subCategory', function ($querySubCategory) use ($searchTerm): void {
                        $querySubCategory->where('name', 'like', $searchTerm);
                    });
            });
        }

        // Apply sorting
        // Added 'item_code' to allowed sorts. Removed 'name' if not a direct field of Equipment.
        $allowedSorts      = ['item_code', 'tag_id', 'asset_type', 'brand', 'model', 'status', 'condition_status', 'purchase_date', 'created_at', 'updated_at'];
        $safeSortBy        = in_array($sortBy, $allowedSorts) ? $sortBy : 'tag_id';
        $safeSortDirection = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($safeSortBy, $safeSortDirection);

        if ($perPage === -1) {
            return $query->get();
        }

        return $query->paginate($perPage);
    }

    /**
     * Find a specific equipment item by its ID, with optional eager loading.
     *
     * @param int                $id   The ID of the equipment.
     * @param array<int, string> $with Relationships to eager load.
     *
     * @return Equipment|null The found equipment model or null if not found.
     */
    public function findEquipmentById(int $id, array $with = []): ?Equipment
    {
        Log::debug(self::LOG_AREA . ' findEquipmentById called', ['id' => $id, 'with' => $with]);
        // Default relations typically needed when viewing a single equipment item
        // MODIFIED: Removed 'grade' as it's not defined on Equipment model
        $defaultWith = [
            'department',
            'equipmentCategory',
            'subCategory',
            'definedLocation',
            // 'grade', // Removed
            'creator',
            'updater',
            'loanTransactionItems.loanTransaction', // This assumes LoanTransactionItem has a loanTransaction relationship
        ];
        $finalWith = array_unique(array_merge($defaultWith, $with));

        $query = Equipment::query(); // Start a new query

        if ($finalWith !== []) {
            $query->with($finalWith);
        }

        /** @var Equipment|null $equipment */
        $equipment = $query->find($id); // find() should be called on the query builder

        if (! $equipment) {
            Log::notice(self::LOG_AREA . sprintf(' Equipment with ID %d not found.', $id));
        }

        return $equipment;
    }

    /**
     * Create a new equipment item.
     * Assumes BlameableObserver handles created_by/updated_by via Auth::user().
     *
     * @param array<string, mixed> $data Validated data for creating equipment.
     *
     * @throws RuntimeException If creation fails.
     *
     * @return Equipment The newly created equipment model.
     */
    public function createEquipment(array $data): Equipment
    {
        Log::info(self::LOG_AREA . ' Attempting to create equipment.', ['data_keys' => array_keys($data)]);
        DB::beginTransaction();
        try {
            /** @var Equipment $equipment */
            $equipment = Equipment::create($data);
            DB::commit();
            Log::info(self::LOG_AREA . ' Equipment created successfully.', ['equipment_id' => $equipment->id]);

            return $equipment;
        } catch (Throwable $throwable) {
            DB::rollBack();
            Log::error(self::LOG_AREA . ' Failed to create equipment: ' . $throwable->getMessage(), ['exception_class' => get_class($throwable), 'trace_snippet' => substr($throwable->getTraceAsString(), 0, 500), 'data' => $data]);
            throw new RuntimeException(__('Gagal mencipta item peralatan: ') . $throwable->getMessage(), 0, $throwable);
        }
    }

    /**
     * Update an existing equipment item.
     * Assumes BlameableObserver handles updated_by via Auth::user().
     *
     * @param Equipment            $equipment The equipment model instance to update.
     * @param array<string, mixed> $data      Validated data for updating equipment.
     *
     * @throws RuntimeException If update fails.
     *
     * @return bool True if update was successful, false otherwise.
     */
    public function updateEquipment(Equipment $equipment, array $data): bool
    {
        Log::info(self::LOG_AREA . ' Attempting to update equipment.', ['equipment_id' => $equipment->id, 'data_keys' => array_keys($data)]);
        DB::beginTransaction();
        try {
            $updated = $equipment->update($data);
            DB::commit();
            Log::info(self::LOG_AREA . ' Equipment ID ' . $equipment->id . ' update status: ' . ($updated ? 'Success' : 'No changes or failed'));

            return $updated;
        } catch (Throwable $throwable) {
            DB::rollBack();
            Log::error(self::LOG_AREA . ' Failed to update equipment ID ' . $equipment->id . ': ' . $throwable->getMessage(), ['exception_class' => get_class($throwable), 'trace_snippet' => substr($throwable->getTraceAsString(), 0, 500), 'data' => $data]);
            throw new RuntimeException(__('Gagal mengemaskini item peralatan: ') . $throwable->getMessage(), 0, $throwable);
        }
    }

    /**
     * Soft delete an equipment item.
     * Assumes BlameableObserver handles deleted_by.
     *
     * @param Equipment $equipment The equipment model instance to delete.
     *
     * @throws RuntimeException If deletion is not allowed (e.g., on loan) or fails.
     *
     * @return bool True if soft delete was successful.
     */
    public function deleteEquipment(Equipment $equipment): bool
    {
        Log::info(self::LOG_AREA . ' Attempting to soft delete equipment.', ['equipment_id' => $equipment->id, 'current_status' => $equipment->status]);
        if ($equipment->status === Equipment::STATUS_ON_LOAN) {
            Log::warning(self::LOG_AREA . sprintf(' Attempt to delete equipment ID %d that is currently on loan.', $equipment->id));
            throw new RuntimeException(__('Tidak boleh memadam peralatan yang sedang dalam pinjaman. Sila pastikan peralatan telah dipulangkan terlebih dahulu.'));
        }

        DB::beginTransaction();
        try {
            $deleted = $equipment->delete();
            DB::commit();
            Log::info(self::LOG_AREA . ' Equipment ID ' . $equipment->id . ' soft deleted status: ' . ($deleted ? 'Success' : 'Failed'));

            return (bool) $deleted;
        } catch (Throwable $throwable) {
            DB::rollBack();
            Log::error(self::LOG_AREA . ' Failed to soft delete equipment ID ' . $equipment->id . ': ' . $throwable->getMessage(), ['exception_class' => get_class($throwable), 'trace_snippet' => substr($throwable->getTraceAsString(), 0, 500)]);
            throw new RuntimeException(__('Gagal memadam item peralatan: ') . $throwable->getMessage(), 0, $throwable);
        }
    }

    /**
     * Restore a soft-deleted equipment item.
     * BlameableObserver should handle clearing deleted_by and setting updated_by.
     */
    public function restoreEquipment(Equipment $equipment): bool
    {
        Log::info(self::LOG_AREA . ' Attempting to restore equipment.', ['equipment_id' => $equipment->id]);
        DB::beginTransaction();
        try {
            $restored = $equipment->restore();
            DB::commit();
            Log::info(self::LOG_AREA . ' Equipment ID ' . $equipment->id . ' restored status: ' . ($restored ? 'Success' : 'Failed/No action'));

            return (bool) $restored;
        } catch (Throwable $throwable) {
            DB::rollBack();
            Log::error(self::LOG_AREA . ' Failed to restore equipment ID ' . $equipment->id . ': ' . $throwable->getMessage(), ['exception_class' => get_class($throwable), 'trace_snippet' => substr($throwable->getTraceAsString(), 0, 500)]);
            throw new RuntimeException(__('Gagal memulihkan item peralatan: ') . $throwable->getMessage(), 0, $throwable);
        }
    }

    /**
     * Permanently delete an equipment item. Use with extreme caution.
     */
    public function forceDeleteEquipment(Equipment $equipment): bool
    {
        Log::warning(self::LOG_AREA . ' Attempting to FORCE DELETE equipment.', ['equipment_id' => $equipment->id]);
        if ($equipment->loanTransactionItems()->exists()) {
            Log::error(self::LOG_AREA . sprintf(' Attempt to FORCE DELETE equipment ID %d that has loan history.', $equipment->id));
            throw new RuntimeException(__('Tidak boleh memadam peralatan ini secara kekal kerana ia mempunyai sejarah transaksi pinjaman.'));
        }

        DB::beginTransaction();
        try {
            $forceDeleted = $equipment->forceDelete();
            DB::commit();
            Log::info(self::LOG_AREA . ' Equipment ID ' . $equipment->id . ' FORCE DELETED status: ' . ($forceDeleted ? 'Success' : 'Failed'));

            return (bool) $forceDeleted;
        } catch (Throwable $throwable) {
            DB::rollBack();
            Log::error(self::LOG_AREA . ' Failed to force delete equipment ID ' . $equipment->id . ': ' . $throwable->getMessage(), ['exception_class' => get_class($throwable), 'trace_snippet' => substr($throwable->getTraceAsString(), 0, 500)]);
            throw new RuntimeException(__('Gagal memadam item peralatan secara kekal: ') . $throwable->getMessage(), 0, $throwable);
        }
    }

    /**
     * Update the operational status of an equipment.
     * Delegates to the Equipment model's method, ensuring acting user is recorded.
     */
    public function changeOperationalStatus(
        Equipment $equipment,
        string $newOperationalStatus,
        User $actingUser,
        ?string $reason = null
    ): bool {
        Log::info(self::LOG_AREA . ' Attempting to update equipment operational status.', [
            'equipment_id'   => $equipment->id,
            'current_status' => $equipment->status,
            'new_status'     => $newOperationalStatus,
            'acting_user_id' => $actingUser->id,
            'reason'         => $reason,
        ]);

        if (! in_array($newOperationalStatus, Equipment::getOperationalStatusesList())) {
            throw new InvalidArgumentException('Status operasi tidak sah: ' . $newOperationalStatus);
        }

        if ($equipment->status === Equipment::STATUS_ON_LOAN && $newOperationalStatus !== Equipment::STATUS_ON_LOAN) {
            Log::warning(self::LOG_AREA . sprintf(" Equipment ID %d is currently ON_LOAN. Status change to '%s' should typically be handled via a return transaction.", $equipment->id, $newOperationalStatus));
        }

        if ($newOperationalStatus === Equipment::STATUS_ON_LOAN && $equipment->status !== Equipment::STATUS_ON_LOAN) {
            Log::warning(self::LOG_AREA . sprintf(' Equipment ID %d status is being manually set to ON_LOAN. This is typically handled by an issue transaction.', $equipment->id));
        }

        DB::beginTransaction();
        try {
            $defaultReason = $reason ?? __('Status operasi dikemaskini oleh pentadbir.');
            $updated       = $equipment->updateOperationalStatus($newOperationalStatus, $defaultReason, $actingUser->id);
            DB::commit();
            Log::info(self::LOG_AREA . ' Equipment ID ' . $equipment->id . ' operational status updated to ' . $newOperationalStatus . ': ' . ($updated ? 'Success' : 'No changes or failed'));

            return $updated;
        } catch (Throwable $throwable) {
            DB::rollBack();
            Log::error(self::LOG_AREA . ' Failed to update equipment ID ' . $equipment->id . ' operational status: ' . $throwable->getMessage(), ['exception_class' => get_class($throwable), 'trace_snippet' => substr($throwable->getTraceAsString(), 0, 500)]);
            throw new RuntimeException(__('Gagal mengemaskini status operasi peralatan: ') . $throwable->getMessage(), (int) $throwable->getCode(), $throwable);
        }
    }

    /**
     * Update the physical condition status of an equipment.
     * Delegates to the Equipment model's method, ensuring acting user is recorded.
     */
    public function changeConditionStatus(
        Equipment $equipment,
        string $newConditionStatus,
        User $actingUser,
        ?string $reason = null
    ): bool {
        Log::info(self::LOG_AREA . ' Attempting to update equipment physical condition status.', [
            'equipment_id'      => $equipment->id,
            'current_condition' => $equipment->condition_status,
            'new_condition'     => $newConditionStatus,
            'acting_user_id'    => $actingUser->id,
            'reason'            => $reason,
        ]);

        if (! in_array($newConditionStatus, Equipment::getConditionStatusesList())) {
            throw new InvalidArgumentException('Status keadaan fizikal tidak sah: ' . $newConditionStatus);
        }

        DB::beginTransaction();
        try {
            $defaultReason = $reason ?? __('Status keadaan fizikal dikemaskini oleh pentadbir.');
            $updated       = $equipment->updatePhysicalConditionStatus($newConditionStatus, $defaultReason, $actingUser->id);
            DB::commit();
            Log::info(self::LOG_AREA . ' Equipment ID ' . $equipment->id . ' physical condition status updated to ' . $newConditionStatus . ': ' . ($updated ? 'Success' : 'No changes or failed'));

            return $updated;
        } catch (Throwable $throwable) {
            DB::rollBack();
            Log::error(self::LOG_AREA . ' Failed to update equipment ID ' . $equipment->id . ' physical condition status: ' . $throwable->getMessage(), ['exception_class' => get_class($throwable), 'trace_snippet' => substr($throwable->getTraceAsString(), 0, 500)]);
            throw new RuntimeException(__('Gagal mengemaskini status keadaan fizikal peralatan: ') . $throwable->getMessage(), (int) $throwable->getCode(), $throwable);
        }
    }
}
