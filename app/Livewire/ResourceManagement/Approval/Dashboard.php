<?php

// File: app/Livewire/ResourceManagement/Approval/Dashboard.php

declare(strict_types=1);

namespace App\Livewire\ResourceManagement\Approval; // User: Confirm namespace

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\ApprovalService; // For processing approval decisions
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
// use Livewire\Attributes\Title; // The #[Title] attribute line will be commented out
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

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

    protected string $paginationTheme = 'bootstrap';
    protected int $perPage = 10;

    // The #[Title] attribute below is commented out
    // #[Title]
    public function title(): string
    {
        $appName = __(config('variables.templateName', 'Sistem Pengurusan Sumber Bersepadu MOTAC'));
        return __('Papan Pemuka Kelulusan') . ' - ' . $appName;
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
                'approvable' => function ($morphTo) {
                    $morphTo->morphWith([
                        EmailApplication::class => ['user:id,name,personal_email,title'],
                        LoanApplication::class => [
                            'user:id,name,personal_email,title',
                            'applicationItems:id,loan_application_id,equipment_type,quantity_requested',
                        ],
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
                Log::warning("Livewire.Approval.Dashboard: Invalid filterType '{$this->filterType}'. Resolved class '{$modelClass}' is not valid.");
            }
        }

        if (trim($this->search) !== '') {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'like', $searchTerm)
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
                      }
                  );
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }

    #[Computed]
    public function currentApprovalDetails(): ?Approval
    {
        if (!$this->currentApprovalId) return null;
        try {
            /** @var Approval|null $approval */
            $approval = Approval::with([
                'approvable' => function ($morphTo) {
                    $morphTo->morphWith([
                        EmailApplication::class => ['user:id,name,service_status,title', 'user.department:id,name', 'user.position:id,name', 'user.grade:id,name'],
                        LoanApplication::class => ['user:id,name,title', 'user.department:id,name', 'user.position:id,name', 'user.grade:id,name', 'applicationItems'],
                    ]);
                },
                'officer:id,name',
            ])->find($this->currentApprovalId);

            if (!$approval) {
                Log::warning('Livewire.Approval.Dashboard: currentApprovalId set but approval not found: ' . $this->currentApprovalId);
                $this->dispatch('closeApprovalModalEvent');
                session()->flash('error_toast', __('Tugasan kelulusan tidak ditemui atau telah dialih keluar.'));
            }
            return $approval;
        } catch (ModelNotFoundException $e) {
            Log::error('Livewire.Approval.Dashboard: Model not found for currentApprovalId: ' . $this->currentApprovalId, ['exception' => $e]);
            $this->dispatch('closeApprovalModalEvent');
            session()->flash('error_toast', __('Ralat memuatkan butiran kelulusan. Rekod mungkin tidak wujud.'));
            return null;
        } catch (Throwable $e) {
            Log::error('Livewire.Approval.Dashboard: Error fetching current approval details: ' . $e->getMessage(), ['exception' => $e]);
            $this->dispatch('closeApprovalModalEvent');
            session()->flash('error_toast', __('Berlaku ralat tidak dijangka semasa memuatkan butiran kelulusan.'));
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
        $this->reset(['approvalDecision', 'approvalComments']);
        $this->resetValidation();
        $this->showApprovalModal = true;
        $this->dispatch('showApprovalModalEvent');
        Log::debug("Livewire.Approval.Dashboard: Opening modal for Approval ID {$approvalId}.");
    }

    public function recordDecision(ApprovalService $approvalService): void
    {
        $currentApproval = $this->currentApprovalDetails;
        if (!$currentApproval) {
            session()->flash('error_toast', __('Tugasan kelulusan tidak lagi tersedia. Sila muat semula.'));
            $this->dispatch('closeApprovalModalEvent');
            return;
        }

        $validatedData = $this->validate();

        try {
            $this->authorize('update', $currentApproval);
            /** @var User $user */
            $user = Auth::user();
            if (!$user) throw new \RuntimeException(__('Pengguna yang disahkan tidak ditemui.'));

            $approvalService->processApprovalDecision(
                $currentApproval,
                $validatedData['approvalDecision'],
                $user,
                $validatedData['approvalComments'] ?? null
            );

            session()->flash('success', __('Keputusan berjaya direkodkan untuk tugasan #:id.', ['id' => $currentApproval->id]));
            Log::info("Livewire.Approval.Dashboard: Decision '{$validatedData['approvalDecision']}' recorded for Approval ID {$currentApproval->id} by User ID {$user->id}.");
            $this->dispatch('closeApprovalModalEvent');
        } catch (AuthorizationException $e) {
            session()->flash('error', __('Anda tidak dibenarkan untuk melakukan tindakan ini.'));
            Log::warning("Livewire.Approval.Dashboard: AuthorizationException for Approval ID {$currentApproval->id}. User ID: {$user->id}. Error: {$e->getMessage()}");
            $this->dispatch('closeApprovalModalEvent');
        } catch (Throwable $e) {
            session()->flash('error', __('Berlaku ralat semasa merekodkan keputusan: ') . $e->getMessage());
            Log::error("Livewire.Approval.Dashboard: Throwable error recording decision for Approval ID {$currentApproval->id}. Error: {$e->getMessage()}", ['exception' => $e]);
        }
    }

    #[On('closeApprovalModalEvent')]
    public function closeApprovalModal(): void
    {
        $this->showApprovalModal = false;
        $this->currentApprovalId = null;
        $this->reset(['approvalDecision', 'approvalComments']);
        $this->resetValidation();
        Log::debug('Livewire.Approval.Dashboard: Approval modal closed and state reset by event.');
    }

    public function render(): View
    {
        return view('livewire.resource-management.approval.dashboard');
    }

    protected function rules(): array
    {
        return [
            'approvalDecision' => ['required', Rule::in(array_keys(Approval::getDecisionStatuses()))],
            'approvalComments' => Rule::when(
                $this->approvalDecision === Approval::STATUS_REJECTED,
                ['required', 'string', 'min:10', 'max:2000'],
                ['nullable', 'string', 'max:2000']
            ),
        ];
    }

    public function messages(): array
    {
        return [
            'approvalDecision.required' => __('Sila pilih keputusan (Lulus/Tolak).'),
            'approvalDecision.in' => __('Pilihan keputusan tidak sah.'),
            'approvalComments.required' => __('Ulasan diperlukan jika permohonan ditolak.'),
            'approvalComments.min' => __('Ulasan mesti sekurang-kurangnya :min aksara.'),
        ];
    }
}
