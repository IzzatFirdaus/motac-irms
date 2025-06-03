<?php

namespace App\Livewire\ResourceManagement\Approval;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Contracts\View\View; // Standard type hint
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
use Carbon\Carbon; // Added: Import the Carbon class

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
        $appName = __(config('variables.templateName', 'Sistem Pengurusan Sumber Bersepadu MOTAC'));
        return __('Sejarah Kelulusan Saya') . ' - ' . $appName;
    }

    #[Computed(persist: false)]
    public function approvalHistory(): LengthAwarePaginator
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (!$user) {
            Log::warning('Livewire.Approval.History: User not authenticated.');
            return new LengthAwarePaginator([], 0, $this->perPage, $this->resolvePage());
        }

        // $this->authorize('viewHistory', Approval::class); // Optional: Policy check

        Log::debug('Livewire.Approval.History: Fetching approval history for user ID ' . $user->id, [
            'filters' => $this->only(['filterType', 'filterDecision', 'dateFrom', 'dateTo', 'search'])
        ]);

        $query = Approval::query()
            ->where('officer_id', $user->id)
            ->where('status', '!=', Approval::STATUS_PENDING) // History of actions taken
            ->with([
                'approvable' => function ($morphTo) {
                    $morphTo->morphWith([
                        EmailApplication::class => ['user:id,name,title'],
                        LoanApplication::class => ['user:id,name,title', 'applicationItems:id,loan_application_id,equipment_type,quantity_approved'],
                    ]);
                },
                'officer:id,name,title',
            ]);

        if ($this->filterType !== 'all') {
            $morphMap = \Illuminate\Database\Eloquent\Relations\Relation::morphMap();
            $modelClass = $morphMap[$this->filterType] ?? ('App\\Models\\' . ucfirst(str_replace('_', '', $this->filterType)));
            if (class_exists($modelClass) && is_subclass_of($modelClass, \Illuminate\Database\Eloquent\Model::class)) {
                $query->where('approvable_type', app($modelClass)->getMorphClass());
            } else {
                Log::warning('Livewire.Approval.History: Invalid filterType ' . $this->filterType);
            }
        }

        // Corrected: Approval::getStatuses() should not take arguments as per diagnostic.
        // Assuming Approval::getStatuses() returns an array of valid status values.
        if ($this->filterDecision !== 'all' && in_array($this->filterDecision, Approval::getStatuses())) {
            $query->where('status', $this->filterDecision);
        }

        if ($this->dateFrom) {
            try {
                $query->whereDate('approval_timestamp', '>=', Carbon::parse($this->dateFrom)->toDateString());
            } catch (\Exception $e) { Log::error('Invalid dateFrom for Approval History: ' . $this->dateFrom, ['exception' => $e]); }
        }
        if ($this->dateTo) {
            try {
                $query->whereDate('approval_timestamp', '<=', Carbon::parse($this->dateTo)->toDateString());
            } catch (\Exception $e) { Log::error('Invalid dateTo for Approval History: ' . $this->dateTo, ['exception' => $e]); }
        }

        if (trim($this->search) !== '') {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'like', $searchTerm)
                  ->orWhere('comments', 'like', $searchTerm)
                  ->orWhere('stage', 'like', $searchTerm)
                  ->orWhereHasMorph('approvable', [EmailApplication::class, LoanApplication::class],
                      function ($morphQ, $type) use ($searchTerm) {
                          $morphQ->where('id', 'like', $searchTerm);
                          if ($type === EmailApplication::class) {
                              $morphQ->orWhere('proposed_email', 'like', $searchTerm);
                          } elseif ($type === LoanApplication::class) {
                              $morphQ->orWhere('purpose', 'like', $searchTerm);
                          }
                          $morphQ->orWhereHas('user', function ($userQ) use ($searchTerm) {
                              $userQ->where('name', 'like', $searchTerm)
                                    ->orWhere('personal_email', 'like', $searchTerm);
                          });
                          if ($type === LoanApplication::class) {
                              $morphQ->orWhereHas('applicationItems.equipment', function ($itemQ) use ($searchTerm) {
                                  $itemQ->where('tag_id', 'like', $searchTerm)
                                        ->orWhere('model', 'like', $searchTerm) // Assuming equipment model has 'model'
                                        ->orWhere('brand', 'like', $searchTerm); // Assuming equipment model has 'brand'
                              });
                          }
                      }
                  );
            });
        }

        return $query->orderBy('updated_at', 'desc')->paginate($this->perPage);
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['filterType', 'filterDecision', 'dateFrom', 'dateTo', 'search'])) {
            $this->resetPage();
            Log::debug("Livewire.Approval.History: Filter '{$propertyName}' updated. Pagination reset.");
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
