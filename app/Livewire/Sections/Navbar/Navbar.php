<?php

namespace App\Livewire\Sections\Navbar;

use App\Helpers\Helpers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Navbar extends Component
{
    // --- State Properties ---
    public string $containerNav = 'container-fluid';
    public string $navbarDetachedClass = '';
    public bool $navbarFull = true;
    public bool $navbarHideToggle = false;
    public array $availableLocales = [];
    protected string $localeConfigKey = 'app.available_locales';

    // FIX: This public property MUST be declared here to be accessible in the Blade view.
    public ?string $activeTheme = null;

    /**
     * Mount the component.
     */
    public function mount(
        string $containerNav = 'container-fluid',
        string $navbarDetachedClass = '',
        ?bool $navbarFull = null,
        ?bool $navbarHideToggle = null
    ): void {
        $configData = Helpers::appClasses();

        // FIX: This line initializes the $activeTheme property when the component is loaded.
        // This allows for role-based themes to be set on the initial page load.
        $this->activeTheme = $configData['myStyle'] ?? 'light';

        $this->containerNav = $containerNav;
        $this->navbarDetachedClass = $navbarDetachedClass;
        $this->navbarFull = $navbarFull ?? ($configData['navbarFull'] ?? true);
        $this->navbarHideToggle = $navbarHideToggle ?? (($configData['myLayout'] ?? 'vertical') === 'horizontal');

        // Initialize language switcher
        $this->initializeLocales();
    }

    /**
     * FIX: This event listener syncs the component's state when the theme is changed on the client-side.
     */
    #[On('themeHasChanged')]
    public function syncTheme($theme)
    {
        Log::debug('[Navbar Component]: Syncing server state to theme "' . $theme . '".');
        $this->activeTheme = $theme;
    }

    private function initializeLocales(): void
    {
        $configuredLocales = config($this->localeConfigKey, []);
        $processedLocales = [];
        foreach ($configuredLocales as $localeKey => $properties) {
            if (!is_array($properties)) {
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
            $processedLocales[$localeKey]['flag_code'] = !empty($countryCode) && $countryCode !== 'default' ? $countryCode : ($localeKey === 'ms' ? 'my' : ($localeKey === 'en' ? 'us' : $localeKey));
        }
        $this->availableLocales = $processedLocales;
    }

    protected function getCurrentLocaleViewData(): array
    {
        $appCurrentLocaleKey = app()->getLocale();
        $currentLocaleConfig = $this->availableLocales[$appCurrentLocaleKey] ?? null;

        $flagCode = $appCurrentLocaleKey === 'ms' ? 'my' : ($appCurrentLocaleKey === 'en' ? 'us' : $appCurrentLocaleKey);
        $displayName = Str::upper($appCurrentLocaleKey);

        if ($currentLocaleConfig && is_array($currentLocaleConfig)) {
            $flagCode = $currentLocaleConfig['flag_code'] ?? $flagCode;
            $displayName = isset($currentLocaleConfig['name']) ? __($currentLocaleConfig['name']) : Str::upper($appCurrentLocaleKey);
        }

        return [
            'key' => $appCurrentLocaleKey,
            'flag_code' => $flagCode,
            'name' => $displayName,
        ];
    }

    public function render(): View
    {
        return view('livewire.sections.navbar.navbar', [
            'currentLocaleData' => $this->getCurrentLocaleViewData(),
        ]);
    }
}
