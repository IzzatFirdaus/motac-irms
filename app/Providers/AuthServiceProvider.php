<?php

namespace App\Providers;

use App\Models\Approval;
use App\Models\Department;
use App\Models\EmailApplication; // For Gate::before type hinting
// Models for Policies - System Design Section 4 & 8.1
use App\Models\Equipment;
use App\Models\Grade;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\Position;
// use App\Models\EquipmentCategory;
// use App\Models\SubCategory;
// use App\Models\Location;
use App\Models\User;
// use App\Models\LoanApplicationItem;
use App\Policies\ApprovalPolicy;
// use App\Models\LoanTransactionItem;
use App\Policies\DepartmentPolicy;
// use App\Models\Setting;
// use App\Models\Import;

// Policies - System Design Section 3.1, 8.1, 9
use App\Policies\EmailApplicationPolicy;
use App\Policies\EquipmentPolicy;
use App\Policies\GradePolicy;
use App\Policies\LoanApplicationPolicy;
use App\Policies\LoanTransactionPolicy;
use App\Policies\PositionPolicy;
// use App\Policies\EquipmentCategoryPolicy;
// use App\Policies\SubCategoryPolicy;
// use App\Policies\LocationPolicy;
use App\Policies\UserPolicy;
// use App\Policies\LoanApplicationItemPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
// use App\Policies\LoanTransactionItemPolicy;
use Illuminate\Support\Facades\Gate;

// use App\Policies\SettingPolicy;
// use App\Policies\ImportPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     * System Design Reference: 3.3 AuthServiceProvider registers all model policies.
     * This array should map Eloquent models to their corresponding policy classes.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Core Application Models with Policies
        Approval::class => ApprovalPolicy::class,
        EmailApplication::class => EmailApplicationPolicy::class,
        Equipment::class => EquipmentPolicy::class,
        Grade::class => GradePolicy::class,           // For managing grades themselves
        LoanApplication::class => LoanApplicationPolicy::class,
        LoanTransaction::class => LoanTransactionPolicy::class,
        User::class => UserPolicy::class,             // For managing user profiles, CRUD operations on users

        // Organizational Structure Models (Uncomment and create policies if specific rules are needed beyond basic role checks)
        // Department::class => DepartmentPolicy::class, // System Design 9.1
        // Position::class => PositionPolicy::class,     // System Design 9.1

        // Detail Models (Uncomment if they need specific policies beyond their parent's policy or general role checks)
        // EquipmentCategory::class => EquipmentCategoryPolicy::class,
        // SubCategory::class => SubCategoryPolicy::class,
        // Location::class => LocationPolicy::class,
        // LoanApplicationItem::class => LoanApplicationItemPolicy::class,
        // LoanTransactionItem::class => LoanTransactionItemPolicy::class,

        // Other Models if they require specific authorization logic
        // Setting::class => SettingPolicy::class,
        // Import::class => ImportPolicy::class,
        // \App\Models\Notification::class => \App\Policies\NotificationPolicy::class, // Example
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Implicitly grant users with the 'Admin' role all permissions.
        // This must match the exact role name used in your Spatie setup (e.g., seeded roles).
        // System Design Reference: 3.3, 8.1 - Admin override.
        Gate::before(function (User $user, string $ability) {
            // Ensure 'Admin' is the standardized role name as per System Design 8.1
            // The hasRole check is provided by the Spatie\Permission\Traits\HasRoles trait on the User model.
            if ($user->hasRole('Admin')) { // Use the exact role name
                return true; // Admin can do anything
            }
            return null; // Important: return null to allow other policies or gates to run
        });

        // Define any other global gates here if needed.
        // Example:
        // Gate::define('view-system-logs', function (User $user) {
        //     return $user->hasPermissionTo('view system logs'); // Requires Spatie permission
        // });
    }
}
