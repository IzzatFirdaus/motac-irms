<?php

namespace App\Livewire\ResourceManagement\Approval;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * ApprovalHistory Livewire Component
 * Displays historical approval decisions made by the current user.
 * Allows filtering by application type, decision, date range, and search terms.
 *
 * Last updated: 2025-08-06 10:16:08 UTC by IzzatFirdaus
 */
#[Layout('layouts.app')]
class ApprovalHistory extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    // URL parameters for filters with meaningful Malay aliases
    #[Url(keep: true, history: true, as: 'jenis')]
    public string $filterType = 'all';

    #[Url(keep: true, history: true, as: 'keputusan')]
    public string $filterDecision = 'all';

    #[Url(keep: true, history: true, as: 'dari')]
    public ?string $dateFrom = null;

    #[Url(keep: true, history: true, as: 'hingga')]
    public ?string $dateTo = null;

    #[Url(keep: true, history: true, as: 'q')]
    public string $search = '';

    // Configuration properties
    protected string $paginationTheme = 'bootstrap';

    protected int $perPage = 15;

    /**
     * Dynamic page title for the browser tab.
     */
    #[Title]
    public function pageTitle(): string
    {
        return __('Sejarah Kelulusan');
    }

    /**
     * Component initialization - logs access for audit purposes.
     */
    public function mount(): void
    {
        // Optional: Add authorization check if needed
        // $this->authorize('viewAny', Approval::class);

        Log::info('ApprovalHistory: Component mounted by user.', [
            'user_id'    => Auth::id(),
            'user_name'  => Auth::user()?->name ?? 'Unknown',
            'ip_address' => request()->ip(),
            'timestamp'  => now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Computed property to fetch paginated approval history for the current user.
     * Includes comprehensive filtering and search capabilities.
     *
     * @return LengthAwarePaginator<Approval>
     */
    #[Computed]
    public function getApprovalsProperty(): LengthAwarePaginator
    {
        $currentUser = Auth::user();

        // Build the base query for approvals made by the current user
        $query = Approval::query()
            ->where('approver_id', $currentUser->id) // Filter by current approver
            ->with([
                'approvable', // Load the related application (LoanApplication, etc.)
                'approvable.user', // Load the applicant's user details for display
            ])
            // Filter by application type (loan_application only since email is removed)
            ->when($this->filterType !== 'all', function ($query) {
                if ($this->filterType === 'loan_application') {
                    $query->where('approvable_type', LoanApplication::class);
                }
                // Additional types can be added here in the future
            })
            // Filter by approval decision (approved, rejected, pending)
            ->when($this->filterDecision !== 'all', function ($query) {
                $query->where('decision', $this->filterDecision);
            })
            // Filter by date range - start date
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', Carbon::parse($this->dateFrom)->startOfDay());
            })
            // Filter by date range - end date
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', Carbon::parse($this->dateTo)->endOfDay());
            })
            // Advanced search functionality
            ->when($this->search, function ($query) {
                $searchTerm = '%'.strtolower($this->search).'%';
                $query->where(function ($q) use ($searchTerm) {
                    // Search within the polymorphic approvable relationship
                    $q->whereHasMorph(
                        'approvable',
                        [LoanApplication::class], // Only search within LoanApplication for now
                        function ($morphQ, $type) use ($searchTerm): void {
                            // Search by application purpose for LoanApplication
                            $morphQ->whereRaw('LOWER(purpose) LIKE ?', [$searchTerm])
                                // Search by applicant name or email
                                ->orWhereHas('user', function ($userQ) use ($searchTerm): void {
                                    $userQ->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                                        ->orWhereRaw('LOWER(email) LIKE ?', [$searchTerm]);
                                });

                            // Additional search for loan-specific items
                            if ($type === LoanApplication::class) {
                                $morphQ->orWhereHas('loanApplicationItems.equipment', function ($itemQ) use ($searchTerm): void {
                                    $itemQ->where('tag_id', 'like', $searchTerm)
                                        ->orWhere('model', 'like', $searchTerm)
                                        ->orWhere('brand', 'like', $searchTerm);
                                });
                            }
                        }
                    );
                });
            });

        // Order by most recent updates first
        $approvals = $query->orderBy('updated_at', 'desc')->paginate($this->perPage);

        // Log the query results for debugging and audit purposes
        Log::debug('ApprovalHistory: Query executed successfully.', [
            'user_id'         => $currentUser->id,
            'total_results'   => $approvals->total(),
            'current_page'    => $approvals->currentPage(),
            'filters_applied' => [
                'type'      => $this->filterType,
                'decision'  => $this->filterDecision,
                'date_from' => $this->dateFrom,
                'date_to'   => $this->dateTo,
                'search'    => $this->search,
            ],
        ]);

        return $approvals;
    }

    /**
     * Livewire lifecycle hook - triggered when any filter property is updated.
     * Resets pagination to ensure user sees results from the beginning.
     */
    public function updated($propertyName): void
    {
        // List of properties that should trigger pagination reset
        $filterProperties = ['filterType', 'filterDecision', 'dateFrom', 'dateTo', 'search'];

        if (in_array($propertyName, $filterProperties)) {
            $this->resetPage();
            Log::debug('ApprovalHistory: Filter updated and pagination reset.', [
                'property'  => $propertyName,
                'new_value' => $this->$propertyName,
                'user_id'   => Auth::id(),
            ]);
        }
    }

    /**
     * Reset all filters to their default values and refresh the results.
     * Useful for clearing complex filter combinations quickly.
     */
    public function resetFilters(): void
    {
        // Reset all filter properties to defaults
        $this->reset(['filterType', 'filterDecision', 'dateFrom', 'dateTo', 'search']);
        $this->resetPage(); // Reset pagination

        Log::info('ApprovalHistory: All filters reset to defaults.', [
            'user_id'   => Auth::id(),
            'timestamp' => now()->format('Y-m-d H:i:s'),
        ]);

        // Optional: Show success message to user
        $this->dispatch('toastr', type: 'info', message: __('Semua penapis telah dikembalikan ke tetapan asal.'));
    }

    /**
     * Get available filter options for the application type dropdown.
     * Returns array of type => label pairs.
     */
    public function getApplicationTypeOptionsProperty(): array
    {
        return [
            'all'              => __('Semua Jenis'),
            'loan_application' => __('Permohonan Pinjaman'),
            // Future application types can be added here
        ];
    }

    /**
     * Get available filter options for the decision dropdown.
     * Returns array of decision => label pairs.
     */
    public function getDecisionOptionsProperty(): array
    {
        return [
            'all'                     => __('Semua Keputusan'),
            Approval::STATUS_APPROVED => __('Diluluskan'),
            Approval::STATUS_REJECTED => __('Ditolak'),
            Approval::STATUS_PENDING  => __('Menunggu'),
        ];
    }

    /**
     * Export approval history to Excel/CSV format.
     * This method can be implemented in the future for reporting purposes.
     */
    public function exportHistory(): void
    {
        // TODO: Implement export functionality
        $this->dispatch('toastr', type: 'info', message: __('Fungsi eksport akan dilaksanakan tidak lama lagi.'));

        Log::info('ApprovalHistory: Export requested by user.', [
            'user_id' => Auth::id(),
            'filters' => [
                'type'      => $this->filterType,
                'decision'  => $this->filterDecision,
                'date_from' => $this->dateFrom,
                'date_to'   => $this->dateTo,
                'search'    => $this->search,
            ],
        ]);
    }

    /**
     * Render the approval history Blade view.
     */
    public function render(): View
    {
        return view('livewire.resource-management.approval.approval-history', [
            'approvals'              => $this->approvals, // Use the computed property
            'applicationTypeOptions' => $this->applicationTypeOptions,
            'decisionOptions'        => $this->decisionOptions,
        ]);
    }
}
