<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Grade;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // For assigning roles
use Illuminate\Validation\Rules\Password; // Added for logging
use Spatie\Permission\Models\Role; // Explicitly import Auth facade

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

        $query = User::with(['department', 'position', 'grade', 'roles']);

        // Example Search Functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('identification_number', 'like', "%{$search}%");
                // Removed 'motac_email' as per refactoring plan.
            });
        }

        // Example filtering by department, position, grade, or role
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->input('position_id'));
        }
        if ($request->filled('grade_id')) {
            $query->where('grade_id', $request->input('grade_id'));
        }
        if ($request->filled('role')) {
            $query->role($request->input('role')); // Assuming role scope is defined in User model
        }
        // Removed filtering by 'status' as per refactoring plan, relying on 'is_active' boolean
        // if ($request->filled('status')) {
        //     $query->where('status', $request->input('status'));
        // }

        $users = $query->orderBy('name', 'asc')->paginate(config('pagination.default_size', 15));

        $departments = Department::orderBy('name', 'asc')->get();
        $positions   = Position::orderBy('name', 'asc')->get();
        $grades      = Grade::orderBy('name', 'asc')->get();
        $roles       = Role::orderBy('name', 'asc')->get();

        return view('admin.users.index', compact('users', 'departments', 'positions', 'grades', 'roles'));
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

        return view('admin.users.create', compact('departments', 'positions', 'grades', 'roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255|unique:users',
            'identification_number' => 'nullable|string|max:255|unique:users',
            'department_id'         => 'required|exists:departments,id',
            'position_id'           => 'required|exists:positions,id',
            'grade_id'              => 'required|exists:grades,id',
            'is_active'             => 'required|boolean', // Added validation for is_active
            'roles'                 => 'nullable|array',
            'roles.*'               => 'exists:roles,name',
            'password'              => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name'                  => $validatedData['name'],
            'email'                 => $validatedData['email'],
            'identification_number' => $validatedData['identification_number'],
            'department_id'         => $validatedData['department_id'],
            'position_id'           => $validatedData['position_id'],
            'grade_id'              => $validatedData['grade_id'],
            'is_active'             => $validatedData['is_active'], // Assign is_active
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

        return view('admin.users.show', compact('user'));
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

        return view('admin.users.edit', compact('user', 'departments', 'positions', 'grades', 'roles', 'userRoles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'identification_number' => 'nullable|string|max:255|unique:users,identification_number,'.$user->id,
            'department_id'         => 'required|exists:departments,id',
            'position_id'           => 'required|exists:positions,id',
            'grade_id'              => 'required|exists:grades,id',
            'is_active'             => 'required|boolean', // Validate is_active
            'roles'                 => 'nullable|array',
            'roles.*'               => 'exists:roles,name',
            'password'              => ['nullable', 'confirmed', Password::defaults()],
        ]);

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
