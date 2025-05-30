<?php

namespace App\Livewire\Sections\Navbar;

use App\Models\Import;
use App\Models\User; // Ensure User model is imported if not already
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
  protected string $localeConfigKey = 'app.available_locales';

  public function mount(): void
  {
    $this->unreadNotifications = collect();
    $this->refreshNotifications();
    $this->updateProgressBar();

    $this->canViewAdminSettings = Auth::check() && Auth::user()->hasRole('Admin');

    // CORRECTED: Process available locales to include a 'flag_code'
    $configuredLocales = config($this->localeConfigKey, []);
    $processedLocales = [];

    foreach ($configuredLocales as $localeKey => $properties) {
        $countryCode = 'default'; // Default flag code if one cannot be determined
        if (!empty($properties['regional'])) {
            $parts = explode('_', $properties['regional']);
            if (count($parts) === 2) {
                $countryCode = strtolower($parts[1]); // Extracts 'my' from 'ms_MY', 'us' from 'en_US'
            }
        }
        // You might want a more specific fallback if countryCode remains 'default' or empty
        // For instance, use the first two letters of the localeKey if it's a valid country code,
        // or a generic globe icon. For now, 'us' is used as a fallback in getLocaleFlagIcon.

        $processedLocales[$localeKey] = $properties; // Keep original properties
        $processedLocales[$localeKey]['flag_code'] = $countryCode ?: 'us'; // Add the derived country code, fallback to 'us'
    }
    $this->availableLocales = $processedLocales;

    // Initialize activeTheme if not already set (e.g., by a parent component or direct property binding)
    if (is_null($this->activeTheme) && class_exists(\App\Helpers\Helpers::class)) {
        $configData = \App\Helpers\Helpers::appClasses();
        $this->activeTheme = $configData['style'] ?? 'light';
    } else if (is_null($this->activeTheme)) {
        $this->activeTheme = 'light'; // Absolute fallback
    }
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
      ? $user->unreadNotifications()->latest()->take(10)->get()
      : collect();
  }

  #[On('activeProgressBar')]
  public function updateProgressBar(): void
  {
    $import = Import::latest()->first();

    if ($import && $import->status === 'processing') {
      $this->activeProgressBar = true;
      $this->percentage = $import->total_rows > 0
        ? (int) round($import->processed_rows / ($import->total_rows / 100))
        : 0;
    } else {
      if ($this->activeProgressBar && $import && $import->status === 'completed') {
        // $this->dispatch('toastr', ['type' => 'success', 'message' => __('Import completed successfully!')]);
      }
      $this->percentage = $import && ($import->status === 'completed' || $import->status === 'failed') ? 100 : 0;
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
        $this->refreshNotifications();
      }
    }
  }

  public function markAllNotificationsAsRead(): void
  {
    $user = Auth::user();
    if ($user) {
      $user->unreadNotifications->markAsRead();
      $this->refreshNotifications();
    }
  }

  public function handleNotificationClick(string $notificationId, ?string $link = null): void
  {
    $this->markNotificationAsRead($notificationId);
    if ($link && $link !== '#') {
      redirect()->to($link);
    }
  }

  // CORRECTED: Helper to get just the country code for the flag icon
  public function getLocaleFlagIcon(string $locale): string
  {
    // $this->availableLocales should be populated by mount() with 'flag_code'
    return $this->availableLocales[$locale]['flag_code'] ?? 'us'; // Default to 'us' (US flag code)
  }
}
