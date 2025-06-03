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
use App\Models\Position; // Ensure this is imported

// Corresponding Policies
use App\Policies\ApprovalPolicy;
use App\Policies\EmailApplicationPolicy;
use App\Policies\EquipmentPolicy;
use App\Policies\GradePolicy;
use App\Policies\LoanApplicationPolicy;
use App\Policies\LoanTransactionPolicy;
use App\Policies\UserPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\PositionPolicy;   // Ensure this is imported

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
        //Department::class => DepartmentPolicy::class, // Uncomment if DepartmentPolicy is created
        Position::class => PositionPolicy::class,     // Uncomment and add this line

        // Supporting Detail Models (often authorization is derived from parent)
        // LoanApplicationItem::class => LoanApplicationItemPolicy::class,
        // LoanTransactionItem::class => LoanTransactionItemPolicy::class,
        // EquipmentCategory::class => EquipmentCategoryPolicy::class,
        // SubCategory::class => SubCategoryPolicy::class,
        // Location::class => LocationPolicy::class,

        // System Utility Models
        // Setting::class => SettingPolicy::class,
        // Import::class => ImportPolicy::class,
        // CustomNotification::class => NotificationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Implicitly grant users with the 'Admin' role all permissions.
        // This must match the exact role name used in your Spatie setup (e.g., seeded roles).
        // System Design Reference: Admin override is implemented via Gate::before in AuthServiceProvider.php.
        Gate::before(function (User $user, string $ability) {
            // The hasRole check is provided by the Spatie\Permission\Traits\HasRoles trait on the User model.
            // Ensure 'Admin' is the standardized role name as per System Design.
            if ($user->hasRole('Admin')) {
                return true; // Admin can perform any action
            }
            return null; // Important: return null to allow other policies or gates to define abilities
        });

        // Define any other global gates here if needed.
    }
}
