<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Equipment;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use RuntimeException;
use Throwable;

final class EquipmentService
{
    private const LOG_AREA = 'EquipmentService:';

    /**
     * Get a paginated list of equipment, with optional filters.
     *
     * @param array $filters Filters like 'status', 'asset_type', 'search_term'.
     * @param int $perPage Number of items per page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllEquipment(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        Log::debug(self::LOG_AREA.'Fetching all equipment.', ['filters' => $filters]);
        $query = Equipment::query()->with(['department', 'equipmentCategory', 'subCategory', 'location']); // Eager load common relationships

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['asset_type'])) {
            $query->where('asset_type', $filters['asset_type']);
        }
        if (!empty($filters['condition_status'])) {
            $query->where('condition_status', $filters['condition_status']);
        }
        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }
        if (!empty($filters['equipment_category_id'])) {
            $query->where('equipment_category_id', $filters['equipment_category_id']);
        }
        if (!empty($filters['search_term'])) {
            $term = $filters['search_term'];
            $query->where(function ($q) use ($term) {
                $q->where('tag_id', 'like', "%{$term}%")
                  ->orWhere('serial_number', 'like', "%{$term}%")
                  ->orWhere('brand', 'like', "%{$term}%")
                  ->orWhere('model', 'like', "%{$term}%")
                  ->orWhere('item_code', 'like', "%{$term}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Create a new equipment record.
     *
     * @param array $data Validated data from StoreEquipmentRequest.
     * @param User $creator The user creating the equipment.
     * @return Equipment
     * @throws RuntimeException
     */
    public function createEquipment(array $data, User $creator): Equipment
    {
        Log::info(self::LOG_AREA.'Attempting to create new equipment.', ['user_id' => $creator->id, 'data_keys' => array_keys($data)]);
        // BlameableObserver should handle created_by, updated_by if $creator is authenticated.
        // If not (e.g. console command without impersonation), ensure they are set or nullable.
        // $data['created_by'] = $creator->id;
        // $data['updated_by'] = $creator->id;

        try {
            /** @var Equipment $equipment */
            $equipment = Equipment::create($data);
            Log::info(self::LOG_AREA.'Equipment created successfully.', ['equipment_id' => $equipment->id, 'tag_id' => $equipment->tag_id]);
            return $equipment;
        } catch (Throwable $e) {
            Log::error(self::LOG_AREA.'Failed to create equipment.', ['error' => $e->getMessage(), 'data' => $data]);
            throw new RuntimeException(__('Gagal mencipta rekod peralatan: ').$e->getMessage(), 0, $e);
        }
    }

    /**
     * Update an existing equipment record.
     *
     * @param Equipment $equipment The equipment to update.
     * @param array $data Validated data from UpdateEquipmentRequest.
     * @param User $updater The user updating the equipment.
     * @return Equipment
     * @throws RuntimeException
     */
    public function updateEquipment(Equipment $equipment, array $data, User $updater): Equipment
    {
        Log::info(self::LOG_AREA.'Attempting to update equipment.', ['equipment_id' => $equipment->id, 'user_id' => $updater->id, 'data_keys' => array_keys($data)]);
        // $data['updated_by'] = $updater->id; // Handled by BlameableObserver

        try {
            $equipment->update($data);
            Log::info(self::LOG_AREA.'Equipment updated successfully.', ['equipment_id' => $equipment->id]);
            return $equipment->fresh(); // Return the updated model with fresh attributes
        } catch (Throwable $e) {
            Log::error(self::LOG_AREA.'Failed to update equipment.', ['equipment_id' => $equipment->id, 'error' => $e->getMessage(), 'data' => $data]);
            throw new RuntimeException(__('Gagal mengemaskini rekod peralatan: ').$e->getMessage(), 0, $e);
        }
    }

    /**
     * Soft delete an equipment record.
     *
     * @param Equipment $equipment The equipment to delete.
     * @param User $deleter The user deleting the equipment.
     * @return bool
     * @throws RuntimeException
     */
    public function deleteEquipment(Equipment $equipment, User $deleter): bool
    {
        Log::info(self::LOG_AREA.'Attempting to delete equipment.', ['equipment_id' => $equipment->id, 'user_id' => $deleter->id]);
        // Policy check for deletion (e.g., not on loan) should be in EquipmentPolicy@delete

        try {
            // $equipment->deleted_by = $deleter->id; // Handled by BlameableObserver on deleting event
            // $equipment->save(); // Observer handles this before delete if setting deleted_by
            $result = $equipment->delete(); // Soft delete
            if ($result) {
                Log::info(self::LOG_AREA.'Equipment soft deleted successfully.', ['equipment_id' => $equipment->id]);
            } else {
                Log::warning(self::LOG_AREA.'Equipment soft delete returned false.', ['equipment_id' => $equipment->id]);
            }
            return $result;
        } catch (Throwable $e) {
            Log::error(self::LOG_AREA.'Failed to delete equipment.', ['equipment_id' => $equipment->id, 'error' => $e->getMessage()]);
            throw new RuntimeException(__('Gagal memadam rekod peralatan: ').$e->getMessage(), 0, $e);
        }
    }

    /**
     * Find an equipment by its ID.
     *
     * @param int $equipmentId
     * @return Equipment|null
     */
    public function findEquipmentById(int $equipmentId): ?Equipment
    {
        return Equipment::find($equipmentId);
    }

    /**
     * Update the operational status of an equipment.
     */
    public function changeOperationalStatus(Equipment $equipment, string $newStatus, User $actingUser, ?string $notes = null): Equipment
    {
        Log::info(self::LOG_AREA.'Changing operational status.', [
            'equipment_id' => $equipment->id, 'new_status' => $newStatus, 'user_id' => $actingUser->id
        ]);
        if (!in_array($newStatus, Equipment::getOperationalStatusesList())) {
            throw new \InvalidArgumentException("Status operasi tidak sah: {$newStatus}");
        }

        // Add business logic here, e.g., cannot change status if on loan, except by return process.
        if ($equipment->status === Equipment::STATUS_ON_LOAN && $newStatus !== Equipment::STATUS_ON_LOAN) {
            // This change should ideally happen through a loan return transaction.
            Log::warning(self::LOG_AREA.'Attempt to change status of on-loan equipment outside of return process.', ['id' => $equipment->id, 'new_status' => $newStatus]);
            // Depending on rules, you might throw an exception or allow if user is Admin/BPM.
        }

        $equipment->status = $newStatus;
        if ($notes) {
            $equipment->notes = ($equipment->notes ? $equipment->notes . "\n" : '') . "Status changed to {$newStatus}: {$notes}";
        }
        // updated_by handled by observer
        $equipment->save();
        return $equipment;
    }

     /**
     * Update the physical condition status of an equipment.
     */
    public function changeConditionStatus(Equipment $equipment, string $newCondition, User $actingUser, ?string $notes = null): Equipment
    {
        Log::info(self::LOG_AREA.'Changing condition status.', [
            'equipment_id' => $equipment->id, 'new_condition' => $newCondition, 'user_id' => $actingUser->id
        ]);
        if (!in_array($newCondition, Equipment::getConditionStatusesList())) {
            throw new \InvalidArgumentException("Status keadaan fizikal tidak sah: {$newCondition}");
        }
        $equipment->condition_status = $newCondition;
         if ($notes) {
            $equipment->notes = ($equipment->notes ? $equipment->notes . "\n" : '') . "Condition changed to {$newCondition}: {$notes}";
        }
        // updated_by handled by observer
        $equipment->save();
        return $equipment;
    }
}
