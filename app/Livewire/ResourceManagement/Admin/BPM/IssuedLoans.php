<?php

namespace App\Livewire\ResourceManagement\Admin\BPM;

use App\Models\LoanApplication;
use App\Models\LoanTransaction; // For type constant
use App\Models\User; // For type hinting
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')] // Ensure layouts.app is Bootstrap-compatible
class IssuedLoans extends Component
{
    use AuthorizesRequests, WithPagination;

    public string $searchTerm = '';
    protected string $paginationTheme = 'bootstrap'; // Converted to Bootstrap

    public function mount(): void
    {
        $this->authorize('viewAny', LoanApplication::class); // General permission for BPM staff
    }

    public function getIssuedLoansProperty() // Changed to a computed property
    {
        /** @var User $user */
        $user = Auth::user();
        // Assuming BPM staff can see all issued loans, not just their own.
        // If there's a department scope for BPM staff, it should be applied here.

        $query = LoanApplication::query()
            ->with([
                'user:id,name,department_id', // Select specific columns
                'user.department:id,name',
                'applicationItems.equipment:id,asset_type,brand,model,tag_id', // Also select specific columns
                'loanTransactions' => function ($query) { // Eager load issue transactions
                    $query->where('type', LoanTransaction::TYPE_ISSUE)
                          ->select('id', 'loan_application_id', 'transaction_date'); // Select only necessary columns
                }
            ])
            ->whereIn('status', [
                LoanApplication::STATUS_ISSUED,
                LoanApplication::STATUS_OVERDUE,
                LoanApplication::STATUS_PARTIALLY_ISSUED, // If partially issued loans are also considered "active" issues
            ]);

        if (!empty($this->searchTerm)) {
            $searchTerm = '%' . $this->searchTerm . '%';
            $query->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('id', 'like', $searchTerm)
                    ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                        $userQuery->where('name', 'like', $searchTerm);
                    })
                    ->orWhereHas('applicationItems.equipment', function ($equipmentQuery) use ($searchTerm) {
                        $equipmentQuery->where('tag_id', 'like', $searchTerm)
                                       ->orWhere('serial_number', 'like', $searchTerm);
                    });
            });
        }
        // Get the latest issue date for sorting
        $query->orderByDesc(
            LoanTransaction::select('transaction_date')
                ->whereColumn('loan_application_id', 'loan_applications.id')
                ->where('type', LoanTransaction::TYPE_ISSUE)
                ->latest('transaction_date')
                ->limit(1)
        );

        return $query->paginate(10);
    }

    public function updatingSearchTerm(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.resource-management.admin.bpm.issued-loans', [
            'issuedLoans' => $this->issuedLoans, // Access computed property
        ])->title(__('Senarai Pinjaman Dikeluarkan'));
    }
}
