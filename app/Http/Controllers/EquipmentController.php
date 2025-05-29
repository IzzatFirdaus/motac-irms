<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Location;
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
        // Authorization for 'index' (viewAny) and 'show' (view) handled by EquipmentPolicy via authorizeResource
        $this->authorizeResource(Equipment::class, 'equipment');
    }

    /**
     * Display a listing of the equipment.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        Log::info('EquipmentController@index: Fetching equipment list for general viewing.', [
            'user_id' => $user?->id,
            'filters_requested' => $request->all(),
        ]);

        $filters = [];
        if ($request->filled('asset_type') && $request->asset_type !== 'all') {
            $filters['asset_type'] = $request->asset_type;
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $filters['status'] = $request->status;
        }
        if ($request->filled('condition_status') && $request->condition_status !== 'all') {
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

        // Non-privileged users only see 'available' equipment, unless they specifically filter status
        if ($user && !$user->hasAnyRole(['Admin', 'BPM Staff', 'IT Admin']) && !$request->filled('status')) {
            $filters['status'] = Equipment::STATUS_AVAILABLE;
        }

        $equipmentPaginator = $this->equipmentService->getAllEquipment(
            filters: $filters,
            perPage: (int) $request->input('per_page', config('pagination.default_size', 15))
        );

        $viewData = [
            'equipmentList' => $equipmentPaginator,
            'requestFilters' => $request->only(['asset_type', 'status', 'condition_status', 'classification', 'location_id', 'search']),
            'assetTypes' => Equipment::getAssetTypeOptions(),
            'operationalStatuses' => Equipment::getStatusOptions(),
            'conditionStatuses' => Equipment::getConditionStatusOptions(),
            'classifications' => Equipment::getClassificationOptions(),
            'locations' => Location::where('is_active', true)->orderBy('name')->get(['id', 'name']), // For filter dropdown
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
        // Authorization handled by authorizeResource
        Log::info(
            "EquipmentController@show: Displaying equipment ID {$equipment->id} details.",
            ['user_id' => Auth::id()]
        );

        $equipment->loadMissing([
            'creatorInfo:id,name',     // Ensure 'creatorInfo' relation exists
            'updaterInfo:id,name',     // Ensure 'updaterInfo' relation exists
            'definedLocation:id,name', // Relation name from Equipment model
            'department:id,name',
            'equipmentCategory:id,name',
            'subCategory:id,name'
        ]);

        return view('equipment.show', compact('equipment'));
    }
}
