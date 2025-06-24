<?php

declare(strict_types=1);

namespace App\Livewire\ResourceManagement\MyApplications\Loan;

use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\ApprovalService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $searchTerm = '';

    public string $filterStatus = '';

    protected string $paginationTheme = 'bootstrap';

    protected int $perPage = 10;

    public ?int $selectedApplicationId = null;

    public bool $showApprovalActionModal = false;

    public ?string $approvalActionType = null;

    public string $approvalComments = '';

    protected ApprovalService $approvalService;

    protected array $rules = [
        'approvalComments' => '',
    ];

    protected array $messages = [
        'approvalComments.required' => 'Sila masukkan sebab penolakan.',
    ];

    public function boot(ApprovalService $approvalService): void
    {
        $this->approvalService = $approvalService;
    }

    public function mount(): void
    {
        $this->authorize('viewAny', LoanApplication::class);
        $pageTitle = $this->generatePageTitle();
        $this->dispatch('update-page-title', title: $pageTitle);
    }

    public function generatePageTitle(): string
    {
        $appName = __(config('variables.templateName', 'Sistem Pengurusan Sumber Bersepadu MOTAC'));

        return __('Senarai Permohonan Pinjaman ICT Saya').' - '.$appName;
    }

    public function getLoanApplicationsProperty()
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            Log::warning('MyApplications\Loan\Index: Unauthenticated access attempt.');

            return LoanApplication::whereRaw('1 = 0')->paginate($this->perPage);
        }

        $query = LoanApplication::where('user_id', $user->id)
            ->with([
                'user:id,name',
                'responsibleOfficer:id,name',
                'currentApprovalOfficer:id,name',
                'approvals.officer:id,name',
            ])
            ->orderBy('updated_at', 'desc');

        if ($this->searchTerm !== '' && $this->searchTerm !== '0') {
            $search = '%'.$this->searchTerm.'%';
            $query->where(function ($q) use ($search): void {
                if (is_numeric(str_replace('%', '', $search))) {
                    $q->orWhere('id', 'like', $search);
                }

                $q->orWhere('purpose', 'like', $search)
                    ->orWhere('location', 'like', 'search');
            });
        }

        if ($this->filterStatus !== '' && $this->filterStatus !== '0' && $this->filterStatus !== 'all' && $this->filterStatus !== '') {
            $query->where('status', $this->filterStatus);
        }

        $applications = $query->paginate($this->perPage);

        // This `transform` block is the corrected logic. It ensures that we iterate
        // through the collection of applications and pass each individual model
        // to the checkCanActOnApplication method, resolving the warning.
        $applications->getCollection()->transform(function (LoanApplication $application): \App\Models\LoanApplication {
            $application->can_act_on = $this->checkCanActOnApplication($application);

            return $application;
        });

        return $applications;
    }

    public function getStatusOptionsProperty(): array
    {
        $options = LoanApplication::getStatusOptions() ?? [];

        return ['' => __('Semua Status')] + $options;
    }

    protected function checkCanActOnApplication(LoanApplication $application): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        return $user->can('recordDecision', $application);
    }

    public function showApproveModal(int $applicationId): void
    {
        $application = LoanApplication::findOrFail($applicationId);
        if (! $this->checkCanActOnApplication($application)) {
            session()->flash('error', __('Anda tidak dibenarkan untuk meluluskan permohonan ini.'));

            return;
        }

        $this->selectedApplicationId = $application->id;
        $this->approvalActionType = 'approve';
        $this->approvalComments = '';
        $this->resetValidation('approvalComments');
        $this->showApprovalActionModal = true;
        $this->dispatch('openModal', elementId: 'approvalActionModal');
    }

    public function showRejectModal(int $applicationId): void
    {
        $application = LoanApplication::findOrFail($applicationId);
        if (! $this->checkCanActOnApplication($application)) {
            session()->flash('error', __('Anda tidak dibenarkan untuk menolak permohonan ini.'));

            return;
        }

        $this->selectedApplicationId = $application->id;
        $this->approvalActionType = 'reject';
        $this->approvalComments = '';
        $this->resetValidation('approvalComments');
        $this->showApprovalActionModal = true;
        $this->dispatch('openModal', elementId: 'approvalActionModal');
    }

    public function updated($propertyName): void
    {
        if ($propertyName === 'approvalComments' && $this->approvalActionType === 'reject') {
            $this->validateOnly($propertyName, [
                'approvalComments' => ['required', 'string', 'min:10', 'max:500'],
            ], $this->messages);
        }
    }

    public function submitApprovalAction(): void
    {
        $this->rules['approvalComments'] = ($this->approvalActionType === 'reject')
            ? ['required', 'string', 'min:10', 'max:500']
            : ['nullable', 'string', 'max:500'];
        $this->validate($this->rules, $this->messages);

        $application = LoanApplication::find($this->selectedApplicationId);
        if (! $application) {
            session()->flash('error', __('Permohonan tidak ditemui.'));
            $this->closeApprovalActionModal();

            return;
        }

        $user = Auth::user();
        if (! $this->checkCanActOnApplication($application)) {
            session()->flash('error', __('Anda tidak lagi mempunyai kebenaran untuk tindakan ini.'));
            $this->closeApprovalActionModal();

            return;
        }

        $actionStatusForApprovalService = $this->approvalActionType === 'approve' ? Approval::STATUS_APPROVED : Approval::STATUS_REJECTED;

        try {
            $currentApprovalStageKey = $application->current_approval_stage ?? $application->status;
            $approvalTask = Approval::where('approvable_id', $application->id)
                ->where('approvable_type', $application->getMorphClass())
                ->where('stage', $currentApprovalStageKey)
                ->where('status', Approval::STATUS_PENDING)
                ->when(! $user->hasRole('Admin'), fn ($query) => $query->where('officer_id', $user->id))
                ->first();

            if (! $approvalTask && $user->hasRole('Admin')) {
                $approvalTask = Approval::where('approvable_id', $application->id)
                    ->where('approvable_type', $application->getMorphClass())
                    ->where('stage', $currentApprovalStageKey)
                    ->where('status', Approval::STATUS_PENDING)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            if (! $approvalTask) {
                session()->flash('error', __('Tiada tugasan kelulusan aktif yang sesuai ditemui untuk diproses.'));
                $this->closeApprovalActionModal();

                return;
            }

            $this->approvalService->processApprovalDecision($approvalTask, $actionStatusForApprovalService, $user, $this->approvalComments);
            session()->flash('success', __('Tindakan kelulusan telah berjaya direkodkan.'));
        } catch (AuthorizationException $e) {
            session()->flash('error', $e->getMessage());
        } catch (\Exception $e) {
            session()->flash('error', __('Gagal memproses tindakan kelulusan. Ralat: ').$e->getMessage());
        }

        $this->closeApprovalActionModal();
    }

    public function closeApprovalActionModal(): void
    {
        $this->showApprovalActionModal = false;
        $this->selectedApplicationId = null;
        $this->approvalActionType = null;
        $this->approvalComments = '';
        $this->resetValidation();
        $this->dispatch('closeModal', elementId: 'approvalActionModal');
    }

    #[On('deleteLoanApplication')]
    public function deleteLoanApplication(string $id): void
    {
        try {
            $application = LoanApplication::findOrFail($id);

            $this->authorize('delete', $application);

            $application->delete();

            $this->dispatch('toastr', type: 'success', message: 'Permohonan draf #'.$id.' telah berjaya dipadam.');

        } catch (AuthorizationException $e) {
            $this->dispatch('toastr', type: 'error', message: 'Anda tidak mempunyai kebenaran untuk memadam permohonan ini.');
        } catch (\Exception $e) {
            Log::error('Failed to delete loan application: '.$e->getMessage());
            $this->dispatch('toastr', type: 'error', message: 'Gagal memadam permohonan tersebut.');
        }
    }

    public function updatingSearchTerm(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['searchTerm', 'filterStatus']);
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.resource-management.my-applications.loan.index', [
            'applications' => $this->loanApplications,
            'statusOptions' => $this->statusOptions,
        ]);
    }
}
