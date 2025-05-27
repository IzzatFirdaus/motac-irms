<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Location; // If providing a full list of locations for filtering
use App\Services\EquipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class EquipmentController extends Controller
{
    protected EquipmentService $equipmentService;

    public function __construct(EquipmentService $equipmentService)
    {
        $this->middleware('auth');
        $this->equipmentService = $equipmentService;
        // Authorization for 'index' (viewAny) and 'show' (view) handled by EquipmentPolicy
        $this->authorizeResource(Equipment::class, 'equipment'); //
    }

    /**
     * Display a listing of the equipment.
     * Users without Admin/BPM/IT roles will only see 'available' equipment.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $user = Auth::user(); // Get the authenticated user
        Log::info('EquipmentController@index: Fetching equipment list for general viewing.', [
            'user_id' => $user?->id, // Use null-safe operator if user could somehow be null despite middleware
            'filters_requested' => $request->all(),
        ]);

        $filters = [];
        // Populate filters from the request
        if ($request->filled('asset_type') && $request->asset_type !== 'all') {
            $filters['asset_type'] = $request->asset_type;
        }
        if ($request->filled('status') && $request->status !== 'all') { // Operational status
            $filters['status'] = $request->status;
        }
        if ($request->filled('condition_status') && $request->condition_status !== 'all') { // Physical condition status
            $filters['condition_status'] = $request->condition_status;
        }
        if ($request->filled('classification') && $request->classification !== 'all') {
            $filters['classification'] = $request->classification;
        }
        if ($request->filled('location_id') && $request->location_id !== 'all') {
            $filters['location_id'] = $request->location_id;
        }
        if ($request->filled('search')) {
            $filters['search'] = $request->search;
        }

        // Apply default filter for non-privileged users
        if ($user && !$user->hasAnyRole(['Admin', 'BPM Staff', 'IT Admin'])) {
            $filters['status'] = Equipment::STATUS_AVAILABLE; //
        }

        $equipmentPaginator = $this->equipmentService->getAllEquipment(
            filters: $filters,
            perPage: (int) $request->input('per_page', 15) // Ensure perPage is an int
        ); //

        $viewData = [
            'equipmentList' => $equipmentPaginator,
            'requestFilters' => $request->only(['asset_type', 'status', 'condition_status', 'classification', 'location_id', 'search']),
            'assetTypes' => Equipment::getAssetTypeOptions(), //
            'operationalStatuses' => Equipment::getStatusOptions(), //
            'conditionStatuses' => Equipment::getConditionStatusOptions(), //
            'classifications' => Equipment::getClassificationOptions(), //
             // Fetch locations for filter dropdown if a general location filter is desired
             // 'locations' => Location::where('is_active', true)->orderBy('name')->get(), //
        ];

        return view('equipment.index', $viewData);
    }

    /**
     * Display the specified equipment.
     *
     * @param \App\Models\Equipment $equipment Route model bound instance
     * @return \Illuminate\View\View
     */
    public function show(Equipment $equipment): View
    {
        Log::info(
            "EquipmentController@show: Displaying equipment ID {$equipment->id} details.",
            ['user_id' => Auth::id()]
        );

        // Eager load relationships required by the 'show' view.
        // Updated 'location' to 'definedLocation' to match Equipment model.
        $equipment->loadMissing([
            'creatorInfo',         // Assumes relation defined in Equipment model
            'updaterInfo',         // Assumes relation defined in Equipment model
            'definedLocation',     // Corrected relationship name
            'department',          //
            'equipmentCategory',   //
            'subCategory'          //
        ]);

        return view('equipment.show', compact('equipment'));
    }

    // Other CRUD methods (create, store, edit, update, destroy) are intentionally
    // omitted as this controller is scoped to 'index' and 'show' via routes.
}
