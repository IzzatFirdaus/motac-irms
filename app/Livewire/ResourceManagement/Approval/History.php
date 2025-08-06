<?php

namespace App\Livewire\ResourceManagement\Approval;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\User;
use Carbon\Carbon; // Standard type hint
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
use Livewire\WithPagination; // Added: Import the Carbon class

#[Layout('layouts.app')]
class History extends Component
{
    use AuthorizesRequests; // Assuming you might add authorization for viewing history
    use WithPagination;

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

    protected string $paginationTheme = 'bootstrap';

    protected int $perPage = 15;

    #[Title]
    public function pageTitle(): string
    {
        return __('Kelulusan Terdahulu');
    }

    public function mount(): void
    {
        // $this->authorize('viewAny', Approval::class); // Example authorization
        Log::info('Livewire.Approval.History: Initializing history component.', [
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);
    }

    #[Computed]
    public function getApprovalsProperty(): LengthAwarePaginator
    {
        $query = Approval::query()
            ->where('approver_id', Auth::id())
            ->with([
                'approvable',
                'approvable.user', // Eager load the applicant's user relationship for display
            ])
            ->when($this->filterType !== 'all', function ($query) {
                // Only filter by LoanApplication as EmailApplication is removed
                if ($this->filterType === 'loan_application') {
                    $query->where('approvable_type', LoanApplication::class);
                }
            })
            ->when($this->filterDecision !== 'all', function ($query) {
                $query->where('decision', $this->filterDecision);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', Carbon::parse($this->dateFrom)->startOfDay());
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', Carbon::parse($this->dateTo)->endOfDay());
            })
            ->when($this->search, function ($query) {
                $searchTerm = '%'.strtolower($this->search).'%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereHasMorph(
                        'approvable',
                        [LoanApplication::class], // Only search within LoanApplication
                        function ($morphQ, $type) use ($searchTerm): void {
                            // Search by purpose or applicant name/email for LoanApplication
                            $morphQ->whereRaw('LOWER(purpose) LIKE ?', [$searchTerm])
                                ->orWhereHas('user', function ($userQ) use ($searchTerm): void {
                                    $userQ->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                                        ->orWhereRaw('LOWER(email) LIKE ?', [$searchTerm]);
                                });
                            if ($type === LoanApplication::class) {
                                $morphQ->orWhereHas('loanApplicationItems.equipment', function ($itemQ) use ($searchTerm): void {
                                    $itemQ->where('tag_id', 'like', $searchTerm)
                                        ->orWhere('model', 'like', $searchTerm) // Assuming equipment model has 'model'
                                        ->orWhere('brand', 'like', $searchTerm); // Assuming equipment model has 'brand'
                                });
                            }
                        }
                    );
                });
            });

        return $query->orderBy('updated_at', 'desc')->paginate($this->perPage);
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['filterType', 'filterDecision', 'dateFrom', 'dateTo', 'search'])) {
            $this->resetPage();
            Log::debug(sprintf("Livewire.Approval.History: Filter '%s' updated. Pagination reset.", $propertyName));
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['filterType', 'filterDecision', 'dateFrom', 'dateTo', 'search']);
        $this->resetPage();
        Log::debug('Livewire.Approval.History: Filters reset to defaults.');
    }

    public function render(): View
    {
        return view('livewire.resource-management.approval.history');
    }
}
