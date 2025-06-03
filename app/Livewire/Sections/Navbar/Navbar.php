<?php

namespace App\Livewire\Sections\Navbar;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection; // Use Eloquent Collection
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Helpers\Helpers; // For accessing appClasses
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // Added
use Illuminate\Support\Facades\Route; // Added

class Navbar extends Component
{
  public EloquentCollection $unreadNotifications; // Changed to EloquentCollection

  public string $defaultProfilePhotoUrl;
  public string $profileShowRoute = 'profile.show';
  public string $adminSettingsRoute = 'admin.dashboard';
  public bool $canViewAdminSettings = false;

  public string $containerNav = 'container-fluid';
  public string $navbarDetachedClass = '';
  public bool $navbarFull = true;
  public bool $navbarHideToggle = false;
  public ?string $activeTheme = null;

  public array $availableLocales = [];
  protected string $localeConfigKey = 'app.available_locales';

  public function mount(
    string $containerNav = 'container-fluid',
    string $navbarDetachedClass = '',
    ?bool $navbarFull = null,
    ?bool $navbarHideToggle = null
  ): void {
    $configData = Helpers::appClasses();

    $this->containerNav = $containerNav;
    $this->navbarDetachedClass = $navbarDetachedClass;
    $this->navbarFull = $navbarFull ?? ($configData['navbarFull'] ?? true);
    // Assuming 'menuHorizontal' might be a key in $configData or derived from 'myLayout'
    $this->navbarHideToggle = $navbarHideToggle ?? ($configData['menuHorizontal'] ?? ($configData['myLayout'] === 'horizontal' ?? false));

    $this->defaultProfilePhotoUrl = asset('assets/img/avatars/default-avatar.png');

    $this->unreadNotifications = new EloquentCollection(); // Initialize as empty EloquentCollection
    if (Auth::check()) {
      /** @var User $user */
      $user = Auth::user();
      $this->refreshNotifications();
      $this->canViewAdminSettings = $user->hasRole('Admin');
    }

    $configuredLocales = config($this->localeConfigKey, []);
    $processedLocales = [];
    foreach ($configuredLocales as $localeKey => $properties) {
      if (!is_array($properties)) {
        Log::warning("Navbar: Locale properties for '{$localeKey}' is not an array. Skipping.");
        continue;
      }
      $countryCode = 'default';
      if (!empty($properties['regional'])) {
        $parts = explode('_', $properties['regional']);
        if (count($parts) === 2) {
          $countryCode = strtolower($parts[1]);
        }
      }
      $processedLocales[$localeKey] = $properties;
      // More specific fallback for flag_code, ensuring 'my' for 'ms' and 'us' for 'en' as common defaults
      $processedLocales[$localeKey]['flag_code'] = !empty($countryCode) && $countryCode !== 'default' ? $countryCode : ($localeKey === 'ms' ? 'my' : ($localeKey === 'en' ? 'us' : $localeKey));
    }
    $this->availableLocales = $processedLocales;

    if (is_null($this->activeTheme)) {
      $this->activeTheme = $configData['myStyle'] ?? 'light';
    }
  }

  public function render(): View
  {
    return view('livewire.sections.navbar.navbar', [
      'currentLocaleData' => $this->getCurrentLocaleViewData(),
      'configData' => Helpers::appClasses()
    ]);
  }

  #[On('refreshNotifications')]
  public function refreshNotifications(): void
  {
    /** @var User|null $user */
    $user = Auth::user();
    $this->unreadNotifications = $user
      ? $user->unreadNotifications()->latest()->take(10)->get()
      : new EloquentCollection();
  }

  public function markNotificationAsRead(string $notificationId): void
  {
    /** @var User|null $user */
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
    /** @var User|null $user */
    $user = Auth::user();
    if ($user) {
      $user->unreadNotifications->markAsRead();
      $this->refreshNotifications();
    }
  }

  public function handleNotificationClick(string $notificationId, ?string $link = null): void
  {
    $this->markNotificationAsRead($notificationId);
    if ($link && $link !== '#!') {
      if (Str::startsWith($link, ['http://', 'https://', '/'])) {
        redirect()->to($link);
      } elseif (Route::has($link)) {
        redirect()->route($link);
      } else {
        Log::warning("Navbar: Notification link '{$link}' is not a valid URL or route name.");
      }
    }
  }

  protected function getCurrentLocaleViewData(): array
  {
    $appCurrentLocaleKey = app()->getLocale();
    $currentLocaleConfig = $this->availableLocales[$appCurrentLocaleKey] ?? null;

    $flagCode = $appCurrentLocaleKey === 'ms' ? 'my' : ($appCurrentLocaleKey === 'en' ? 'us' : $appCurrentLocaleKey); // Default flag
    $displayName = Str::upper($appCurrentLocaleKey);

    if ($currentLocaleConfig && is_array($currentLocaleConfig)) {
      $flagCode = $currentLocaleConfig['flag_code'] ?? $flagCode; // Use pre-calculated flag_code from mount
      $displayName = isset($currentLocaleConfig['name']) ? __($currentLocaleConfig['name']) : Str::upper($appCurrentLocaleKey);
    }
    return [
      'key' => $appCurrentLocaleKey,
      'flag_code' => $flagCode,
      'name' => $displayName,
    ];
  }

  #[On('themeHasChanged')]
  public function syncTheme($theme)
  {
    // Note: Due to wire:ignore on the switcher, this won't directly update the icon itself.
    // JavaScript is already handling the icon.
    // This is useful if the component needs to know the theme for other conditional logic.
    $this->activeTheme = $theme;

    // You might want to update the server-side session/config as well,
    // so that Helpers::appClasses() picks up the correct theme on the next full request.
    // This could involve calling a method on TemplateCustomizer if it handles saving.
    // For example:
    // session(['custom_theme_style' => $theme]); // And ensure Helpers.php reads from this session key
  }
}
