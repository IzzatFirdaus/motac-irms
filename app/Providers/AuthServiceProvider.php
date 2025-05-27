<?php

namespace App\Providers;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\Equipment;
use App\Models\Grade;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User; // Assuming User model is in App\Models
use App\Policies\ApprovalPolicy;
use App\Policies\EmailApplicationPolicy;
use App\Policies\EquipmentPolicy;
use App\Policies\GradePolicy;
use App\Policies\LoanApplicationPolicy;
use App\Policies\LoanTransactionPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Approval::class => ApprovalPolicy::class,
        EmailApplication::class => EmailApplicationPolicy::class,
        Equipment::class => EquipmentPolicy::class,
        Grade::class => GradePolicy::class,
        LoanApplication::class => LoanApplicationPolicy::class,
        LoanTransaction::class => LoanTransactionPolicy::class,
        User::class => UserPolicy::class,
        // Add other model-policy mappings here as needed
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Implicitly grant "Admin" role all permission checks using can()
        // This aligns with the system design (Section 8.1)
        // and standardized role name 'Admin'.
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('Admin')) { // Standardized role name
                return true;
            }
            return null; // Important: return null to allow other policies or gates to run
        });
    }
}
