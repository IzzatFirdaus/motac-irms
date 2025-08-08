<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGradeRequest;
use App\Http\Requests\Admin\UpdateGradeRequest;  // Correctly referenced
use App\Models\Grade; // Correctly referenced
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class GradeController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:Admin']); // Grades management for Admins

        // System Design indicates GradePolicy exists
        $this->authorizeResource(Grade::class, 'grade');
    }

    /**
     * Display a listing of the grades for administration within Settings.
     * Route: settings.grades.index
     */
    public function index(): View
    {
        Log::info('Admin GradeController@index: Fetching grades for settings view.', ['admin_user_id' => Auth::id()]);
        // Eager-load the minApprovalGrade relationship to prevent N+1 issues / LazyLoadingViolationException
        $grades = Grade::with('minApprovalGrade')
            ->orderBy('level', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(config('pagination.default_size', 15));

        return view('admin.grades.index', ['grades' => $grades]);
    }

    /**
     * Show the form for creating a new grade.
     * Route: settings.grades.create
     */
    public function create(): View
    {
        // For 'min_approval_grade_id' dropdown
        // Renamed variable to gradesList to match usage in previously reviewed Blade files
        $gradesList = Grade::orderBy('level')->orderBy('name')->get(['id', 'name', 'level']);

        return view('admin.grades.create', ['gradesList' => $gradesList]);
    }

    /**
     * Store a newly created grade in storage.
     * Route: settings.grades.store
     */
    public function store(StoreGradeRequest $request): RedirectResponse
    {
        Log::info('Admin GradeController@store: Attempting to create grade.', ['admin_user_id' => Auth::id(), 'data' => $request->except(['_token'])]);
        try {
            Grade::create($request->validated());
            Log::info('Admin Grade created successfully.', ['admin_user_id' => Auth::id()]);

            return redirect()->route('settings.grades.index')
                ->with('success', __('Gred berjaya ditambah.'));
        } catch (\Exception $exception) {
            Log::error('Error storing grade by admin: '.$exception->getMessage(), ['exception_class' => get_class($exception), 'trace_snippet' => substr($exception->getTraceAsString(), 0, 250)]);

            return back()->with('error', __('Gagal menambah gred: ').$exception->getMessage())->withInput();
        }
    }

    /**
     * Display the specified grade.
     * Route: settings.grades.show
     */
    public function show(Grade $grade): View
    {
        // Eager-load minApprovalGrade relationship if it's accessed in the show view
        // Also load counts as per original code
        $grade->load(['minApprovalGrade', 'users', 'positions']);
        // The loadCount for users and positions should be $grade->loadCount(['users', 'positions']);
        // For clarity, if you need counts and the relationship:
        // $grade->load('minApprovalGrade')->loadCount(['users', 'positions']);

        return view('admin.grades.show', ['grade' => $grade]);
    }

    /**
     * Show the form for editing the specified grade.
     * Route: settings.grades.edit
     */
    public function edit(Grade $grade): View
    {
        // For 'min_approval_grade_id' dropdown, exclude the current grade itself
        // Renamed variable to gradesList to match usage in previously reviewed Blade files
        $gradesList = Grade::orderBy('level')->orderBy('name')->where('id', '!=', $grade->id)->get(['id', 'name', 'level']);

        return view('admin.grades.edit', ['grade' => $grade, 'gradesList' => $gradesList]);
    }

    /**
     * Store an updated grade in storage.
     * Route: settings.grades.update
     */
    public function update(UpdateGradeRequest $request, Grade $grade): RedirectResponse
    {
        Log::info(sprintf('Admin GradeController@update: Attempting to update grade ID: %d.', $grade->id), ['admin_user_id' => Auth::id(), 'data' => $request->except(['_token', '_method'])]);
        try {
            $grade->update($request->validated());
            Log::info(sprintf('Admin Grade ID: %d updated successfully.', $grade->id), ['admin_user_id' => Auth::id()]);

            return redirect()->route('settings.grades.index')
                ->with('success', __('Butiran gred berjaya dikemaskini.'));
        } catch (\Exception $exception) {
            Log::error(sprintf('Error updating grade ID %d by admin: ', $grade->id).$exception->getMessage(), ['exception_class' => get_class($exception), 'trace_snippet' => substr($exception->getTraceAsString(), 0, 250)]);

            return back()->with('error', __('Gagal mengemaskini gred: ').$exception->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified grade from storage.
     * Route: settings.grades.destroy
     */
    public function destroy(Grade $grade): RedirectResponse
    {
        Log::info(sprintf('Admin GradeController@destroy: Attempting to delete grade ID: %d.', $grade->id), ['admin_user_id' => Auth::id()]);
        try {
            if ($grade->users()->exists() || $grade->positions()->exists()) {
                return redirect()->route('settings.grades.index')
                    ->with('error', __('Gred tidak boleh dipadam kerana masih digunakan oleh pengguna atau jawatan.'));
            }

            $grade->delete();
            Log::info(sprintf('Admin Grade ID: %d deleted successfully.', $grade->id), ['admin_user_id' => Auth::id()]);

            return redirect()->route('settings.grades.index')
                ->with('success', __('Gred berjaya dipadam.'));
        } catch (\Exception $exception) {
            Log::error(sprintf('Error deleting grade ID %d by admin: ', $grade->id).$exception->getMessage(), ['exception_class' => get_class($exception), 'trace_snippet' => substr($exception->getTraceAsString(), 0, 250)]);

            return back()->with('error', __('Gagal memadam gred: ').$exception->getMessage());
        }
    }
}
