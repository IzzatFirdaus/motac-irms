<?php

namespace App\Livewire\Sections\Navbar;

use App\Models\Import;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Navbar extends Component
{
    public Collection $unreadNotifications;
    public bool $activeProgressBar = false;
    public int $percentage = 0;

    public string $defaultProfilePhotoUrl = '/assets/img/avatars/1.png';
    public string $profileShowRoute = '/profile/show';
    public string $adminSettingsRoute = '/admin/settings';
    public bool $canViewAdminSettings = false;

    public string $containerNav = 'container-fluid';
    public string $navbarDetachedClass = '';
    public bool $navbarFull = true;
    public bool $navbarHideToggle = false;
    public ?string $activeTheme = null;

    public array $availableLocales = [];
    protected string $localeConfigKey = 'locales'; // Optional config path for locales

    public function mount(): void
    {
        $this->unreadNotifications = collect();
        $this->refreshNotifications();
        $this->updateProgressBar();

        $this->canViewAdminSettings = Auth::check() && Auth::user()->hasRole('Admin');

        // Load locales from config or default
        $this->availableLocales = config($this->localeConfigKey, [
            'en' => ['name' => 'English', 'flag_icon' => 'fi-gb', 'display' => true],
            'my' => ['name' => 'Bahasa Melayu', 'flag_icon' => 'fi-my', 'display' => true],
            'ar' => ['name' => 'العربية', 'flag_icon' => 'fi-sa', 'display' => true],
        ]);
    }

    public function render(): View
    {
        return view('livewire.sections.navbar.navbar');
    }

    #[On('refreshNotifications')]
    public function refreshNotifications(): void
    {
        $user = Auth::user();
        $this->unreadNotifications = $user
            ? $user->unreadNotifications()->take(10)->get()
            : collect();
    }

    #[On('activeProgressBar')]
    public function updateProgressBar(): void
    {
        $import = Import::latest()->first();

        if ($import && $import->status === 'processing') {
            $this->activeProgressBar = true;
            $this->percentage = $import->total > 0
                ? (int) round($import->current / ($import->total / 100))
                : 0;
        } else {
            if ($this->activeProgressBar && $import && $import->status === 'completed') {
                // Example:
                // $this->dispatch('toastr', ['type' => 'success', 'message' => __('Import completed')]);
            }
            $this->percentage = $import && $import->status === 'completed' ? 100 : 0;
            $this->activeProgressBar = false;
        }
    }

    public function markNotificationAsRead(string $notificationId): void
    {
        $user = Auth::user();
        if ($user) {
            $notification = $user->unreadNotifications()->where('id', $notificationId)->first();
            if ($notification) {
                $notification->markAsRead();
                $this->dispatch('refreshNotifications')->self();
            }
        }
    }

    public function markAllNotificationsAsRead(): void
    {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
            $this->dispatch('refreshNotifications')->self();
        }
    }

    public function handleNotificationClick(string $notificationId, string $link = '#'): void
    {
        $this->markNotificationAsRead($notificationId);
        redirect()->to($link);
    }

    public function getLocaleFlagIcon(string $locale): string
    {
        return $this->availableLocales[$locale]['flag_icon'] ?? 'fi-gl';
    }
}
