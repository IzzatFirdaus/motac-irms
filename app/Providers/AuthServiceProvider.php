<?php

namespace App\Providers;

use App\Models\Approval;
use App\Models\Department;
use App\Models\Equipment;
use App\Models\Grade;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\Position;
use App\Models\User;
use App\Policies\ApprovalPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\EquipmentPolicy;
use App\Policies\GradePolicy;
use App\Policies\HelpdeskTicketPolicy;
use App\Policies\LoanApplicationPolicy;
use App\Policies\LoanTransactionPolicy;
use App\Policies\PositionPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

/**
 * Registers model-to-policy mappings and global authorization logic.
 * Updated for v4.0: Removes all EmailApplication references, adds HelpdeskTicketPolicy.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Approval::class        => ApprovalPolicy::class,
        Equipment::class       => EquipmentPolicy::class,
        Grade::class           => GradePolicy::class,
        LoanApplication::class => LoanApplicationPolicy::class,
        LoanTransaction::class => LoanTransactionPolicy::class,
        User::class            => UserPolicy::class,
        Department::class      => DepartmentPolicy::class,
        Position::class        => PositionPolicy::class,
        HelpdeskTicket::class  => HelpdeskTicketPolicy::class, // Helpdesk module policy mapping
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Grant all permissions to Admin users by default.
        Gate::before(function (User $user, string $ability): ?true {
            if ($user->hasRole('Admin')) {
                return true;
            }

            return null;
        });

        // BPM Staff can view equipment admin interface.
        Gate::define('view-equipment-admin', function (User $user): bool {
            return $user->hasRole('BPM Staff');
        });
    }
}
