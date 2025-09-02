<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StoreDepartmentRequest;
use App\Http\Requests\Admin\UpdateDepartmentRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:Admin|IT Admin']); // Restrict access to Admin and IT Admin roles
    }

    /**
     * Display a listing of the departments.
     */
    public function index(Request $request): View
    {
        Log::info('Admin\\DepartmentController@index: Fetching department list.', ['admin_user_id' => Auth::id()]);

        $departments = Department::query()
            ->search($request->string('search')->toString())
            ->orderBy('name')
            ->paginate(config('pagination.default_size', 15));

        return view('admin.departments.index', ['departments' => $departments]);
    }

    /**
     * Show the form for creating a new department.
     */
    public function create(): View
    {
        Log::info('Admin\\DepartmentController@create: Displaying create department form.', ['admin_user_id' => Auth::id()]);

        $branchTypes = Department::getBranchTypeOptions();
        $users       = User::orderBy('name')->get(['id', 'name']); // Fetch all users for the Head of Department dropdown

        return view('admin.departments.create', ['branchTypes' => $branchTypes, 'users' => $users]);
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        Log::info('Admin\\DepartmentController@store: Attempting to create a new department.', ['admin_user_id' => Auth::id()]);

    $validatedData = $request->validated();

        try {
            $validatedData['created_by'] = Auth::id();
            Department::create($validatedData);

            Log::info('Admin\\DepartmentController@store: Department created successfully.', ['admin_user_id' => Auth::id()]);

            return redirect()->route('admin.departments.index')
                ->with('success', __('Jabatan berjaya ditambah.'));
        } catch (\Exception $exception) {
            Log::error('Admin\\DepartmentController@store: Failed to create department.', [
                'exception_message' => $exception->getMessage(),
                'admin_user_id'     => Auth::id(),
            ]);

            return back()->with('error', __('Gagal untuk menambah jabatan.'))->withInput();
        }
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department): View
    {
        Log::info(sprintf('Admin\\DepartmentController@show: Displaying department ID: %d.', $department->id), ['admin_user_id' => Auth::id()]);

        // Load related data for better insights
        $department->load(['headOfDepartment', 'users']);

        return view('admin.departments.show', ['department' => $department]);
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department): View
    {
        Log::info(sprintf('Admin\\DepartmentController@edit: Displaying edit form for department ID: %d.', $department->id), ['admin_user_id' => Auth::id()]);

        $branchTypes = Department::getBranchTypeOptions();
        $users       = User::orderBy('name')->get(['id', 'name']); // Fetch all users for the Head of Department dropdown

        return view('admin.departments.edit', ['department' => $department, 'branchTypes' => $branchTypes, 'users' => $users]);
    }

    /**
     * Update the specified department in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        Log::info(sprintf('Admin\\DepartmentController@update: Attempting to update department ID: %d.', $department->id), ['admin_user_id' => Auth::id()]);

    $validatedData = $request->validated();

        try {
            $validatedData['updated_by'] = Auth::id();
            $department->update($validatedData);

            Log::info(sprintf('Admin\\DepartmentController@update: Department ID %d updated successfully.', $department->id), ['admin_user_id' => Auth::id()]);

            return redirect()->route('admin.departments.index')
                ->with('success', __('Jabatan berjaya dikemaskini.'));
        } catch (\Exception $exception) {
            Log::error(sprintf('Admin\\DepartmentController@update: Failed to update department ID %d.', $department->id), [
                'exception_message' => $exception->getMessage(),
                'admin_user_id'     => Auth::id(),
            ]);

            return back()->with('error', __('Gagal untuk mengemaskini jabatan.'))->withInput();
        }
    }

    /**
     * Remove the specified department from storage.
     */
    public function destroy(Department $department): RedirectResponse
    {
        Log::info(sprintf('Admin\\DepartmentController@destroy: Attempting to delete department ID: %d.', $department->id), ['admin_user_id' => Auth::id()]);

        try {
            $department->delete();
            Log::info(sprintf('Admin\\DepartmentController@destroy: Department ID %d deleted successfully.', $department->id), ['admin_user_id' => Auth::id()]);

            return redirect()->route('admin.departments.index')
                ->with('success', __('Jabatan berjaya dipadam.'));
        } catch (\Exception $exception) {
            Log::error(sprintf('Admin\\DepartmentController@destroy: Failed to delete department ID %d.', $department->id), [
                'exception_message' => $exception->getMessage(),
                'admin_user_id'     => Auth::id(),
            ]);

            return back()->with('error', __('Gagal untuk memadam jabatan.'));
        }
    }
}
