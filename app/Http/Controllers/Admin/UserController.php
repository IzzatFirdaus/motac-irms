<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Grade;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // For assigning roles
use Illuminate\Validation\Rules\Password; // Added for logging
use Spatie\Permission\Models\Role;

// Explicitly import Auth facade

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Apply MOTAC-specific authorization, e.g., only 'Admin' role can manage users
        // $this->authorizeResource(User::class, 'user'); // Assumes UserPolicy is set up
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        // Add authorization check if not using authorizeResource
        // if (!Auth::user()->can('viewAny', User::class)) { // Use Auth::user() for clarity
        //     abort(403);
        // }
        Log::info('Admin UserController@index: Fetching users for administration view.', ['admin_user_id' => Auth::id()]);

        $query = User::with(['department', 'position', 'grade', 'roles'])
            ->search($request->string('search')->toString())
            ->filterDepartment($request->integer('department_id'))
            ->filterPosition($request->integer('position_id'))
            ->filterGrade($request->integer('grade_id'));

        if ($request->filled('role')) {
            $query->role($request->input('role'));
        }

        $users = $query->orderByName()->paginate(config('pagination.default_size', 15));

        $departments = Department::orderBy('name', 'asc')->get();
        $positions   = Position::orderBy('name', 'asc')->get();
        $grades      = Grade::orderBy('name', 'asc')->get();
        $roles       = Role::orderBy('name', 'asc')->get();

        return view('admin.users.index', ['users' => $users, 'departments' => $departments, 'positions' => $positions, 'grades' => $grades, 'roles' => $roles]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        Log::info('Admin UserController@create: Displaying create user form.', ['admin_user_id' => Auth::id()]);
        $departments = Department::orderBy('name', 'asc')->get();
        $positions   = Position::orderBy('name', 'asc')->get();
        $grades      = Grade::orderBy('name', 'asc')->get();
        $roles       = Role::orderBy('name', 'asc')->get();

        return view('admin.users.create', ['departments' => $departments, 'positions' => $positions, 'grades' => $grades, 'roles' => $roles]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::create([
            'name'                  => $validatedData['name'],
            'email'                 => $validatedData['email'],
            'identification_number' => $validatedData['identification_number'],
            'department_id'         => $validatedData['department_id'],
            'position_id'           => $validatedData['position_id'],
            'grade_id'              => $validatedData['grade_id'],
            'status'                => $validatedData['status'],
            'password'              => Hash::make($validatedData['password']),
        ]);

    if ($request->filled('roles')) {
            $user->assignRole($validatedData['roles']);
        }

        Log::info(sprintf('User ID: %d created successfully.', $user->id), ['admin_user_id' => Auth::id()]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        Log::info(sprintf('Admin UserController@show: Displaying user ID: %d.', $user->id), ['admin_user_id' => Auth::id()]);
        $user->load(['department', 'position', 'grade', 'roles']); // Eager load relationships

        return view('admin.users.show', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        Log::info(sprintf('Admin UserController@edit: Displaying edit form for user ID: %d.', $user->id), ['admin_user_id' => Auth::id()]);
        $departments = Department::orderBy('name', 'asc')->get();
        $positions   = Position::orderBy('name', 'asc')->get();
        $grades      = Grade::orderBy('name', 'asc')->get();
        $roles       = Role::orderBy('name', 'asc')->get();

        // Load current roles for the user
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('admin.users.edit', ['user' => $user, 'departments' => $departments, 'positions' => $positions, 'grades' => $grades, 'roles' => $roles, 'userRoles' => $userRoles]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validatedData = $request->validated();

        $userData = $validatedData;
        if (! empty($validatedData['password'])) {
            $userData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($userData['password']);
        }

        $user->update($userData);

        if ($request->filled('roles')) {
            $user->syncRoles($validatedData['roles']);
        } else {
            $user->syncRoles([]); // Remove all roles if none are provided
        }

        Log::info(sprintf('User ID: %d updated successfully.', $user->id), ['admin_user_id' => Auth::id()]);

        return redirect()->route('admin.users.show', $user)->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage (soft delete).
     */
    public function destroy(User $user)
    {
        // if (!Auth::user()->can('delete', $user)) {
        //     abort(403);
        // }
        Log::info(sprintf('Admin UserController@destroy: Attempting to delete user ID: %d.', $user->id), ['admin_user_id' => Auth::id()]);

        // Prevent deleting own account, or add specific logic if needed
        if ($user->id === Auth::id()) {
            Log::warning(sprintf('Admin User ID: %d attempted to delete own account (ID: %d).', Auth::id(), $user->id));

            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        try {
            $user->delete(); // Soft delete
            Log::info(sprintf('Admin User ID: %d deleted successfully (soft-delete).', $user->id), ['admin_user_id' => Auth::id()]);

            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $exception) {
            Log::error(sprintf('Error soft-deleting user ID %d by admin: ', $user->id).$exception->getMessage(), ['exception_class' => get_class($exception), 'trace_snippet' => substr($exception->getTraceAsString(), 0, 500)]);

            return back()->with('error', 'Failed to delete user: '.$exception->getMessage());
        }
    }
}
