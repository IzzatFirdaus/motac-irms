<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; // Base controller
use App\Http\Requests\Admin\StoreEquipmentRequest;
use App\Http\Requests\Admin\UpdateEquipmentRequest;
use App\Models\Department;
use App\Models\Equipment;
use App\Models\EquipmentCategory; // Make sure SubCategory model is used if needed in views
use App\Models\Location;
use App\Models\SubCategory;
use App\Services\EquipmentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class EquipmentController extends Controller
{
    use AuthorizesRequests;

    protected EquipmentService $equipmentService;

    public function __construct(EquipmentService $equipmentService)
    {
        $this->equipmentService = $equipmentService;
        $this->middleware('auth');
        // Apply role middleware for Admin and BPM Staff as per system design
        $this->middleware(['role:Admin|BPM Staff']);

        // Authorize resource actions using EquipmentPolicy
        $this->authorizeResource(Equipment::class, 'equipment');
    }

    /**
     * Show the form for creating new equipment.
     */
    public function create(): View
    {
        Log::info('Admin\EquipmentController@create: Displaying create equipment form.', ['admin_user_id' => Auth::id()]);

        $assetTypes = Equipment::getAssetTypeOptions();
        $initialStatuses = Equipment::getStatusOptions(); // Assuming getStatusOptions provides initial statuses as well, or you might need a dedicated method like getInitialStatusOptions()
        $conditionStatuses = Equipment::getConditionStatusOptions();
        $acquisitionTypes = Equipment::getAcquisitionTypeOptions();
        $classifications = Equipment::getClassificationOptions();

        $locations = Location::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $departments = Department::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $equipmentCategories = EquipmentCategory::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        // SubCategories will often be dynamically loaded based on the selected EquipmentCategory.
        // For 'create' if not dynamic, you might pass all of them.
        $subCategories = SubCategory::where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return view('admin.equipment.create', ['assetTypes' => $assetTypes, 'initialStatuses' => $initialStatuses, 'conditionStatuses' => $conditionStatuses, 'acquisitionTypes' => $acquisitionTypes, 'classifications' => $classifications, 'locations' => $locations, 'departments' => $departments, 'equipmentCategories' => $equipmentCategories, 'subCategories' => $subCategories]);
    }

    /**
     * Store a newly created equipment in storage.
     */
    public function store(StoreEquipmentRequest $request): RedirectResponse
    {
        Log::info('Admin\EquipmentController@store: Attempting to create equipment.', ['admin_user_id' => Auth::id(), 'data_keys' => array_keys($request->except(['_token']))]);
        try {
            $this->equipmentService->createEquipment($request->validated());
            Log::info('Admin Equipment created successfully.', ['admin_user_id' => Auth::id()]);

            return redirect()->route('resource-management.equipment-admin.index')
                ->with('success', __('Peralatan ICT berjaya ditambah.'));
        } catch (\Exception $exception) {
            Log::error('Error storing equipment by admin: '.$exception->getMessage(), ['exception_class' => get_class($exception), 'trace_snippet' => substr($exception->getTraceAsString(), 0, 500)]);

            return back()->with('error', __('Gagal menambah peralatan ICT: ').$exception->getMessage())->withInput();
        }
    }

    /**
     * Display the specified equipment.
     */
    public function show(Equipment $equipment): View
    {
        Log::info(sprintf('Admin\EquipmentController@show: Displaying equipment ID %d.', $equipment->id), ['admin_user_id' => Auth::id()]);
        // Eager load necessary relationships for the show view
        $equipment->loadMissing([
            'department:id,name',
            'equipmentCategory:id,name',
            'subCategory:id,name',
            'definedLocation:id,name', // Assuming 'definedLocation' is the correct relationship to Location model
            'creator:id,name',         // Creator relation for audit trail
            'updater:id,name',         // Updater relation for audit trail
            'loanTransactionItems.loanTransaction.loanApplication.user:id,name', // For detailed loan history
        ]);

        return view('admin.equipment.show', ['equipment' => $equipment]);
    }

    /**
     * Show the form for editing the specified equipment.
     */
    public function edit(Equipment $equipment): View
    {
        Log::info(sprintf('Admin\EquipmentController@edit: Displaying edit form for equipment ID %d.', $equipment->id), ['admin_user_id' => Auth::id()]);

        // Fetch all necessary data for dropdowns
        $assetTypes = Equipment::getAssetTypeOptions();
        $statusOptions = Equipment::getStatusOptions(); // Full list of operational statuses
        $conditionStatusOptions = Equipment::getConditionStatusOptions(); // Full list of physical condition statuses
        $acquisitionTypes = Equipment::getAcquisitionTypeOptions();
        $classifications = Equipment::getClassificationOptions();

        $locations = Location::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $departments = Department::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $equipmentCategories = EquipmentCategory::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        // SubCategories might need to be filtered by the currently selected equipment's category,
        // or loaded dynamically via JavaScript/Livewire. For a standard dropdown, provide all.
        $subCategories = SubCategory::where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return view('admin.equipment.edit', ['equipment' => $equipment, 'assetTypes' => $assetTypes, 'statusOptions' => $statusOptions, 'conditionStatusOptions' => $conditionStatusOptions, 'acquisitionTypes' => $acquisitionTypes, 'classifications' => $classifications, 'locations' => $locations, 'departments' => $departments, 'equipmentCategories' => $equipmentCategories, 'subCategories' => $subCategories]);
    }

    /**
     * Update the specified equipment in storage.
     */
    public function update(UpdateEquipmentRequest $request, Equipment $equipment): RedirectResponse
    {
        Log::info(sprintf('Admin\EquipmentController@update: Attempting to update equipment ID: %d.', $equipment->id), ['admin_user_id' => Auth::id(), 'data_keys' => array_keys($request->except(['_token', '_method']))]);
        try {
            $this->equipmentService->updateEquipment($equipment, $request->validated());
            Log::info(sprintf('Admin Equipment ID: %d updated successfully.', $equipment->id), ['admin_user_id' => Auth::id()]);

            return redirect()->route('resource-management.equipment-admin.index')
                ->with('success', __('Butiran peralatan ICT berjaya dikemaskini.'));
        } catch (\Exception $exception) {
            Log::error(sprintf('Error updating equipment ID %d by admin: ', $equipment->id).$exception->getMessage(), ['exception_class' => get_class($exception), 'trace_snippet' => substr($exception->getTraceAsString(), 0, 500)]);

            return back()->with('error', __('Gagal mengemaskini peralatan ICT: ').$exception->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified equipment from storage.
     */
    public function destroy(Equipment $equipment): RedirectResponse
    {
        Log::info(sprintf('Admin\EquipmentController@destroy: Attempting to delete equipment ID: %d.', $equipment->id), ['admin_user_id' => Auth::id()]);
        try {
            // Prevent deletion if the equipment is currently on loan
            if (defined(Equipment::class.'::STATUS_ON_LOAN') && $equipment->status === Equipment::STATUS_ON_LOAN) {
                return redirect()->route('resource-management.equipment-admin.index')
                    ->with('error', __('Peralatan tidak boleh dipadam kerana sedang dalam pinjaman.'));
            }

            $this->equipmentService->deleteEquipment($equipment);
            Log::info(sprintf('Admin Equipment ID: %d deleted successfully.', $equipment->id), ['admin_user_id' => Auth::id()]);

            return redirect()->route('resource-management.equipment-admin.index')
                ->with('success', __('Peralatan ICT berjaya dipadam.'));
        } catch (\Exception $exception) {
            Log::error(sprintf('Error deleting equipment ID %d by admin: ', $equipment->id).$exception->getMessage(), ['exception_class' => get_class($exception), 'trace_snippet' => substr($exception->getTraceAsString(), 0, 500)]);

            return back()->with('error', __('Gagal memadam peralatan ICT: ').$exception->getMessage());
        }
    }
}
