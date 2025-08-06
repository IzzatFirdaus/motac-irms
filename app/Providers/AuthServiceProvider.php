<?php

namespace App\Providers;

use App\Models\Approval;
use App\Models\Department;
use App\Models\Equipment;
use App\Models\Grade;
use App\Models\HelpdeskTicket; // Add this line
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\Position;
use App\Models\User;
use App\Policies\ApprovalPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\EquipmentPolicy;
use App\Policies\GradePolicy;
use App\Policies\HelpdeskTicketPolicy; // Add this line
use App\Policies\LoanApplicationPolicy;
use App\Policies\LoanTransactionPolicy;
use App\Policies\PositionPolicy;
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
        Equipment::class => EquipmentPolicy::class,
        Grade::class => GradePolicy::class,
        LoanApplication::class => LoanApplicationPolicy::class,
        LoanTransaction::class => LoanTransactionPolicy::class,
        User::class => UserPolicy::class,
        Department::class => DepartmentPolicy::class,
        Position::class => PositionPolicy::class,
        HelpdeskTicket::class => HelpdeskTicketPolicy::class, // Add this line
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function (User $user, string $ability): ?true {
            if ($user->hasRole('Admin')) {
                return true;
            }

            return null;
        });

        // --- FINAL, DEFINITIVE FIX ---
        // Explicitly define a gate for viewing the equipment admin index.
        // This removes all ambiguity in the test environment. The Gate::before
        // method already handles Admins, so this only needs to check for BPM Staff.
        Gate::define('view-equipment-admin', function (User $user): bool {
            return $user->hasRole('BPM Staff');
        });
    }
}
