<?php

namespace App\Livewire\ResourceManagement\Approval;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\User; // Ensure User model is imported
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class History extends Component
{
  use WithPagination;

  #[Url(keep: true)]
  public string $filterType = 'all'; // 'all', 'email_application', 'loan_application'

  #[Url(keep: true)]
  public string $filterDecision = 'all'; // 'all', Approval::STATUS_PENDING, Approval::STATUS_APPROVED, Approval::STATUS_REJECTED

  #[Url(keep: true)]
  public ?string $dateFrom = null;

  #[Url(keep: true)]
  public ?string $dateTo = null;

  #[Url(keep: true)]
  public string $search = '';

  protected string $paginationTheme = 'bootstrap';

  #[Computed]
  public function approvalHistory(): LengthAwarePaginator
  {
    $userId = Auth::id();

    if (!$userId) {
      Log::warning('Livewire.Approval.History: User not authenticated.');
      return new LengthAwarePaginator([], 0, $this->getPageName());
    }

    Log::debug(
      'Livewire.Approval.History: Fetching approval history for user ID ' .
        $userId .
        ' with filters: type=' .
        $this->filterType .
        ', decision=' .
        $this->filterDecision .
        ', from=' .
        ($this->dateFrom ?? 'N/A') .
        ', to=' .
        ($this->dateTo ?? 'N/A') .
        ', search=' .
        ($this->search ?? 'N/A')
    );

    $query = Approval::query()
      ->where(function ($q) use ($userId) {
        // User was the assigned officer or the one who made the final decision
        $q->where('officer_id', $userId)
          ->orWhere('approved_by', $userId) // Assuming approved_by and rejected_by are direct user IDs
          ->orWhere('rejected_by', $userId);
      })
      ->with([
        'approvable' => function ($morphTo) {
          $morphTo->morphWith([
            EmailApplication::class => [
              'user:id,name', // User who applied for email; use 'name' [cite: 353]
              // 'groupMembers', // If groupMembers relation exists and is needed for display
            ],
            LoanApplication::class => [
              'user:id,name', // User who applied for loan; use 'name' [cite: 353]
              'applicationItems.equipment:id,name,tag_id', // Load items and basic equipment info [cite: 318, 363]
            ],
          ]);
        },
        'officer:id,name', // User who was the designated officer for the stage
        'approvedBy:id,name', // User who approved (if applicable)
        'rejectedBy:id,name', // User who rejected (if applicable)
      ]);

    if ($this->filterType !== 'all') {
      // Assuming filterType values like 'email_application' or 'loan_application' which might be morph keys
      $morphMap = \Illuminate\Database\Eloquent\Relations\Relation::morphMap();
      $modelClass = $morphMap[$this->filterType] ?? $this->filterType;

      if (class_exists($modelClass) && is_subclass_of($modelClass, \Illuminate\Database\Eloquent\Model::class)) {
        $query->where('approvable_type', $modelClass);
      } else {
        Log::warning(
          'Livewire.Approval.History: Invalid filterType provided: ' .
            $this->filterType
        );
        $this->filterType = 'all'; // Reset to default or handle error
      }
    }

    if ($this->filterDecision !== 'all' && in_array($this->filterDecision, Approval::getStatuses())) {
      $query->where('status', $this->filterDecision);
    }

    if ($this->dateFrom) {
      $query->whereDate('approval_timestamp', '>=', $this->dateFrom); // Assuming approval_timestamp is the relevant date
    }
    if ($this->dateTo) {
      $query->whereDate('approval_timestamp', '<=', $this->dateTo);
    }

    if (trim($this->search) !== '') {
      $searchTerm = '%' . trim($this->search) . '%';
      $query->where(function ($q) use ($searchTerm) {
        $q->where('id', 'like', $searchTerm) // Search by Approval ID
          ->orWhere('comments', 'like', $searchTerm)
          ->orWhere('stage', 'like', $searchTerm)
          ->orWhereHasMorph(
            'approvable',
            [EmailApplication::class, LoanApplication::class],
            function ($morphQ, $type) use ($searchTerm) {
              $morphQ->where('id', 'like', $searchTerm); // Search by Approvable ID
              if ($type === EmailApplication::class) {
                $morphQ->orWhere('proposed_email', 'like', $searchTerm);
              } elseif ($type === LoanApplication::class) {
                $morphQ->orWhere('purpose', 'like', $searchTerm);
              }
              $morphQ->orWhereHas('user', function ($userQ) use ($searchTerm) {
                $userQ
                  ->where('name', 'like', $searchTerm) // Use 'name' for user [cite: 353]
                  ->orWhere('personal_email', 'like', $searchTerm);
              });
              if ($type === LoanApplication::class) {
                $morphQ->orWhereHas('applicationItems.equipment', function ($itemQ) use ($searchTerm) {
                  $itemQ
                    ->where('tag_id', 'like', $searchTerm) // equipment.tag_id [cite: 363]
                    ->orWhere('name', 'like', $searchTerm); // equipment.name (if equipment has a name, else model/brand) [cite: 363]
                });
              }
            }
          );
      });
    }

    return $query->orderBy('updated_at', 'desc')->paginate(10);
  }

  // Methods for filter updates trigger re-render and reset pagination
  public function updated($propertyName): void
  {
    if (in_array($propertyName, ['filterType', 'filterDecision', 'dateFrom', 'dateTo', 'search'])) {
      $this->resetPage();
      Log::debug(
        "Livewire.Approval.History: Filter '{$propertyName}' updated to '" . $this{
        $propertyName} . "'. Pagination reset."
      );
    }
  }

  public function resetFilters(): void
  {
    $this->reset(['filterType', 'filterDecision', 'dateFrom', 'dateTo', 'search']);
    $this->resetPage();
    Log::debug(
      'Livewire.Approval.History: Filters reset to defaults. Pagination reset.'
    );
  }

  public function render(): View
  {
    return view('livewire.resource-management.approval.history');
    // Example if using a specific layout for full-page components:
    // ->layout('layouts.app', ['title' => __('Approval History')]);
  }
}
