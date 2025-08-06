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
     * Display a listing of the equipment.
     */
    public function index(): View
    {
        Log::info('Admin\\EquipmentController@index: Displaying equipment list.', ['admin_user_id' => Auth::id()]);
        // The actual equipment listing is likely handled by a Livewire component on the blade view.
        // This controller method primarily serves to display the view.

        return view('resource-management.equipment-admin.index')->with([
            // Any data needed for the initial view load, if not handled by Livewire's mount
        ]);
    }

    /**
     * Show the form for creating new equipment.
     */
    public function create(): View
    {
        Log::info('Admin\\EquipmentController@create: Displaying create equipment form.', ['admin_user_id' => Auth::id()]);

        $assetTypes = Equipment::getAssetTypeOptions();
        $statuses = Equipment::getStatusOptions();
        $conditionStatuses = Equipment::getConditionStatusesList();

        // Pass these to the Livewire component if it's rendered directly
        // Otherwise, the Livewire component's mount method will handle it
        return view('resource-management.equipment-admin.create', compact('assetTypes', 'statuses', 'conditionStatuses'));
    }

    /**
     * Store a newly created equipment in storage.
     */
    public function store(StoreEquipmentRequest $request): RedirectResponse
    {
        Log::info('Admin\\EquipmentController@store: Attempting to store new equipment.', ['admin_user_id' => Auth::id(), 'request_data_keys' => array_keys($request->validated())]);
        try {
            $equipment = $this->equipmentService->createEquipment($request->validated());
            Log::info(sprintf('Admin Equipment ID: %d created successfully.', $equipment->id), ['admin_user_id' => Auth::id()]);

            return redirect()->route('resource-management.equipment-admin.index')
                ->with('success', __('Peralatan ICT berjaya ditambah.'));
        } catch (\Exception $exception) {
            Log::error('Error creating equipment by admin: '.$exception->getMessage(), ['exception_class' => get_class($exception), 'trace_snippet' => substr($exception->getTraceAsString(), 0, 500)]);

            return back()->with('error', __('Gagal menambah peralatan ICT: ').$exception->getMessage())->withInput();
        }
    }

    /**
     * Display the specified equipment.
     */
    public function show(Equipment $equipment): View
    {
        Log::info(sprintf('Admin\\EquipmentController@show: Displaying equipment ID: %d.', $equipment->id), ['admin_user_id' => Auth::id()]);

        return view('resource-management.equipment-admin.show', compact('equipment'));
    }

    /**
     * Show the form for editing the specified equipment.
     */
    public function edit(Equipment $equipment): View
    {
        Log::info(sprintf('Admin\\EquipmentController@edit: Displaying edit form for equipment ID: %d.', $equipment->id), ['admin_user_id' => Auth::id()]);
        // The Livewire component EquipmentForm handles populating the fields.
        // We only need to pass the equipment ID.

        return view('resource-management.equipment-admin.edit', compact('equipment'));
    }

    /**
     * Update the specified equipment in storage.
     */
    public function update(UpdateEquipmentRequest $request, Equipment $equipment): RedirectResponse
    {
        Log::info(sprintf('Admin\\EquipmentController@update: Attempting to update equipment ID: %d.', $equipment->id), ['admin_user_id' => Auth::id(), 'request_data_keys' => array_keys($request->validated())]);
        try {
            $this->equipmentService->updateEquipment($equipment, $request->validated());
            Log::info(sprintf('Admin Equipment ID: %d updated successfully.', $equipment->id), ['admin_user_id' => Auth::id()]);

            return redirect()->route('resource-management.equipment-admin.index')
                ->with('success', __('Peralatan ICT berjaya dikemaskini.'));
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
        Log::info(sprintf('Admin\\EquipmentController@destroy: Attempting to delete equipment ID: %d.', $equipment->id), ['admin_user_id' => Auth::id()]);
        try {
            // Prevent deletion if the equipment is currently on loan
            // Using direct check, ensure Equipment::STATUS_LOANED is correctly defined
            if ($equipment->status === Equipment::STATUS_LOANED) { // Changed from STATUS_ON_LOAN
                return redirect()->route('resource-management.equipment-admin.index')
                    ->with('error', __('Peralatan tidak boleh dipadam kerana sedang dalam pinjaman.'));
            }

            $this->equipmentService->deleteEquipment($equipment);
            Log::info(sprintf('Admin Equipment ID: %d deleted successfully.', $equipment->id), ['admin_user_id' => Auth::id()]);

            return redirect()->route('resource-management.equipment-admin.index')
                ->with('success', __('Peralatan ICT berjaya dipadam.'));
        } catch (\Exception $exception) {
            Log::error(sprintf('Error deleting equipment ID %d by admin: ', $equipment->id) . $exception->getMessage(), ['exception_class' => get_class($exception), 'trace_snippet' => substr($exception->getTraceAsString(), 0, 500)]);

            return back()->with('error', __('Gagal memadam peralatan ICT: ') . $exception->getMessage());
        }
    }
}
