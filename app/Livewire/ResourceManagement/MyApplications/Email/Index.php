<?php

namespace App\Livewire\ResourceManagement\MyApplications\Email;

use App\Models\EmailApplication;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
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

    public function mount(): void
    {
        $this->authorize('viewAny', EmailApplication::class);
    }

    public function getEmailApplicationsProperty()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return EmailApplication::whereRaw('1 = 0')->paginate(10);
        }

        $query = EmailApplication::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        if (!empty($this->searchTerm)) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('proposed_email', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('group_email', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('application_reason_notes', 'like', '%' . $this->searchTerm . '%');
            });
        }

        if (!empty($this->filterStatus) && $this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }
        return $query->paginate(10);
    }

    public function getStatusOptionsProperty(): array
    {
        return ['' => __('Semua Status')] + (EmailApplication::$STATUSES_LABELS ?? []);
    }

    public function updatingSearchTerm(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        // REMOVED ->title(...) chain
        return view('livewire.resource-management.my-applications.email.index', [
            'applications' => $this->emailApplications,
            'statusOptions' => $this->statusOptions,
        ]);
    }
}
