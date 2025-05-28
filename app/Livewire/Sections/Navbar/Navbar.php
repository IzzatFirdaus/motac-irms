<?php

namespace App\Livewire\Sections\Navbar;

use App\Models\Import; // For progress bar feature [cite: 1]
use App\Models\User;   // For type hinting Auth::user() [cite: 1]
use Illuminate\Support\Collection; // For unreadNotifications type [cite: 1]
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Navbar extends Component
{
    public Collection $unreadNotifications;
    public bool $activeProgressBar = false; // Related to import progress feature [cite: 1]
    public int $percentage = 0;

    public function mount(): void
    {
        $this->unreadNotifications = collect(); // Initialize [cite: 1]
        $this->refreshNotifications(); // [cite: 1]
        $this->updateProgressBar(); // Call to check progress bar state on mount [cite: 1]
    }

    public function render(): View
    {
        return view('livewire.sections.navbar.navbar'); // [cite: 1]
    }

    #[On('refreshNotifications')] // [cite: 1]
    public function refreshNotifications(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user(); // [cite: 1]
        if ($user) {
            // Limit the number of notifications fetched for performance
            $this->unreadNotifications = $user->unreadNotifications()->take(10)->get(); // [cite: 1]
        } else {
            $this->unreadNotifications = collect(); // [cite: 1]
        }
    }

    // Import progress bar logic.
    // Ensure 'activeProgressBar' event is dispatched from your import process if this is used.
    #[On('activeProgressBar')] // Listens for the event to update [cite: 1]
    public function updateProgressBar(): void
    {
        $import_data = Import::latest()->first(); // [cite: 1]
        if ($import_data && $import_data->status == 'processing') { // [cite: 1]
            $this->activeProgressBar = true; // [cite: 1]
            if ($import_data->total > 0) { // [cite: 1]
                $this->percentage = (int) round($import_data->current / ($import_data->total / 100)); // [cite: 1]
            } else {
                $this->percentage = 0; // Avoid division by zero [cite: 1]
            }
        } else {
            if ($this->activeProgressBar && $import_data && $import_data->status == 'completed') { // [cite: 1]
                // Example: Dispatch a toastr notification. Ensure you have a system to handle this.
                // $this->dispatch('toastr', ['type' => 'success', 'message' => __('Imported Successfully!')]);
            }
            $this->percentage = ($import_data && $import_data->status == 'completed') ? 100 : 0; // Show 100% if completed, else 0 [cite: 1]
            $this->activeProgressBar = false; // Reset [cite: 1]
        }
    }

    public function markNotificationAsRead(string $notificationId): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user(); // [cite: 1]
        if ($user) {
            $notification = $user->unreadNotifications()->where('id', $notificationId)->first(); // [cite: 1]
            if ($notification) {
                $notification->markAsRead(); // [cite: 1]
                $this->dispatch('refreshNotifications')->self(); // Refresh after marking as read [cite: 1]
            }
        }
    }

    public function markAllNotificationsAsRead(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user(); // [cite: 1]
        if ($user) {
            $user->unreadNotifications->markAsRead(); // [cite: 1]
            $this->dispatch('refreshNotifications')->self(); // Refresh after marking all as read [cite: 1]
        }
    }
}
