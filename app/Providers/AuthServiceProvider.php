<?php

namespace App\Providers;

// Core Models with Policies
use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\Equipment;
use App\Models\Grade;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
// Organizational Models that might have policies
use App\Models\Department;
use App\Models\Position;
// Supporting Models (uncomment if policies are created)
// use App\Models\EquipmentCategory;
// use App\Models\SubCategory;
// use App\Models\Location;
// use App\Models\LoanApplicationItem;
// use App\Models\LoanTransactionItem;
// use App\Models\Setting;
// use App\Models\Import;
// use App\Models\Notification as CustomNotification;

// Corresponding Policies
use App\Policies\ApprovalPolicy;
use App\Policies\EmailApplicationPolicy;
use App\Policies\EquipmentPolicy;
use App\Policies\GradePolicy;
use App\Policies\LoanApplicationPolicy;
use App\Policies\LoanTransactionPolicy;
use App\Policies\UserPolicy;
use App\Policies\DepartmentPolicy; // Uncomment if DepartmentPolicy is created
use App\Policies\PositionPolicy;   // Uncomment if PositionPolicy is created
// use App\Policies\EquipmentCategoryPolicy;
// use App\Policies\SubCategoryPolicy;
// use App\Policies\LocationPolicy;
// use App\Policies\LoanApplicationItemPolicy;
// use App\Policies\LoanTransactionItemPolicy;
// use App\Policies\SettingPolicy;
// use App\Policies\ImportPolicy;
// use App\Policies\NotificationPolicy;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     * System Design Reference: 3.3 AuthServiceProvider registers all model policies.
     * This array maps Eloquent models to their corresponding policy classes.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Core Application Models with Policies
        Approval::class => ApprovalPolicy::class,
        EmailApplication::class => EmailApplicationPolicy::class,
        Equipment::class => EquipmentPolicy::class,
        Grade::class => GradePolicy::class,
        LoanApplication::class => LoanApplicationPolicy::class,
        LoanTransaction::class => LoanTransactionPolicy::class,
        User::class => UserPolicy::class,

        // Organizational Structure Models
        // Uncomment and create policy if specific authorization rules are needed beyond general roles.
        // Department::class => DepartmentPolicy::class, // System Design [cite: 490] implies policy could exist
        // Position::class => PositionPolicy::class,     // System Design [cite: 490] implies policy could exist

        // Supporting Detail Models (often authorization is derived from parent)
        // LoanApplicationItem::class => LoanApplicationItemPolicy::class,
        // LoanTransactionItem::class => LoanTransactionItemPolicy::class,
        // EquipmentCategory::class => EquipmentCategoryPolicy::class,
        // SubCategory::class => SubCategoryPolicy::class, // Assuming SubCategory is an alias for App\Models\SubCategory
        // Location::class => LocationPolicy::class,       // Assuming Location is an alias for App\Models\Location

        // System Utility Models
        // Setting::class => SettingPolicy::class,
        // Import::class => ImportPolicy::class,
        // CustomNotification::class => NotificationPolicy::class, // If App\Models\Notification is aliased to CustomNotification
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Implicitly grant users with the 'Admin' role all permissions.
        // This must match the exact role name used in your Spatie setup (e.g., seeded roles).
        // System Design Reference: [cite: 56, 193, 340, 477] (Admin override).
        Gate::before(function (User $user, string $ability) {
            // The hasRole check is provided by the Spatie\Permission\Traits\HasRoles trait on the User model.
            // Ensure 'Admin' is the standardized role name as per System Design[cite: 8, 292, 576].
            if ($user->hasRole('Admin')) {
                return true; // Admin can perform any action
            }
            return null; // Important: return null to allow other policies or gates to define abilities
        });

        // Define any other global gates here if needed.
        // Example Gate for viewing system logs:
        // Gate::define('view-system-logs', function (User $user) {
        //     return $user->hasPermissionTo('view_system_logs'); // Requires Spatie permission
        // });
    }
}
