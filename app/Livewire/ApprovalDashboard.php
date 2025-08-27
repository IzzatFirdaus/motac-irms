<?php

namespace App\Livewire;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Services\ApprovalService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

#[Layout('layouts.app')]
#[Title('Papan Pemuka Kelulusan')]
class ApprovalDashboard extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $filterType = 'all';

    public string $searchTerm = '';

    public string $filterStatus = Approval::STATUS_PENDING;

    public bool $showApprovalActionModal = false;

    public ?Approval $selectedApproval = null;

    public string $decision = '';

    public string $comments = '';

    protected string $paginationTheme = 'bootstrap';

    protected ApprovalService $approvalService;

    // Inject ApprovalService
    public function boot(ApprovalService $approvalService): void
    {
        $this->approvalService = $approvalService;
    }

    // Validation rules for the approval action modal
    protected function rules(): array
    {
        return [
            'decision' => [
                'required',
                'string',
                Rule::in([Approval::STATUS_APPROVED, Approval::STATUS_REJECTED]),
            ],
            'comments' => [
                Rule::when($this->decision === Approval::STATUS_REJECTED, ['required', 'string', 'min:10']),
                'nullable', 'string', 'max:1000',
            ],
        ];
    }

    protected array $messages = [
        'decision.required' => 'Sila pilih keputusan (Lulus/Tolak).',
        'decision.in'       => 'Keputusan yang dipilih tidak sah.',
        'comments.required' => 'Sila masukkan komen untuk keputusan Penolakan.',
        'comments.min'      => 'Komen mestilah sekurang-kurangnya 10 aksara.',
        'comments.max'      => 'Komen tidak boleh melebihi 1000 aksara.',
    ];

    /**
     * Computed property to get approval tasks for the current user.
     */
    #[Computed]
    public function getApprovalTasksProperty(): LengthAwarePaginator
    {
        $user = Auth::user();
        if (! $user || ! $user->can('view_approvals')) {
            return new LengthAwarePaginator([], 0, 10);
        }

        $query = Approval::where('officer_id', $user->id)
            ->with([
                'approvable' => function ($morphTo): void {
                    $morphTo->morphWith([
                        LoanApplication::class => ['user:id,name,department_id', 'user.department:id,name', 'loanApplicationItems'],
                    ]);
                },
            ]);

        // Filter by status
        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        // Filter by application type
        if ($this->filterType !== 'all') {
            $query->where('approvable_type', $this->filterType);
        }

        // Search
        if ($this->searchTerm !== '') {
            $searchTerm = '%' . trim($this->searchTerm) . '%';
            $query->where(function (Builder $q) use ($searchTerm): void {
                $q->whereHasMorph('approvable', [LoanApplication::class], function (Builder $morphQuery) use ($searchTerm): void {
                    $morphQuery->where('application_no', 'like', $searchTerm)
                        ->orWhereHas('user', function (Builder $userQuery) use ($searchTerm): void {
                            $userQuery->where('name', 'like', $searchTerm);
                        });
                });
            });
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate(10);
    }

    #[Computed]
    public function getStatusOptionsProperty(): array
    {
        return [
            'all'                     => 'Semua Status',
            Approval::STATUS_PENDING  => Approval::$STATUSES_LABELS[Approval::STATUS_PENDING],
            Approval::STATUS_APPROVED => Approval::$STATUSES_LABELS[Approval::STATUS_APPROVED],
            Approval::STATUS_REJECTED => Approval::$STATUSES_LABELS[Approval::STATUS_REJECTED],
        ];
    }

    #[Computed]
    public function getTypeOptionsProperty(): array
    {
        return [
            'all'                  => 'Semua Jenis Permohonan',
            LoanApplication::class => 'Permohonan Pinjaman Peralatan ICT',
        ];
    }

    /**
     * Show the modal for approval action.
     */
    public function openApprovalActionModal(int $approvalId): void
    {
        try {
            $user     = Auth::user();
            $approval = Approval::where('officer_id', $user->id)
                ->where('status', Approval::STATUS_PENDING)
                ->findOrFail($approvalId);

            $this->authorize('approveOrReject', $approval);

            $this->selectedApproval = $approval;
            $this->decision         = '';
            $this->comments         = '';
            $this->resetErrorBag();

            $this->showApprovalActionModal = true;
            $this->dispatch('openApprovalActionModalEvent');
        } catch (AuthorizationException $e) {
            Log::warning('ApprovalDashboard: Authorization failed for openApprovalActionModal.', ['approval_id' => $approvalId, 'user_id' => Auth::id(), 'error' => $e->getMessage()]);
            $this->dispatch('toastr', type: 'error', message: __('Anda tidak dibenarkan untuk melihat butiran kelulusan ini.'));
            $this->closeModal();
        } catch (\Exception $e) {
            Log::error('ApprovalDashboard: Error showing action modal.', ['approval_id' => $approvalId, 'error' => $e->getMessage()]);
            $this->dispatch('toastr', type: 'error', message: __('Gagal memaparkan modal keputusan.'));
            $this->closeModal();
        }
    }

    /**
     * Handles the submit action from the approval modal.
     */
    public function submitDecision(): void
    {
        $this->validate();

        $user = Auth::user();
        try {
            if (! $this->selectedApproval || ! $user) {
                throw new \Exception('No approval selected or user not authenticated.');
            }

            $this->authorize('approveOrReject', $this->selectedApproval);

            $approvable = $this->selectedApproval->approvable;
            if (! ($approvable instanceof LoanApplication)) {
                throw new \Exception('Jenis permohonan tidak disokong untuk tindakan kelulusan.');
            }

            if ($this->decision === Approval::STATUS_APPROVED) {
                $this->approvalService->handleApprovedDecision(
                    $this->selectedApproval,
                    $approvable,
                    $this->comments
                );
                $this->dispatch('toastr', type: 'success', message: __('Keputusan Lulus berjaya direkodkan.'));
            } elseif ($this->decision === Approval::STATUS_REJECTED) {
                $this->approvalService->handleRejectedDecision(
                    $this->selectedApproval,
                    $approvable,
                    $this->comments
                );
                $this->dispatch('toastr', type: 'success', message: __('Keputusan Tolak berjaya direkodkan.'));
            } else {
                throw new \Exception('Invalid decision type provided.');
            }

            $this->closeModal();
            $this->refreshDataEvent();
        } catch (AuthorizationException $e) {
            Log::error('ApprovalDashboard: Authorization error on submitDecision.', ['message' => $e->getMessage(), 'user_id' => $user?->id]);
            $this->dispatch('toastr', type: 'error', message: __('Anda tidak dibenarkan untuk tindakan ini.'));
        } catch (Throwable $e) {
            Log::error('ApprovalDashboard: Error submitting decision.', ['exception' => $e, 'user_id' => $user?->id]);
            $this->dispatch('toastr', type: 'error', message: __('Gagal merekodkan keputusan: ') . $e->getMessage());
        }
    }

    #[On('close-modal')]
    public function closeModal(): void
    {
        $this->showApprovalActionModal = false;
        $this->selectedApproval        = null;
        $this->decision                = '';
        $this->comments                = '';
        $this->resetErrorBag();
        $this->dispatch('closeApprovalActionModalEvent');
    }

    #[On('refresh-approval-list')]
    public function refreshDataEvent(): void
    {
        unset($this->approvalTasks);
    }

    /**
     * Helper to get the URL for viewing the full application, based on the approvable type.
     */
    public function getViewApplicationRouteForSelected(): ?string
    {
        if (! $this->selectedApproval || ! $this->selectedApproval->approvable) {
            return null;
        }
        $approvable = $this->selectedApproval->approvable;
        if ($approvable instanceof LoanApplication) {
            return route('loan-applications.show', $approvable->id);
        }

        return null;
    }

    public function render(): View
    {
        return view('livewire.approval-dashboard');
    }
}
