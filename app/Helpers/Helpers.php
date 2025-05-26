<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session; // [cite: 10]

final class Helpers
{
    /**
     * Retrieves application classes and configuration for theming and layout.
     *
     * @return array<string, mixed>
     */
    public static function appClasses(): array
    {
        /** @var array<string,mixed> $customConfig */
        $customConfig = Config::get('custom.custom', []); // [cite: 10] Example path, adjust if your theme config is elsewhere

        // Default values for the MOTAC RMS
        $defaultData = [
            'templateName' => config('app.name', 'MOTAC Resource Management System'), // [cite: 10]
            'style' => Session::get('theme_style', config('theme.default_style', 'light')), // Example: light/dark mode preference from session or config [cite: 10]
            // Add other MOTAC specific defaults if needed
        ];

        // Merge default data with any custom configurations
        $mergedData = array_merge($defaultData, $customConfig);
        $finalConfig = [];

        $finalConfig['templateName'] = $mergedData['templateName'];
        $finalConfig['style'] = $mergedData['style']; // Used by app.blade.php for data-bs-theme

        // Determine text direction (LTR or RTL)
        $currentLocale = str_replace('_', '-', App::getLocale());
        $sessionTextDirection = Session::get('textDirection');

        if ($sessionTextDirection === 'rtl' || (!$sessionTextDirection && $currentLocale === 'ar')) { // [cite: 10]
            $finalConfig['textDirection'] = 'rtl';
        } else {
            $finalConfig['textDirection'] = 'ltr'; // [cite: 10]
        }

        // Add other relevant config data that might be used by layouts/views
        $finalConfig['isMenu'] = $mergedData['isMenu'] ?? true;
        $finalConfig['isNavbar'] = $mergedData['isNavbar'] ?? true;
        $finalConfig['isFooter'] = $mergedData['isFooter'] ?? true;
        $finalConfig['container'] = $mergedData['container'] ?? 'container-fluid';
        $finalConfig['navbarFull'] = $mergedData['navbarFull'] ?? false;
        $finalConfig['containerNav'] = $mergedData['containerNav'] ?? 'container-fluid';


        // Example: $finalConfig['rtlSupportPath'] = ($finalConfig['textDirection'] === 'rtl' && $mergedData['myRTLSupport']) ? '/rtl' : '';
        // Remove myRTLSupport if not used, or define it in config.

        return $finalConfig;
    }

    /**
     * Dynamically updates page-specific configurations.
     * This might be used by controllers or view composers to alter layout settings per page.
     *
     * @param array<string, mixed> $pageConfigs
     */
    public static function updatePageConfig(array $pageConfigs): void // [cite: 10]
    {
        $configBasePath = 'custom.custom'; // Ensure this config path is intentional for overrides
        if (empty($pageConfigs)) {
            return;
        }
        foreach ($pageConfigs as $configKey => $value) {
            if (is_string($configKey)) {
                Config::set($configBasePath . '.' . $configKey, $value);
            } else {
                Log::warning('Helpers::updatePageConfig: Skipping non-string config key.', [ // [cite: 10]
                    'key_type' => gettype($configKey),
                    'key_value' => $configKey,
                    'value' => $value,
                ]);
            }
        }
    }

    /**
     * Gets Bootstrap 5 text and background color classes for status badges.
     * Uses 'text-bg-*' for combined styling where appropriate.
     */
    public static function getBootstrapStatusColorClass(string $status): string // [cite: 10]
    {
        // Normalize status string: lowercase and replace spaces/hyphens with underscores
        $normalizedStatus = strtolower(str_replace([' ', '-'], '_', $status));

        return match ($normalizedStatus) { // [cite: 10]
            'draft' => 'text-bg-light',
            'pending', 'pending_support', 'pending_admin', 'pending_hod_review', 'pending_bpm_review' => 'text-bg-warning', // [cite: 10]
            'approved', 'issued', 'completed', 'returned_good' => 'text-bg-success', // [cite: 10]
            'rejected', 'provision_failed', 'lost', 'overdue', 'cancelled' => 'text-bg-danger', // [cite: 10]
            'processing', 'partially_issued', 'on_loan' => 'text-bg-info', // [cite: 10]
            'returned' => 'text-bg-primary', // [cite: 10] // General returned, might be good or with issues
            'under_maintenance', 'damaged_needs_repair',
            'returned_damaged', 'returned_minor_damage', 'returned_major_damage' => 'text-dark bg-orange', // Custom: Bootstrap doesn't have text-bg-orange. You'd need custom CSS for .bg-orange [cite: 10]
            'disposed', 'unserviceable' => 'text-bg-secondary',
            default => 'text-bg-secondary', // Fallback for unknown statuses
        };
    }

    /**
     * Gets Bootstrap 5 alert contextual classes.
     */
    public static function getBootstrapAlertClass(string $statusType): string // [cite: 10]
    {
        return match (strtolower($statusType)) { // [cite: 10]
            'success' => 'alert-success', // [cite: 10]
            'info', 'information' => 'alert-info', // [cite: 10]
            'warning' => 'alert-warning', // [cite: 10]
            'error', 'danger', 'failed' => 'alert-danger', // [cite: 10]
            default => 'alert-secondary', // [cite: 10]
        };
    }
}
