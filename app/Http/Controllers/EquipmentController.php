<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Location;
use App\Models\User;
use App\Services\EquipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * EquipmentController.
 *
 * Handles requests related to viewing and managing equipment assets.
 * Applies authorization, filtering, and paginated listing of equipment.
 */
class EquipmentController extends Controller
{
    protected EquipmentService $equipmentService;

    /**
     * EquipmentController constructor.
     * Applies authentication middleware and resource-based authorization.
     */
    public function __construct(EquipmentService $equipmentService)
    {
        $this->middleware('auth');
        $this->equipmentService = $equipmentService;
        // Automatically applies EquipmentPolicy for resource methods (index, show, etc.)
        $this->authorizeResource(Equipment::class, 'equipment');
    }

    /**
     * Display a listing of the equipment, with filtering and pagination.
     */
    public function index(Request $request): View
    {
        /** @var User|null $user */
        $user = Auth::user();
        Log::info('EquipmentController@index: Fetching equipment list for general viewing.', [
            'user_id'           => $user?->id,
            'filters_requested' => $request->all(),
        ]);

        // Gather filters from the request; skip any with a value of 'all'
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
        if ($request->filled('equipment_category_id') && $request->equipment_category_id !== 'all') {
            $filters['equipment_category_id'] = $request->equipment_category_id;
        }
        if ($request->filled('sub_category_id') && $request->sub_category_id !== 'all') {
            $filters['sub_category_id'] = $request->sub_category_id;
        }
        if ($request->filled('location_id') && $request->location_id !== 'all') {
            $filters['location_id'] = $request->location_id;
        }
        if ($request->filled('search')) {
            $filters['search'] = $request->search;
        }

        // Non-privileged users should see only 'available' equipment unless they filter by status
        // Check that required methods/constants exist to avoid errors
        if (
            $user && method_exists($user, 'hasAnyRole') && ! $user->hasAnyRole(['Admin', 'BPM Staff', 'IT Admin']) && ! $request->filled('status')
        ) {
            if (defined(Equipment::class.'::STATUS_AVAILABLE')) {
                $filters['status'] = Equipment::STATUS_AVAILABLE;
            } else {
                Log::warning('Equipment::STATUS_AVAILABLE constant is not defined.');
                // Fallback handling if constant missing could be added here
            }
        }

        // Paginate equipment list based on filters and per-page setting
        $equipmentPaginator = $this->equipmentService->getAllEquipment(
            filters: $filters,
            perPage: (int) $request->input('per_page', config('pagination.default_size', 15))
        );

        // Prepare dropdown/filter options for the view
        $assetTypes          = Equipment::getAssetTypeOptions();
        $operationalStatuses = Equipment::getStatusOptions();
        $conditionStatuses   = Equipment::getConditionStatusesList();
        $classifications     = Equipment::getClassificationOptions();

        // Fetch all active equipment categories and their subcategories for filtering
        $equipmentCategories = EquipmentCategory::active()
            ->orderBy('name')
            ->with(['subCategories' => function ($q) {
                $q->where('is_active', true)->orderBy('name');
            }])
            ->get(['id', 'name']);

        $locations = Location::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $viewData = [
            'equipmentList'  => $equipmentPaginator,
            'requestFilters' => $request->only([
                'asset_type', 'status', 'condition_status', 'classification',
                'equipment_category_id', 'sub_category_id', 'location_id', 'search',
            ]),
            'assetTypes'          => $assetTypes,
            'operationalStatuses' => $operationalStatuses,
            'conditionStatuses'   => $conditionStatuses,
            'classifications'     => $classifications,
            'equipmentCategories' => $equipmentCategories,
            'locations'           => $locations,
        ];

        // Render the equipment listing view
        return view('equipment.index', $viewData);
    }

    /**
     * Display the specified equipment with relevant relationships loaded.
     */
    public function show(Equipment $equipment): View
    {
        // Authorization is handled by the authorizeResource() in the constructor
        Log::info(
            sprintf('EquipmentController@show: Displaying equipment ID %d details.', $equipment->id),
            ['user_id' => Auth::id()]
        );

        // Eager load related models to prevent N+1 query issues
        $equipment->loadMissing([
            'creator:id,name',
            'updater:id,name',
            'definedLocation:id,name',
            'department:id,name',
            'equipmentCategory:id,name',
            'subCategory:id,name',
            // Eager load loanTransactionItems and their nested relationships
            'loanTransactionItems.loanTransaction.loanApplication.user',
        ]);

        return view('equipment.show', ['equipment' => $equipment]);
    }
}
