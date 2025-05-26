<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\User;
use App\Services\EquipmentService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Added from suggestion

class EquipmentController extends Controller
{
    use AuthorizesRequests; // Added from suggestion

    protected EquipmentService $equipmentService;

    public function __construct(EquipmentService $equipmentService)
    {
        $this->equipmentService = $equipmentService;
        $this->middleware('auth');
        // $this->authorizeResource(Equipment::class, 'equipment');
    }

    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        $this->authorize('viewAny', Equipment::class);

        $filters = $request->only(['asset_type', 'brand', 'search_term']);
        $filters['status'] = Equipment::STATUS_AVAILABLE;

        $equipmentList = $this->equipmentService->getAllEquipment($filters);
        $assetTypes = Equipment::getAssetTypeOptions();

        return view('equipment.index', compact('equipmentList', 'assetTypes'));
    }

    public function show(Equipment $equipment): View
    {
        $this->authorize('view', $equipment);

        $equipment->load(['department', 'equipmentCategory', 'subCategory', 'definedLocation']);
        return view('equipment.show', compact('equipment'));
    }
}
