<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// Models for Policies - System Design Section 4 & 8.1
use App\Models\User;
use App\Models\Department; // Added based on System Design 9.1 implies User Management
use App\Models\Position;   // Added based on System Design 9.1
use App\Models\Grade;
use App\Models\EmailApplication;
use App\Models\Equipment;
use App\Models\EquipmentCategory; // Added if specific policies exist
use App\Models\SubCategory;       // Added if specific policies exist
use App\Models\Location;          // Added if specific policies exist
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem; // Added if specific policies exist
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem; // Added if specific policies exist
use App\Models\Approval;
// use App\Models\Setting; // If settings have policies
// use App\Models\Import;  // If imports have policies

// Policies - System Design Section 3.1, 8.1, 9
use App\Policies\UserPolicy;
use App\Policies\DepartmentPolicy; // Added
use App\Policies\PositionPolicy;   // Added
use App\Policies\GradePolicy;
use App\Policies\EmailApplicationPolicy;
use App\Policies\EquipmentPolicy;
use App\Policies\EquipmentCategoryPolicy; // Added
use App\Policies\SubCategoryPolicy;       // Added
use App\Policies\LocationPolicy;          // Added
use App\Policies\LoanApplicationPolicy;
use App\Policies\LoanApplicationItemPolicy; // Added
use App\Policies\LoanTransactionPolicy;
use App\Policies\LoanTransactionItemPolicy; // Added
use App\Policies\ApprovalPolicy;
// use App\Policies\SettingPolicy;
// use App\Policies\ImportPolicy;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     * System Design Reference: 3.3 AuthServiceProvider registers all model policies.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Core Application Models
        Approval::class => ApprovalPolicy::class,
        EmailApplication::class => EmailApplicationPolicy::class,
        Equipment::class => EquipmentPolicy::class,
        Grade::class => GradePolicy::class,
        LoanApplication::class => LoanApplicationPolicy::class,
        LoanTransaction::class => LoanTransactionPolicy::class,
        User::class => UserPolicy::class,

        // Organizational Structure Models (add if policies exist/are needed)
        //Department::class => DepartmentPolicy::class, // System Design 9.1
        //Position::class => PositionPolicy::class,     // System Design 9.1

        // Equipment Related Detail Models (add if policies exist/are needed)
        //EquipmentCategory::class => EquipmentCategoryPolicy::class,
        //SubCategory::class => SubCategoryPolicy::class,
        //Location::class => LocationPolicy::class,

        // Loan Application Detail Models (add if policies exist/are needed)
        //LoanApplicationItem::class => LoanApplicationItemPolicy::class,
        //LoanTransactionItem::class => LoanTransactionItemPolicy::class,

        // Other Models (add if policies exist/are needed)
        // Setting::class => SettingPolicy::class,
        // Import::class => ImportPolicy::class,
        // App\Models\Notification::class => NotificationPolicy::class, // If notifications need authorization
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Implicitly grant users with the 'Admin' role all permissions.
        // This must match the exact role name used in your Spatie setup.
        // System Design Reference: 3.3, 8.1 - Admin override.
        Gate::before(function ($user, $ability) {
            // Ensure 'Admin' is the standardized role name as per System Design 8.1
            if ($user instanceof User && $user->hasRole('Admin')) {
                return true;
            }
            return null; // Important: return null to allow other policies or gates to run
        });

        // Define any other global gates here if needed
        // Example:
        // Gate::define('view-telescope', function ($user) {
        //     return $user->hasRole('Admin');
        // });
    }
}
