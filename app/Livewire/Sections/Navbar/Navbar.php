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

  // These properties are defined but not directly used for route() generation in this component.
  // The Blade view handles its own route() calls.
  public string $defaultProfilePhotoUrl = '/assets/img/avatars/1.png'; // Default, can be overridden by Auth::user()->profile_photo_url
  public string $profileShowRoute = '/profile/show'; // Placeholder, Blade uses Route::has('profile.show')
  public string $adminSettingsRoute = '/admin/settings'; // Placeholder, Blade uses its own logic
  public bool $canViewAdminSettings = false;

  // Properties for navbar appearance, typically passed from a layout or config
  public string $containerNav = 'container-fluid';
  public string $navbarDetachedClass = ''; // Example: 'navbar-detached'
  public bool $navbarFull = true; // Example: true if it's the main full navbar
  public bool $navbarHideToggle = false;
  public ?string $activeTheme = null; // Will be set by App\Helpers\Helpers in Blade

  public array $availableLocales = [];
  // The key used to fetch locales from config. Can be 'app.available_locales' if structured that way.
  protected string $localeConfigKey = 'app.available_locales';

  public function mount(): void
  {
    $this->unreadNotifications = collect(); // Initialize as an empty collection
    $this->refreshNotifications();
    $this->updateProgressBar();

    $this->canViewAdminSettings = Auth::check() && Auth::user()->hasRole('Admin');

    // Load locales from config or provide a sensible default structure.
    // Ensure the config key 'app.available_locales' exists and is structured as expected by the Blade view.
    // Example structure: ['en' => ['name' => 'English', 'flag_icon' => 'us', 'display' => true], ...]
    $this->availableLocales = config($this->localeConfigKey, [
      'en' => ['name' => 'English', 'flag_icon_class' => 'fi-us', 'display' => true], // Assuming 'flag_icon_class' for CSS
      'my' => ['name' => 'Bahasa Melayu', 'flag_icon_class' => 'fi-my', 'display' => true],
      'ar' => ['name' => 'العربية', 'flag_icon_class' => 'fi-sy', 'display' => config('app.available_locales.ar.display', false)], // Use actual config for display
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
      ? $user->unreadNotifications()->latest()->take(10)->get() // Added latest() for order
      : collect();
  }

  #[On('activeProgressBar')]
  public function updateProgressBar(): void
  {
    $import = Import::latest()->first();

    if ($import && $import->status === 'processing') {
      $this->activeProgressBar = true;
      $this->percentage = $import->total_rows > 0 // Assuming 'total_rows' and 'processed_rows'
        ? (int) round($import->processed_rows / ($import->total_rows / 100))
        : 0;
    } else {
      if ($this->activeProgressBar && $import && $import->status === 'completed') {
        // Example: Dispatch a toastr notification for completion
        // $this->dispatch('toastr', ['type' => 'success', 'message' => __('Import completed successfully!')]);
      }
      $this->percentage = $import && ($import->status === 'completed' || $import->status === 'failed') ? 100 : 0; // Show 100% also on fail to remove bar
      $this->activeProgressBar = false; // Hide progress bar if not processing
    }
  }

  public function markNotificationAsRead(string $notificationId): void
  {
    $user = Auth::user();
    if ($user) {
      $notification = $user->unreadNotifications()->where('id', $notificationId)->first();
      if ($notification) {
        $notification->markAsRead();
        // Refresh notifications by re-calling the method or dispatching the event to self
        $this->refreshNotifications();
        // Or if other components need to know: $this->dispatch('notificationsUpdated');
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

  /**
   * Handles click on a notification, marks it as read, and redirects.
   * The $link should ideally come from the notification data.
   */
  public function handleNotificationClick(string $notificationId, ?string $link = null): void
  {
    $this->markNotificationAsRead($notificationId);

    // If a link is provided (from notification data), redirect to it.
    // Otherwise, do nothing or redirect to a default notifications page.
    if ($link && $link !== '#') {
      redirect()->to($link);
    }
    // Optionally, if no link, you could redirect to notifications.index:
    // else {
    //     redirect()->route('notifications.index');
    // }
  }

  // Helper to get flag icon, not directly used in Blade but good for consistency if needed
  public function getLocaleFlagIcon(string $locale): string
  {
    return $this->availableLocales[$locale]['flag_icon_class'] ?? 'fi-us'; // Default flag
  }
}
