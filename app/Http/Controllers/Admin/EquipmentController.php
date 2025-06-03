<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; // Base controller
use App\Models\Equipment;
use App\Models\Location;
use App\Models\Department;
use App\Models\EquipmentCategory;
// use App\Models\SubCategory; // Uncomment if SubCategory model is used
use App\Services\EquipmentService;
use App\Http\Requests\Admin\StoreEquipmentRequest;
use App\Http\Requests\Admin\UpdateEquipmentRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
// use Illuminate\Http\Request; // Using FormRequests for store/update
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EquipmentController extends Controller
{
    use AuthorizesRequests;

    protected EquipmentService $equipmentService;

    public function __construct(EquipmentService $equipmentService)
    {
        $this->equipmentService = $equipmentService;
        $this->middleware('auth');
        $this->middleware(['role:Admin|BPM Staff']); // System Design: Role for equipment management

        // Assumes EquipmentPolicy is registered and defines abilities for equipment resource
        $this->authorizeResource(Equipment::class, 'equipment');
    }

    /**
     * Show the form for creating new equipment.
     */
    public function create(): View
    {
        Log::info('Admin\EquipmentController@create: Displaying create equipment form.', ['admin_user_id' => Auth::id()]);
        // Ensure these static methods exist on the Equipment model or are sourced appropriately
        $viewData = [
            'assetTypes' => method_exists(Equipment::class, 'getAssetTypeOptions') ? Equipment::getAssetTypeOptions() : [],
            'initialStatuses' => method_exists(Equipment::class, 'getInitialStatusOptions') ? Equipment::getInitialStatusOptions() : [],
            'conditionStatuses' => method_exists(Equipment::class, 'getConditionStatusOptions') ? Equipment::getConditionStatusOptions() : [],
            'acquisitionTypes' => method_exists(Equipment::class, 'getAcquisitionTypeOptions') ? Equipment::getAcquisitionTypeOptions() : [],
            'classifications' => method_exists(Equipment::class, 'getClassificationOptions') ? Equipment::getClassificationOptions() : [],
            'locations' => Location::where('is_active', true)->orderBy('name')->pluck('name', 'id'),
            'departments' => Department::where('is_active', true)->orderBy('name')->pluck('name', 'id'),
            'equipmentCategories' => EquipmentCategory::where('is_active', true)->orderBy('name')->pluck('name', 'id'),
            // 'subCategories' => SubCategory::where('is_active', true)->orderBy('name')->pluck('name', 'id'), // Or load dynamically based on category
        ];
        return view('admin.equipment.create', $viewData);
    }

    /**
     * Store a newly created equipment in storage.
     */
    public function store(StoreEquipmentRequest $request): RedirectResponse
    {
        Log::info('Admin\EquipmentController@store: Attempting to create equipment.', ['admin_user_id' => Auth::id(), 'data_keys' => array_keys($request->except(['_token']))]);
        try {
            // Assuming EquipmentService->createEquipment handles the creation logic
            $this->equipmentService->createEquipment($request->validated());
            Log::info('Admin Equipment created successfully.', ['admin_user_id' => Auth::id()]);
            return redirect()->route('resource-management.equipment-admin.index')
                             ->with('success', __('Peralatan ICT berjaya ditambah.'));
        } catch (\Exception $e) {
            Log::error('Error storing equipment by admin: ' . $e->getMessage(), ['exception_class' => get_class($e), 'trace_snippet' => substr($e->getTraceAsString(),0,500)]);
            return back()->with('error', __('Gagal menambah peralatan ICT: ') . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified equipment.
     */
    public function show(Equipment $equipment): View
    {
        Log::info("Admin\EquipmentController@show: Displaying equipment ID {$equipment->id}.", ['admin_user_id' => Auth::id()]);
        // Verify these relationship names with your Equipment model:
        // 'grade' is unusual for an equipment item directly.
        // 'location' vs 'definedLocation' - ensure consistency.
        $equipment->loadMissing([
            // 'grade', // Does Equipment have a direct 'grade' relationship?
            'department:id,name',
            'equipmentCategory:id,name',
            'subCategory:id,name',    // Ensure this relation exists
            'definedLocation:id,name',// Use 'definedLocation' if that's the relation name (from user-facing controller) or 'location' if it's direct.
            'creator:id,name',        // Blameable: user who created
            'updater:id,name',        // Blameable: user who last updated
            'loanTransactionItems.loanTransaction.loanApplication.user:id,name' // For detailed loan history
        ]);
        return view('admin.equipment.show', compact('equipment'));
    }

    /**
     * Show the form for editing the specified equipment.
     */
    public function edit(Equipment $equipment): View
    {
        Log::info("Admin\EquipmentController@edit: Displaying edit form for equipment ID {$equipment->id}.", ['admin_user_id' => Auth::id()]);
        $viewData = [
            'equipment' => $equipment,
            'assetTypes' => method_exists(Equipment::class, 'getAssetTypeOptions') ? Equipment::getAssetTypeOptions() : [],
            'allStatuses' => method_exists(Equipment::class, 'getStatusOptions') ? Equipment::getStatusOptions() : [], // All statuses for editing
            'conditionStatuses' => method_exists(Equipment::class, 'getConditionStatusOptions') ? Equipment::getConditionStatusOptions() : [],
            'acquisitionTypes' => method_exists(Equipment::class, 'getAcquisitionTypeOptions') ? Equipment::getAcquisitionTypeOptions() : [],
            'classifications' => method_exists(Equipment::class, 'getClassificationOptions') ? Equipment::getClassificationOptions() : [],
            'locations' => Location::where('is_active', true)->orderBy('name')->pluck('name', 'id'),
            'departments' => Department::where('is_active', true)->orderBy('name')->pluck('name', 'id'),
            'equipmentCategories' => EquipmentCategory::where('is_active', true)->orderBy('name')->pluck('name', 'id'),
            // 'subCategories' => $equipment->equipmentCategory ? $equipment->equipmentCategory->subCategories()->orderBy('name')->pluck('name', 'id') : collect(),
        ];
        return view('admin.equipment.edit', $viewData);
    }

    /**
     * Update the specified equipment in storage.
     */
    public function update(UpdateEquipmentRequest $request, Equipment $equipment): RedirectResponse
    {
        Log::info("Admin\EquipmentController@update: Attempting to update grade ID: {$equipment->id}.", ['admin_user_id' => Auth::id(), 'data_keys' => array_keys($request->except(['_token', '_method']))]);
        try {
            // Assuming EquipmentService->updateEquipment handles the update logic
            $this->equipmentService->updateEquipment($equipment, $request->validated());
            Log::info("Admin Equipment ID: {$equipment->id} updated successfully.", ['admin_user_id' => Auth::id()]);
            return redirect()->route('resource-management.equipment-admin.index')
                             ->with('success', __('Butiran peralatan ICT berjaya dikemaskini.'));
        } catch (\Exception $e) {
            Log::error("Error updating equipment ID {$equipment->id} by admin: " . $e->getMessage(), ['exception_class' => get_class($e), 'trace_snippet' => substr($e->getTraceAsString(),0,500)]);
            return back()->with('error', __('Gagal mengemaskini peralatan ICT: ') . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified equipment from storage.
     */
    public function destroy(Equipment $equipment): RedirectResponse
    {
        Log::info("Admin\EquipmentController@destroy: Attempting to delete equipment ID: {$equipment->id}.", ['admin_user_id' => Auth::id()]);
        try {
            // Policy should check if deletable. Additional business logic can be in service.
            if (defined(Equipment::class.'::STATUS_ON_LOAN') && $equipment->status === Equipment::STATUS_ON_LOAN) {
                 return redirect()->route('resource-management.equipment-admin.index')
                                 ->with('error', __('Peralatan tidak boleh dipadam kerana sedang dalam pinjaman.'));
            }
            // Assuming EquipmentService->deleteEquipment handles the deletion logic
            $this->equipmentService->deleteEquipment($equipment);
            Log::info("Admin Equipment ID: {$equipment->id} deleted successfully.", ['admin_user_id' => Auth::id()]);
            return redirect()->route('resource-management.equipment-admin.index')
                             ->with('success', __('Peralatan ICT berjaya dipadam.'));
        } catch (\Exception $e) {
             Log::error("Error deleting equipment ID {$equipment->id} by admin: " . $e->getMessage(), ['exception_class' => get_class($e), 'trace_snippet' => substr($e->getTraceAsString(),0,500)]);
            return back()->with('error', __('Gagal memadam peralatan ICT: ') . $e->getMessage());
        }
    }
}
