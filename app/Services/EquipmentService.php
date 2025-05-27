<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Equipment;
use App\Models\User; // Added to type hint actingUser consistently
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection; // Corrected to use EloquentCollection
use Illuminate\Support\Facades\Auth; // Needed if Auth::id() is used as fallback
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException; // Corrected namespace
use RuntimeException;
use Throwable;

final class EquipmentService
{
    private const LOG_AREA = 'EquipmentService:';


    /**
     * Get all equipment with optional filters and eager loading.
     *
     * @param  array<string, mixed>  $filters  Available filters: 'status', 'condition_status', 'asset_type', 'search', 'location_id', 'department_id', 'classification', 'acquisition_type'.
     * @param  array<int, string>  $with  Relationships to eager load.
     * @param  int  $perPage  Items per page. Use -1 for all results.
     * @param  string  $sortBy  Column to sort by.
     * @param  string  $sortDirection  Sort direction ('asc' or 'desc').
     * @return LengthAwarePaginator<Equipment>|EloquentCollection<int, Equipment>
     */
    public function getAllEquipment(
        array $filters = [],
        array $with = [],
        int $perPage = 15,
        string $sortBy = 'tag_id',
        string $sortDirection = 'asc'
    ): LengthAwarePaginator|EloquentCollection {
        Log::debug(self::LOG_AREA.' getAllEquipment called', compact('filters', 'with', 'perPage', 'sortBy', 'sortDirection'));
        $query = Equipment::query();

        $defaultWith = ['department', 'equipmentCategory', 'subCategory', 'definedLocation']; // Renamed from location to definedLocation
        $finalWith = array_unique(array_merge($defaultWith, $with));

        if (count($finalWith) > 0) {
            $query->with($finalWith);
        }

        // Filter by operational status
        if (! empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }
        // Filter by physical condition status
        if (! empty($filters['condition_status']) && $filters['condition_status'] !== 'all') {
            $query->where('condition_status', $filters['condition_status']);
        }
        // Filter by asset type
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

        if (isset($filters['search']) && $filters['search'] !== '') {
            $searchTerm = '%'.trim($filters['search']).'%';
            $query->where(function ($q) use ($searchTerm): void {
                $q->where('tag_id', 'like', $searchTerm)
                    ->orWhere('asset_type', 'like', $searchTerm)
                    ->orWhere('brand', 'like', $searchTerm)
                    ->orWhere('model', 'like', $searchTerm)
                    ->orWhere('serial_number', 'like', $searchTerm)
                    ->orWhereHas('definedLocation', function ($queryLocation) use ($searchTerm): void { // Changed to definedLocation
                        $queryLocation->where('name', 'like', $searchTerm);
                    })
                    ->orWhere('description', 'like', $searchTerm)
                    ->orWhere('item_code', 'like', $searchTerm) // Added item_code to search
                    ->orWhere('notes', 'like', $searchTerm);
            });
        }

        $allowedSorts = ['tag_id', 'asset_type', 'brand', 'model', 'status', 'condition_status', 'purchase_date', 'created_at', 'updated_at', 'item_code'];
        $safeSortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'tag_id';
        $safeSortDirection = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';
        $query->orderBy($safeSortBy, $safeSortDirection);

        if ($perPage === -1) {
            return $query->get();
        }

        return $query->paginate($perPage);
    }

    public function findEquipmentById(int $id, array $with = []): ?Equipment
    {
        Log::debug(self::LOG_AREA.' findEquipmentById called', ['id' => $id, 'with' => $with]); // Corrected log message to match method name
        $defaultWith = ['department', 'equipmentCategory', 'subCategory', 'definedLocation']; // Default relations
        $finalWith = array_unique(array_merge($defaultWith, $with));

        // Corrected to fetch the model instance
        /** @var Equipment|null $equipment */
        $equipment = Equipment::with($finalWith)->find($id);

        if (! $equipment) {
            Log::notice(self::LOG_AREA." Equipment with ID {$id} not found.");
        }

        return $equipment;
    }

    public function createEquipment(array $data): Equipment
    {
        Log::info(self::LOG_AREA.' Attempting to create equipment.', ['data_keys' => array_keys($data)]);
        // BlameableObserver handles created_by/updated_by using Auth::user()

        DB::beginTransaction();
        try {
            /** @var Equipment $equipment */
            $equipment = Equipment::create($data);
            DB::commit();
            Log::info(self::LOG_AREA.' Equipment created successfully.', ['equipment_id' => $equipment->id]);

            return $equipment;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA.' Failed to create equipment: '.$e->getMessage(), ['exception_trace' => $e->getTraceAsString(), 'data' => $data]);
            throw new RuntimeException(__('Gagal mencipta item peralatan: ').$e->getMessage(), 0, $e);
        }
    }

    public function updateEquipment(Equipment $equipment, array $data): bool
    {
        Log::info(self::LOG_AREA.' Attempting to update equipment.', ['equipment_id' => $equipment->id, 'data_keys' => array_keys($data)]);
        // BlameableObserver handles updated_by using Auth::user()
        DB::beginTransaction();
        try {
            $updated = $equipment->update($data);
            DB::commit();
            Log::info(self::LOG_AREA.' Equipment ID '.$equipment->id.' update status: '.($updated ? 'Success' : 'No changes or failed'));

            return $updated;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA.' Failed to update equipment ID '.$equipment->id.': '.$e->getMessage(), ['exception_trace' => $e->getTraceAsString(), 'data' => $data]);
            throw new RuntimeException(__('Gagal mengemaskini item peralatan: ').$e->getMessage(), 0, $e);
        }
    }

    public function deleteEquipment(Equipment $equipment): bool
    {
        Log::info(self::LOG_AREA.' Attempting to soft delete equipment.', ['equipment_id' => $equipment->id]);
        if ($equipment->status === Equipment::STATUS_ON_LOAN) {
            Log::warning(self::LOG_AREA." Attempt to delete equipment ID {$equipment->id} that is currently on loan.");
            throw new RuntimeException(__('Tidak boleh memadam peralatan yang sedang dalam pinjaman.'));
        }

        DB::beginTransaction();
        try {
            $deleted = $equipment->delete(); // BlameableObserver handles deleted_by
            DB::commit();
            Log::info(self::LOG_AREA.' Equipment ID '.$equipment->id.' soft deleted status: '.($deleted ? 'Success' : 'Failed'));

            return (bool) $deleted;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA.' Failed to soft delete equipment ID '.$equipment->id.': '.$e->getMessage(), ['exception_trace' => $e->getTraceAsString()]);
            throw new RuntimeException(__('Gagal memadam item peralatan: ').$e->getMessage(), 0, $e);
        }
    }

    public function restoreEquipment(Equipment $equipment): bool
    {
        Log::info(self::LOG_AREA.' Attempting to restore equipment.', ['equipment_id' => $equipment->id]);
        DB::beginTransaction();
        try {
            $restored = $equipment->restore(); // BlameableObserver should handle clearing deleted_by and setting updated_by
            DB::commit();
            Log::info(self::LOG_AREA.' Equipment ID '.$equipment->id.' restored status: '.($restored ? 'Success' : 'Failed/No action'));

            return (bool) $restored;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA.' Failed to restore equipment ID '.$equipment->id.': '.$e->getMessage(), ['exception_trace' => $e->getTraceAsString()]);
            throw new RuntimeException(__('Gagal memulihkan item peralatan: ').$e->getMessage(), 0, $e);
        }
    }

    public function forceDeleteEquipment(Equipment $equipment): bool
    {
        Log::info(self::LOG_AREA.' Attempting to force delete equipment.', ['equipment_id' => $equipment->id]);
        DB::beginTransaction();
        try {
            $forceDeleted = $equipment->forceDelete();
            DB::commit();
            Log::info(self::LOG_AREA.' Equipment ID '.$equipment->id.' force deleted status: '.($forceDeleted ? 'Success' : 'Failed'));

            return (bool) $forceDeleted;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA.' Failed to force delete equipment ID '.$equipment->id.': '.$e->getMessage(), ['exception_trace' => $e->getTraceAsString()]);
            throw new RuntimeException(__('Gagal memadam item peralatan secara kekal: ').$e->getMessage(), 0, $e);
        }
    }

    /**
     * Update the operational status of an equipment using the model's dedicated method.
     */
    public function changeOperationalStatus(
        Equipment $equipment,
        string $newOperationalStatus,
        User $actingUser, // Explicitly pass the acting user
        ?string $reason = 'Status operasi dikemaskini.' // More generic default reason
    ): bool {
        Log::info(self::LOG_AREA.' Attempting to update equipment operational status.', [
            'equipment_id' => $equipment->id,
            'current_status' => $equipment->status,
            'new_status' => $newOperationalStatus,
            'acting_user_id' => $actingUser->id,
            'reason' => $reason
        ]);

        if (!in_array($newOperationalStatus, Equipment::getOperationalStatusesList())) {
            throw new InvalidArgumentException("Status operasi tidak sah: {$newOperationalStatus}");
        }

        if ($newOperationalStatus === Equipment::STATUS_ON_LOAN && $equipment->status !== Equipment::STATUS_ON_LOAN) {
            Log::warning(self::LOG_AREA." Manual update to 'On Loan' status is typically handled by loan issuance for Equipment ID {$equipment->id}.");
            // Allowing for flexibility if direct change is needed by authorized service/user
        }
        if ($equipment->status === Equipment::STATUS_ON_LOAN && $newOperationalStatus !== Equipment::STATUS_ON_LOAN) {
            Log::warning(self::LOG_AREA." Change from 'On Loan' status is typically handled by loan return for Equipment ID {$equipment->id}.");
            // Allowing for flexibility
        }

        DB::beginTransaction();
        try {
            $updated = $equipment->updateOperationalStatus($newOperationalStatus, $reason, $actingUser->id);
            DB::commit();
            Log::info(self::LOG_AREA.' Equipment ID '.$equipment->id.' operational status updated to '.$newOperationalStatus.': '.($updated ? 'Success' : 'No changes or failed'));
            return $updated;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA.' Failed to update equipment ID '.$equipment->id.' operational status: '.$e->getMessage(), ['exception_trace' => $e->getTraceAsString()]);
            throw new RuntimeException(__('Gagal mengemaskini status operasi peralatan: ').$e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Update the physical condition status of an equipment using the model's dedicated method.
     */
    public function changeConditionStatus(
        Equipment $equipment,
        string $newConditionStatus,
        User $actingUser, // Explicitly pass the acting user
        ?string $reason = 'Status keadaan fizikal dikemaskini.' // More generic default reason
    ): bool {
        Log::info(self::LOG_AREA.' Attempting to update equipment physical condition status.', [
            'equipment_id' => $equipment->id,
            'current_condition' => $equipment->condition_status,
            'new_condition' => $newConditionStatus,
            'acting_user_id' => $actingUser->id,
            'reason' => $reason
        ]);

        if (!in_array($newConditionStatus, Equipment::getConditionStatusesList())) {
            throw new InvalidArgumentException("Status keadaan fizikal tidak sah: {$newConditionStatus}");
        }

        DB::beginTransaction();
        try {
            $updated = $equipment->updatePhysicalConditionStatus($newConditionStatus, $reason, $actingUser->id);
            DB::commit();
            Log::info(self::LOG_AREA.' Equipment ID '.$equipment->id.' physical condition status updated to '.$newConditionStatus.': '.($updated ? 'Success' : 'No changes or failed'));
            return $updated;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error(self::LOG_AREA.' Failed to update equipment ID '.$equipment->id.' physical condition status: '.$e->getMessage(), ['exception_trace' => $e->getTraceAsString()]);
            throw new RuntimeException(__('Gagal mengemaskini status keadaan fizikal peralatan: ').$e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
