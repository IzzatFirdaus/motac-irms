<?php

namespace App\Livewire\Sections\Navbar;

use App\Helpers\Helpers;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;

class Navbar extends Component
{
    // REVISED: Public properties are now only for data passed from the parent layout.
    public string $containerNav = 'container-fluid';
    public string $navbarDetachedClass = '';

    /**
     * Mount the component with configuration from the layout.
     */
    public function mount(string $containerNav, string $navbarDetachedClass): void
    {
        $this->containerNav = $containerNav;
        $this->navbarDetachedClass = $navbarDetachedClass;
    }

    /**
     * Prepares the available locales from the config file for the view.
     * This now runs on every render to ensure data is always fresh and stateless.
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
            // REVISED: Simplified flag code generation.
            $regional = $properties['regional'] ?? '';
            $parts = explode('_', $regional);
            $countryCode = count($parts) === 2 ? strtolower($parts[1]) : null;

            $processedLocales[$localeKey] = $properties;
            $processedLocales[$localeKey]['flag_code'] = $countryCode ?? ($localeKey === 'ms' ? 'my' : 'us');
        }
        return $processedLocales;
    }

    /**
     * Prepares data for the currently active locale for the view.
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
     * Render the component's view.
     */
    public function render(): View
    {
        $availableLocales = $this->getAvailableLocales();
        $currentLocaleData = $this->getCurrentLocaleData($availableLocales);

        return view('livewire.sections.navbar.navbar', [
            'availableLocales' => $availableLocales,
            'currentLocaleData' => $currentLocaleData,
        ]);
    }
}
