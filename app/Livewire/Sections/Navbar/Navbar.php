<?php

namespace App\Livewire\Sections\Navbar;

use App\Models\Import; // For progress bar feature
use App\Models\User;   // For type hinting Auth::user()
use Illuminate\Support\Collection; // For unreadNotifications type
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB; // Not used after removing failed_jobs truncate
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Navbar extends Component
{
    public Collection $unreadNotifications;
    public bool $activeProgressBar = false; // Related to import progress feature
    public int $percentage = 0;

    public function mount(): void
    {
        $this->unreadNotifications = collect(); // Initialize
        $this->refreshNotifications();
        // $this->updateProgressBar(); // Call if progress bar should be checked on mount
    }

    public function render(): View
    {
        // No need to fetch notifications here again if mount and refreshNotifications handle it.
        // However, fetching here ensures it's always up-to-date on re-renders triggered by other actions.
        // For performance with Livewire, it's often better to refresh explicitly via events.
        // $this->refreshNotifications(); // Can be called here if needed on every render
        return view('livewire.sections.navbar.navbar');
    }

    #[On('refreshNotifications')]
    public function refreshNotifications(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user) {
            // Limit the number of notifications fetched for performance
            $this->unreadNotifications = $user->unreadNotifications()->take(10)->get();
        } else {
            $this->unreadNotifications = collect();
        }
    }

    // The import progress bar logic. Review if this is a core MOTAC navbar feature.
    // If kept, ensure 'activeProgressBar' event is dispatched from the import process.
    // #[On('activeProgressBar')]
    // public function updateProgressBar(): void
    // {
    //     $import_data = Import::latest()->first();
    //     if ($import_data && $import_data->status == 'processing') {
    //         $this->activeProgressBar = true;
    //         if ($import_data->total > 0) {
    //             $this->percentage = (int) round($import_data->current / ($import_data->total / 100));
    //         } else {
    //             $this->percentage = 0; // Avoid division by zero
    //         }
    //     } else {
    //         if ($this->activeProgressBar && $import_data && $import_data->status == 'completed') {
    //             // session()->flash('toastr', ['type' => 'success', 'message' => __('Imported Successfully!')]);
    //         }
    //         $this->percentage = 100; // Or 0 if no import is active
    //         $this->activeProgressBar = false; // Reset
    //     }
    // }

    public function markNotificationAsRead(string $notificationId): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user) {
            $notification = $user->unreadNotifications()->where('id', $notificationId)->first();
            if ($notification) {
                $notification->markAsRead();
                $this->dispatch('refreshNotifications')->self(); // Refresh after marking as read
            }
        }
    }

    public function markAllNotificationsAsRead(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
            $this->dispatch('refreshNotifications')->self(); // Refresh after marking all as read
        }
    }
}
