<?php

declare(strict_types=1);

namespace App\Livewire\ResourceManagement\Reports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * UserActivityReport Livewire component
 * Displays a report of user activity related to loan applications and approvals.
 */
#[Layout('layouts.app')]
class UserActivityReport extends Component
{
    use WithPagination;

    /** @var string Search term for user name or email */
    public string $search = '';

    /** @var int Pagination per page */
    public int $perPage = 15;

    /** @var string Pagination theme for Bootstrap */
    protected string $paginationTheme = 'bootstrap';

    /**
     * Computed property: paginated user list with activity counts,
     * filtered by search term.
     */
    public function getUsersProperty()
    {
        return User::query()
            ->with('department')
            ->withCount([
                'loanApplicationsAsApplicant',
                'approvalsAsApprover',
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->orderByDesc('loan_applications_as_applicant_count')
            ->paginate($this->perPage);
    }

    /**
     * Reset pagination when search input changes.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Render the user activity report Blade view.
     */
    public function render(): View
    {
        return view('livewire.resource-management.reports.user-activity-report', [
            'users' => $this->users,
        ]);
    }
}
