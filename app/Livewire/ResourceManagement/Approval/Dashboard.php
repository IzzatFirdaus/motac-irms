<?php

// File: app/Livewire/ResourceManagement/Approval/Dashboard.php

declare(strict_types=1);

namespace App\Livewire\ResourceManagement\Approval;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\ApprovalService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;
use Illuminate\Support\Facades\Route;

/**
 * Livewire Component for the Approver's Dashboard.
 * Displays pending approval tasks and allows officers to record decisions.
 * System Design Ref: 6.2 (Approver Dashboard), 9.4 (Approval Workflow Module)
 */
#[Layout('layouts.app')]
class Dashboard extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    // --- Filters ---
    #[Url(keep: true, history: true, as: 'status_kelulusan')]
    public string $filterStatus = Approval::STATUS_PENDING;

    #[Url(keep: true, history: true, as: 'jenis_permohonan')]
    public string $filterType = 'all';

    #[Url(keep: true, history: true, as: 'carian')]
    public string $search = '';

    // --- Approval Modal State ---
    public bool $showApprovalModal = false;
    public ?int $currentApprovalId = null;
    public ?string $approvalDecision = null;
    public ?string $approvalComments = null;
    public array $approvalItems = [];

    protected string $paginationTheme = 'bootstrap';
    protected int $perPage = 10;

    public function title(): string
    {
        // ADJUSTED: Using a consistent key from the approvals language file.
        return __('approvals.title') . ' - ' . config('app.name', 'MOTAC IRMS');
    }

    #[Computed(persist: false)]
    public function approvalTasks(): LengthAwarePaginator
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (!$user) {
            Log::warning('Livewire.Approval.Dashboard: User not authenticated. Cannot fetch approval tasks.');
            return new LengthAwarePaginator([], 0, $this->perPage, $this->resolvePage());
        }

        $query = Approval::query()
            ->where('officer_id', $user->id)
            ->with([
                'approvable' => function ($morphTo): void {
                    $morphTo->morphWith([
                        EmailApplication::class => ['user:id,name,personal_email,title'],
                        LoanApplication::class => ['user:id,name,personal_email,title', 'loanApplicationItems:id,loan_application_id,equipment_type,quantity_requested,quantity_approved'],
                    ]);
                },
            ]);

        if ($this->filterStatus !== 'all' && in_array($this->filterStatus, Approval::getStatuses())) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterType !== 'all') {
            $morphMap = \Illuminate\Database\Eloquent\Relations\Relation::morphMap();
            $modelClass = $morphMap[$this->filterType] ?? ('App\\Models\\' . ucfirst(str_replace('_', '', $this->filterType)));

            if (class_exists($modelClass) && is_subclass_of($modelClass, \Illuminate\Database\Eloquent\Model::class)) {
                $query->where('approvable_type', app($modelClass)->getMorphClass());
            } else {
                Log::warning(sprintf("Livewire.Approval.Dashboard: Invalid filterType '%s'. Resolved class '%s' is not valid.", $this->filterType, $modelClass));
            }
        }

        if (trim($this->search) !== '') {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($searchTerm): void {
                $q->where('id', 'like', $searchTerm)
                    ->orWhere('stage', 'like', $searchTerm)
                    ->orWhereHasMorph('approvable', [EmailApplication::class, LoanApplication::class],
                        function ($morphQ, $type) use ($searchTerm): void {
                            $morphQ->where('id', 'like', $searchTerm);
                            if ($type === EmailApplication::class) {
                                $morphQ->orWhere('proposed_email', 'like', $searchTerm);
                            } elseif ($type === LoanApplication::class) {
                                $morphQ->orWhere('purpose', 'like', $searchTerm);
                            }

                            $morphQ->orWhereHas('user', function ($userQ) use ($searchTerm): void {
                                $userQ->where('name', 'like', $searchTerm)
                                    ->orWhere('personal_email', 'like', $searchTerm);
                            });
                        }
                    );
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }

    #[Computed]
    public function currentApprovalDetails(): ?Approval
    {
        if ($this->currentApprovalId === null || $this->currentApprovalId === 0) {
            return null;
        }

        try {
            /** @var Approval|null $approval */
            $approval = Approval::with([
                'approvable' => function ($morphTo): void {
                    $morphTo->morphWith([
                        EmailApplication::class => ['user:id,name,service_status,title,department_id,position_id,grade_id', 'user.department:id,name', 'user.position:id,name', 'user.grade:id,name'],
                        LoanApplication::class => ['user:id,name,title,department_id,position_id,grade_id', 'user.department:id,name', 'user.position:id,name', 'user.grade:id,name', 'loanApplicationItems'],
                    ]);
                },
                'officer:id,name',
            ])->find($this->currentApprovalId);

            if (!$approval) {
                Log::warning('Livewire.Approval.Dashboard: currentApprovalId set but approval not found: ' . $this->currentApprovalId);
                $this->dispatch('closeApprovalModalEvent');
                session()->flash('error_toast', __('approvals.notifications.not_found'));
            }

            return $approval;
        } catch (Throwable $e) {
            Log::error('Livewire.Approval.Dashboard: Error fetching current approval details: ' . $e->getMessage(), ['exception' => $e]);
            $this->dispatch('closeApprovalModalEvent');
            session()->flash('error_toast', __('approvals.notifications.load_error'));

            return null;
        }
    }

    #[Computed]
    public function currentApprovable()
    {
        return $this->currentApprovalDetails?->approvable;
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['filterStatus', 'filterType', 'search'])) {
            $this->resetPage();
        }
    }

    public function openApprovalModal(int $approvalId): void
    {
        $this->currentApprovalId = $approvalId;
        $this->reset(['approvalDecision', 'approvalComments', 'approvalItems']);
        $this->resetValidation();

        $currentApproval = $this->currentApprovalDetails;
        if ($currentApproval && $currentApproval->approvable instanceof LoanApplication) {
            $this->approvalItems = $currentApproval->approvable->loanApplicationItems->map(function ($item): array {
                return [
                    'loan_application_item_id' => $item->id,
                    'equipment_type' => $item->equipment_type,
                    'quantity_requested' => $item->quantity_requested,
                    'quantity_approved' => $item->quantity_approved ?? $item->quantity_requested,
                ];
            })->toArray();
        }

        $this->showApprovalModal = true;
        $this->dispatch('showApprovalModalEvent');
        Log::debug(sprintf('Livewire.Approval.Dashboard: Opening modal for Approval ID %d.', $approvalId));
    }

    public function recordDecision(ApprovalService $approvalService): void
    {
        $currentApproval = $this->currentApprovalDetails;
        if (!$currentApproval) {
            session()->flash('error_toast', __('approvals.notifications.task_unavailable'));
            $this->dispatch('closeApprovalModalEvent');
            return;
        }

        $validatedData = $this->validate();

        try {
            $this->authorize('update', $currentApproval);
            /** @var User $user */
            $user = Auth::user();
            if (!$user) {
                throw new \RuntimeException(__('approvals.notifications.unauthenticated'));
            }

            $itemQuantitiesPayload = null;
            if ($currentApproval->approvable instanceof LoanApplication &&
                $validatedData['approvalDecision'] === Approval::STATUS_APPROVED &&
                isset($validatedData['approvalItems'])) {
                $itemQuantitiesPayload = collect($validatedData['approvalItems'])->map(function (array $item): array {
                    return [
                        'loan_application_item_id' => $item['loan_application_item_id'],
                        'quantity_approved' => $item['quantity_approved'],
                    ];
                })->toArray();
            }

            $approvalService->processApprovalDecision(
                $currentApproval,
                $validatedData['approvalDecision'],
                $user,
                $validatedData['approvalComments'] ?? null,
                $itemQuantitiesPayload
            );

            session()->flash('success', __('approvals.notifications.decision_recorded', ['id' => $currentApproval->id]));
            Log::info(sprintf("Livewire.Approval.Dashboard: Decision '%s' recorded for Approval ID %s by User ID %d.", $validatedData['approvalDecision'], $currentApproval->id, $user->id));
            $this->dispatch('closeApprovalModalEvent');

        } catch (AuthorizationException $e) {
            session()->flash('error', __('approvals.notifications.unauthorized'));
            Log::warning(sprintf('Livewire.Approval.Dashboard: AuthorizationException for Approval ID %s. User ID: %s. Error: %s', $currentApproval->id, $user->id, $e->getMessage()));
            $this->dispatch('closeApprovalModalEvent');
        } catch (Throwable $e) {
            session()->flash('error', __('approvals.notifications.generic_error') . $e->getMessage());
            Log::error(sprintf('Livewire.Approval.Dashboard: Throwable error recording decision for Approval ID %s. Error: %s', $currentApproval->id, $e->getMessage()), ['exception' => $e]);
        }
    }

    #[On('closeApprovalModalEvent')]
    public function closeApprovalModal(): void
    {
        $this->showApprovalModal = false;
        $this->currentApprovalId = null;
        $this->reset(['approvalDecision', 'approvalComments', 'approvalItems']);
        $this->resetValidation();
        Log::debug('Livewire.Approval.Dashboard: Approval modal closed and state reset by event.');
    }

    public function render(): View
    {
        return view('livewire.resource-management.approval.dashboard');
    }

    protected function rules(): array
    {
        $rules = [
            'approvalDecision' => ['required', Rule::in(array_keys(Approval::getDecisionStatuses()))],
            'approvalComments' => Rule::when(
                $this->approvalDecision === Approval::STATUS_REJECTED,
                ['required', 'string', 'min:10', 'max:2000'],
                ['nullable', 'string', 'max:2000']
            ),
        ];

        if ($this->currentApprovalDetails && $this->currentApprovalDetails->approvable instanceof LoanApplication && $this->approvalDecision === Approval::STATUS_APPROVED) {
            $rules['approvalItems'] = ['present', 'array'];
            foreach ($this->approvalItems as $index => $itemArray) {
                $maxQty = $itemArray['quantity_requested'] ?? 0;
                $rules['approvalItems.' . $index . '.loan_application_item_id'] = ['required', 'integer'];
                $rules['approvalItems.' . $index . '.quantity_approved'] = ['required', 'integer', 'min:0', 'max:' . $maxQty];
            }
        }
        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            'approvalDecision.required' => __('approvals.validation.decision_required'),
            'approvalDecision.in' => __('approvals.validation.decision_invalid'),
            'approvalComments.required' => __('approvals.validation.comments_required'),
            'approvalComments.min' => __('approvals.validation.comments_min'),
            'approvalItems.array' => __('approvals.validation.items_invalid'),
        ];

        if ($this->currentApprovalDetails && $this->currentApprovalDetails->approvable instanceof LoanApplication && $this->approvalDecision === Approval::STATUS_APPROVED) {
            foreach ($this->approvalItems as $index => $itemArray) {
                $itemTypeDisplay = $itemArray['equipment_type'] ?? 'Item ' . ($index + 1);
                $maxQty = $itemArray['quantity_requested'] ?? 0;
                $messages['approvalItems.' . $index . '.quantity_approved.required'] = __('approvals.validation.quantity_required', ['itemType' => $itemTypeDisplay]);
                $messages['approvalItems.' . $index . '.quantity_approved.integer'] = __('approvals.validation.quantity_integer', ['itemType' => $itemTypeDisplay]);
                $messages['approvalItems.' . $index . '.quantity_approved.min'] = __('approvals.validation.quantity_min', ['itemType' => $itemTypeDisplay]);
                $messages['approvalItems.' . $index . '.quantity_approved.max'] = __('approvals.validation.quantity_max', ['itemType' => $itemTypeDisplay, 'max' => $maxQty]);
            }
        }
        return $messages;
    }

    public function getViewApplicationRoute(Approval $approvalTask): ?string
    {
        $approvable = $approvalTask->approvable;
        if (!$approvable || !$approvable->id) {
            return null;
        }

        $routeName = null;
        $routeParams = [];

        if ($approvable instanceof LoanApplication) {
            $routeName = 'loan-applications.show';
            $routeParams = ['loan_application' => $approvable->id];
        } elseif ($approvable instanceof EmailApplication) {
            $routeName = 'email-applications.show';
            $routeParams = ['email_application' => $approvable->id];
        }

        if ($routeName && Route::has($routeName)) {
            try {
                return route($routeName, $routeParams);
            } catch (\Exception $e) {
                Log::error('Error generating getViewApplicationRoute: ' . $e->getMessage(), ['routeName' => $routeName, 'params' => $routeParams]);
                return null;
            }
        }
        return null;
    }
}
