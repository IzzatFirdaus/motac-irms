<?php

namespace App\Livewire\Sections\Navbar;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Navbar Livewire Component
 *
 * Renders the application navbar, including language switcher and theme toggler.
 * Accepts container and detachment class from the parent layout.
 * Now with more robust prop defaults and locale handling.
 */
class Navbar extends Component
{
    // Bootstrap container class for the navbar
    public string $containerNav = 'container-fluid';

    // Additional class for detached navbar styling
    public string $navbarDetachedClass = '';

    /**
     * Mount the component with configuration from the layout.
     * Now with default values for robustness if props are not passed.
     */
    public function mount(string $containerNav = 'container-fluid', string $navbarDetachedClass = ''): void
    {
        $this->containerNav = $containerNav;
        $this->navbarDetachedClass = $navbarDetachedClass;
    }

    /**
     * Get all available locales for the language switcher.
     * Ensures each locale has a flag_code.
     *
     * @return array
     */
    private function getAvailableLocales(): array
    {
        $configuredLocales = Config::get('app.available_locales', []);
        $processedLocales = [];

        foreach ($configuredLocales as $localeKey => $properties) {
            if (!is_array($properties)) {
                continue;
            }

            // Generate the flag code (e.g., 'my' for ms_MY, 'us' for en_US)
            $regional = $properties['regional'] ?? '';
            $parts = explode('_', $regional);
            $countryCode = count($parts) === 2 ? strtolower($parts[1]) : null;

            $processedLocales[$localeKey] = $properties;
            $processedLocales[$localeKey]['flag_code'] = $properties['flag_code'] ?? ($countryCode ?? ($localeKey === 'ms' ? 'my' : 'us'));
        }

        return $processedLocales;
    }

    /**
     * Get data for the current/active locale.
     *
     * @param array $availableLocales
     * @return array
     */
    private function getCurrentLocaleData(array $availableLocales): array
    {
        $currentLocaleKey = App::getLocale();
        $currentLocaleConfig = $availableLocales[$currentLocaleKey] ?? null;

        return [
            'key' => $currentLocaleKey,
            'flag_code' => $currentLocaleConfig['flag_code'] ?? ($currentLocaleKey === 'ms' ? 'my' : 'us'),
            'name' => $currentLocaleConfig['name'] ?? Str::upper($currentLocaleKey),
        ];
    }

    /**
     * Render the navbar view, passing locale data for the language dropdown.
     */
    public function render(): View
    {
        $availableLocales = $this->getAvailableLocales();
        $currentLocaleData = $this->getCurrentLocaleData($availableLocales);

        return view('livewire.sections.navbar.navbar', [
            'containerNav' => $this->containerNav,
            'navbarDetachedClass' => $this->navbarDetachedClass,
            'availableLocales' => $availableLocales,
            'currentLocaleData' => $currentLocaleData,
        ]);
    }
}
