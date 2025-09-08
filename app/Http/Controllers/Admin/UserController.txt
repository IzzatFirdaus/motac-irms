<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role; // For assigning roles

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
    // if (!auth()->user()->can('viewAny', User::class)) {
    //     abort(403);
    // }

    $query = User::with(['department', 'position', 'grade', 'roles']);

    // Example Search Functionality
    if ($request->filled('search')) {
      $search = $request->input('search');
      $query->where(function ($q) use ($search) {
        $q->where('name', 'like', "%{$search}%")
          ->orWhere('email', 'like', "%{$search}%")
          ->orWhere('identification_number', 'like', "%{$search}%")
          ->orWhere('motac_email', 'like', "%{$search}%");
      });
    }

    $users = $query->orderBy('name')->paginate(15);
    return view('admin.users.index', compact('users')); // Ensure this view exists
  }

  /**
   * Show the form for creating a new user.
   */
  public function create()
  {
    // if (!auth()->user()->can('create', User::class)) {
    //     abort(403);
    // }

    $departments = Department::orderBy('name')->pluck('name', 'id');
    $positions = Position::orderBy('name')->pluck('name', 'id');
    $grades = Grade::orderBy('name')->pluck('name', 'id');
    $roles = Role::orderBy('name')->pluck('name', 'name'); // pluck name for both key and value for Spatie
    $serviceStatuses = User::$SERVICE_STATUS_LABELS;
    $appointmentTypes = User::$APPOINTMENT_TYPE_LABELS;
    $userStatuses = User::$STATUS_OPTIONS;

    return view('admin.users.create', compact(
      'departments',
      'positions',
      'grades',
      'roles',
      'serviceStatuses',
      'appointmentTypes',
      'userStatuses'
    )); // Ensure this view exists
  }

  /**
   * Store a newly created user in storage.
   */
  public function store(Request $request)
  {
    // if (!auth()->user()->can('create', User::class)) {
    //     abort(403);
    // }

    $validatedData = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users,email',
      'password' => ['required', 'confirmed', Password::defaults()],
      'title' => 'nullable|string|max:50',
      'identification_number' => 'nullable|string|max:20|unique:users,identification_number',
      'passport_number' => 'nullable|string|max:50|unique:users,passport_number',
      'mobile_number' => 'nullable|string|max:20',
      'personal_email' => 'nullable|string|email|max:255|unique:users,personal_email',
      'motac_email' => 'nullable|string|email|max:255|unique:users,motac_email',
      'user_id_assigned' => 'nullable|string|max:50|unique:users,user_id_assigned',
      'department_id' => 'nullable|exists:departments,id',
      'position_id' => 'nullable|exists:positions,id',
      'grade_id' => 'nullable|exists:grades,id',
      'level' => 'nullable|string|max:20', // Aras
      'service_status' => 'required|string|in:' . implode(',', array_keys(User::$SERVICE_STATUS_LABELS)),
      'appointment_type' => 'required|string|in:' . implode(',', array_keys(User::$APPOINTMENT_TYPE_LABELS)),
      'status' => 'required|string|in:' . implode(',', array_keys(User::$STATUS_OPTIONS)),
      'roles' => 'nullable|array',
      'roles.*' => 'exists:roles,name', // Ensure roles exist
    ]);

    $userData = $validatedData;
    $userData['password'] = Hash::make($validatedData['password']);
    $userData['email_verified_at'] = now(); // Optionally verify admin-created users by default

    $user = User::create($userData);

    if (!empty($validatedData['roles'])) {
      $user->assignRole($validatedData['roles']);
    }

    return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
  }

  /**
   * Display the specified user.
   */
  public function show(User $user)
  {
    // if (!auth()->user()->can('view', $user)) {
    //     abort(403);
    // }
    $user->load(['department', 'position', 'grade', 'roles']);
    return view('admin.users.show', compact('user')); // Ensure this view exists
  }

  /**
   * Show the form for editing the specified user.
   */
  public function edit(User $user)
  {
    // if (!auth()->user()->can('update', $user)) {
    //     abort(403);
    // }
    $user->load('roles');
    $departments = Department::orderBy('name')->pluck('name', 'id');
    $positions = Position::orderBy('name')->pluck('name', 'id');
    $grades = Grade::orderBy('name')->pluck('name', 'id');
    $roles = Role::orderBy('name')->pluck('name', 'name');
    $serviceStatuses = User::$SERVICE_STATUS_LABELS;
    $appointmentTypes = User::$APPOINTMENT_TYPE_LABELS;
    $userStatuses = User::$STATUS_OPTIONS;

    return view('admin.users.edit', compact(
      'user',
      'departments',
      'positions',
      'grades',
      'roles',
      'serviceStatuses',
      'appointmentTypes',
      'userStatuses'
    )); // Ensure this view exists
  }

  /**
   * Update the specified user in storage.
   */
  public function update(Request $request, User $user)
  {
    // if (!auth()->user()->can('update', $user)) {
    //     abort(403);
    // }
    $validatedData = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
      'title' => 'nullable|string|max:50',
      'identification_number' => 'nullable|string|max:20|unique:users,identification_number,' . $user->id,
      'passport_number' => 'nullable|string|max:50|unique:users,passport_number,' . $user->id,
      'mobile_number' => 'nullable|string|max:20',
      'personal_email' => 'nullable|string|email|max:255|unique:users,personal_email,' . $user->id,
      'motac_email' => 'nullable|string|email|max:255|unique:users,motac_email,' . $user->id,
      'user_id_assigned' => 'nullable|string|max:50|unique:users,user_id_assigned,' . $user->id,
      'department_id' => 'nullable|exists:departments,id',
      'position_id' => 'nullable|exists:positions,id',
      'grade_id' => 'nullable|exists:grades,id',
      'level' => 'nullable|string|max:20', // Aras
      'service_status' => 'required|string|in:' . implode(',', array_keys(User::$SERVICE_STATUS_LABELS)),
      'appointment_type' => 'required|string|in:' . implode(',', array_keys(User::$APPOINTMENT_TYPE_LABELS)),
      'status' => 'required|string|in:' . implode(',', array_keys(User::$STATUS_OPTIONS)),
      'roles' => 'nullable|array',
      'roles.*' => 'exists:roles,name',
      'password' => ['nullable', 'confirmed', Password::defaults()],
    ]);

    $userData = $validatedData;
    if (!empty($validatedData['password'])) {
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

    return redirect()->route('admin.users.show', $user)->with('success', 'User updated successfully.');
  }

  /**
   * Remove the specified user from storage (soft delete).
   */
  public function destroy(User $user)
  {
    // if (!auth()->user()->can('delete', $user)) {
    //     abort(403);
    // }

    // Prevent deleting own account, or add specific logic if needed
    if ($user->id === auth()->id()) {
      return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
    }

    $user->delete(); // Soft delete

    return redirect()->route('admin.users.index')->with('success', 'User deactivated successfully.');
  }

  // Add other methods like forceDelete, restore if needed for your admin panel
}
