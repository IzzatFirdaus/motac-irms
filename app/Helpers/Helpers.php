<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\User;
use App\Models\Approval;
use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\EmailApplication;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

final class Helpers
{
    /**
     * Retrieves application classes and configuration for theming and layout.
     * This is the primary source for $configData used in commonMaster.blade.php.
     * System Design Reference: 3.3 AppServiceProvider shares UI config from Helpers::appClasses().
     *
     * @return array<string, mixed>
     */
    public static function appClasses(): array
    {
        // Path to custom theme configurations.
        // Assumes config/custom.php returns ['custom' => [setting1 => value1, ...]]
        // If config/custom.php returns settings directly, use Config::get('custom', [])
        $themeCustomConfig = Config::get('custom.custom', []);

        // Default values for the MOTAC RMS
        $defaultData = [
            'templateName' => env('APP_NAME', 'MOTAC Resource Management System'), //
            'templateDescription' => __('Sistem Pengurusan Sumber Bersepadu Kementerian Pelancongan, Seni dan Budaya Malaysia.'),
            'templateKeyword' => __('motac, kementerian pelancongan, pengurusan sumber, pinjaman ict, permohonan emel'),

            'locale' => str_replace('_', '-', Session::get('locale', Config::get('app.locale', 'en'))), // Prioritize session, then app config

            // Theme and Layout Defaults from custom.php or hardcoded fallbacks
            'style' => $themeCustomConfig['myStyle'] ?? 'light', // 'light' or 'dark'
            'theme' => $themeCustomConfig['myTheme'] ?? 'theme-default',
            'layout' => $themeCustomConfig['myLayout'] ?? 'vertical', // 'vertical', 'horizontal'

            // Structural visibility flags
            'isMenu' => $themeCustomConfig['isMenu'] ?? true,
            'isNavbar' => $themeCustomConfig['isNavbar'] ?? true,
            'isFooter' => $themeCustomConfig['isFooter'] ?? true,
            'contentNavbar' => $themeCustomConfig['contentNavbar'] ?? true, // For themes where navbar is part of content

            // Layout behavior flags
            'menuFixed' => $themeCustomConfig['menuFixed'] ?? false,
            'menuCollapsed' => $themeCustomConfig['menuCollapsed'] ?? false,
            'navbarFixed' => $themeCustomConfig['navbarFixed'] ?? false,
            'navbarDetached' => $themeCustomConfig['navbarDetached'] ?? false, // If true, class 'navbar-detached' might be used
            'footerFixed' => $themeCustomConfig['footerFixed'] ?? false,

            // Container settings
            'container' => $themeCustomConfig['container'] ?? 'container-xxl', // Main content container type
            'containerNav' => $themeCustomConfig['containerNav'] ?? 'container-xxl', // Navbar container type

            // Customizer settings
            'hasCustomizer' => $themeCustomConfig['hasCustomizer'] ?? true, // Whether template-customizer.js is loaded
            'displayCustomizer' => $themeCustomConfig['displayCustomizer'] ?? true, // Whether the UI panel is shown
            'customizerHidden' => !($themeCustomConfig['displayCustomizer'] ?? true), // Class for hiding customizer UI if not displayed

            // Assets paths
            'assetsPath' => asset('/assets') . '/',
            'baseUrl' => url('/'),

            // RTL settings
            'myRTLSupport' => $themeCustomConfig['myRTLSupport'] ?? true, // Does theme support distinct RTL assets?
            // 'myRTLMode' from custom.php can set a default direction if true,
            // but session-based 'textDirection' or locale 'ar' will override.
            'defaultTextDirectionFromConfig' => ($themeCustomConfig['myRTLMode'] ?? false) ? 'rtl' : 'ltr',

            // Other theme specific settings from custom.php if needed
            'showDropdownOnHover' => $themeCustomConfig['showDropdownOnHover'] ?? true,
            'primaryColor' => $themeCustomConfig['primaryColor'] ?? '#7367f0', // Example primary color for theme consistency
            'isFlex' => $themeCustomConfig['isFlex'] ?? false,
        ];

        // Determine final textDirection based on priority: Session > Config File (myRTLMode) > Locale ('ar') > Default
        $textDirection = Session::get('textDirection'); // Set by LanguageController
        if (!$textDirection) {
            $textDirection = ($defaultData['defaultTextDirectionFromConfig'] === 'rtl') ? 'rtl' : (($defaultData['locale'] === 'ar') ? 'rtl' : 'ltr');
        }
        $defaultData['textDirection'] = $textDirection;

        // Determine rtlSupport path component based on final textDirection and myRTLSupport flag
        // This is crucial for loading LTR/RTL specific stylesheets dynamically
        if ($defaultData['textDirection'] === 'rtl' && $defaultData['myRTLSupport']) {
            $defaultData['rtlSupport'] = '/rtl'; // Or '-rtl', depends on your theme's CSS naming convention
        } else {
            $defaultData['rtlSupport'] = '';
        }

        // Merge any other specific keys from $themeCustomConfig that are not already covered by defaults explicitly
        // This ensures all keys from custom.php are available in $configData
        $finalConfig = array_merge($defaultData, $themeCustomConfig);

        // Ensure essential keys from defaultData are preserved if not in themeCustomConfig
        // (already handled by array_merge if defaultData is first argument, but good to be mindful)

        return $finalConfig;
    }

    /**
     * Dynamically updates page-specific configurations by merging into the main 'custom.custom' config path.
     * This method is for runtime overrides.
     * System Design Reference: AppServiceProvider may call this.
     *
     * @param array<string, mixed> $pageConfigs
     */
    public static function updatePageConfig(array $pageConfigs): void
    {
        $configBasePath = 'custom.custom'; // Path used in config/custom.php
        if (empty($pageConfigs)) {
            return;
        }
        foreach ($pageConfigs as $configKey => $value) {
            if (is_string($configKey)) {
                Config::set($configBasePath . '.' . $configKey, $value);
            } else {
                Log::warning('Helpers::updatePageConfig: Skipping non-string config key.', [
                    'key_type' => gettype($configKey),
                    'key_value' => $configKey,
                    'value' => $value,
                ]);
            }
        }
    }

    /**
     * Gets Bootstrap 5 text and background color classes for status badges.
     * Uses 'text-bg-*' for combined styling or specific bg/text for more control.
     * System Design Reference: 6.3 Reusable Blade Components (getStatusColorClass).
     * This should map all relevant statuses from User, EmailApplication, LoanApplication, Equipment, LoanTransaction models.
     */
    public static function getBootstrapStatusColorClass(string $status): string
    {
        $normalizedStatus = strtolower(str_replace([' ', '-'], '_', $status));

        // Ensure all model status constants are covered here for consistent UI
        return match ($normalizedStatus) {
            // General / User Statuses
            User::STATUS_ACTIVE => 'text-bg-success',
            User::STATUS_INACTIVE => 'text-bg-secondary',

            // EmailApplication Statuses
            EmailApplication::STATUS_DRAFT => 'text-bg-light border', // Added border for light bg
            EmailApplication::STATUS_PENDING_SUPPORT, EmailApplication::STATUS_PENDING_ADMIN => 'text-bg-warning',
            EmailApplication::STATUS_APPROVED, EmailApplication::STATUS_PROCESSING => 'text-bg-info',
            EmailApplication::STATUS_COMPLETED => 'text-bg-success',
            EmailApplication::STATUS_REJECTED, EmailApplication::STATUS_PROVISION_FAILED => 'text-bg-danger',

            // LoanApplication Statuses
            LoanApplication::STATUS_DRAFT => 'text-bg-light border',
            LoanApplication::STATUS_PENDING_SUPPORT, LoanApplication::STATUS_PENDING_HOD_REVIEW, LoanApplication::STATUS_PENDING_BPM_REVIEW => 'text-bg-warning',
            LoanApplication::STATUS_APPROVED => 'text-bg-primary', // Different from Email's approved for distinction
            LoanApplication::STATUS_ISSUED, LoanApplication::STATUS_PARTIALLY_ISSUED => 'text-bg-info',
            LoanApplication::STATUS_RETURNED => 'text-bg-success',
            LoanApplication::STATUS_OVERDUE => 'text-danger bg-warning-subtle border border-warning', // Custom combination
            LoanApplication::STATUS_REJECTED, LoanApplication::STATUS_CANCELLED => 'text-bg-danger',

            // Equipment Statuses (Operational)
            Equipment::STATUS_AVAILABLE => 'text-bg-success',
            Equipment::STATUS_ON_LOAN => 'text-bg-info',
            Equipment::STATUS_UNDER_MAINTENANCE => 'text-bg-warning',
            Equipment::STATUS_DAMAGED_NEEDS_REPAIR => 'text-danger bg-warning-subtle border border-warning',
            Equipment::STATUS_LOST => 'text-bg-danger',
            Equipment::STATUS_DISPOSED => 'text-bg-secondary',

            // LoanTransaction Statuses
            LoanTransaction::STATUS_PENDING => 'text-bg-secondary',
            LoanTransaction::STATUS_ISSUED => 'text-bg-info', // Already covered by LA, but can be specific if needed
            LoanTransaction::STATUS_RETURNED_PENDING_INSPECTION => 'text-bg-warning',
            LoanTransaction::STATUS_RETURNED_GOOD => 'text-bg-success',
            LoanTransaction::STATUS_RETURNED_DAMAGED => 'text-danger bg-warning-subtle border border-warning',
            LoanTransaction::STATUS_ITEMS_REPORTED_LOST => 'text-bg-danger',
            LoanTransaction::STATUS_RETURNED_WITH_LOSS => 'text-danger bg-warning-subtle border border-warning',
            LoanTransaction::STATUS_RETURNED_WITH_DAMAGE_AND_LOSS => 'text-danger bg-warning-subtle border border-warning',
            LoanTransaction::STATUS_COMPLETED => 'text-bg-success', // For TX completion
            LoanTransaction::STATUS_CANCELLED => 'text-bg-danger', // For TX cancellation

            // Approval Statuses
            Approval::STATUS_PENDING => 'text-bg-warning',
            Approval::STATUS_APPROVED => 'text-bg-success',
            Approval::STATUS_REJECTED => 'text-bg-danger',

            default => 'text-bg-secondary', // Default fallback
        };
    }

    /**
     * Gets Bootstrap 5 alert contextual classes.
     */
    public static function getBootstrapAlertClass(string $statusType): string
    {
        return match (strtolower($statusType)) {
            'success' => 'alert-success',
            'info', 'information', 'notice' => 'alert-info',
            'warning' => 'alert-warning',
            'error', 'danger', 'failed' => 'alert-danger',
            default => 'alert-secondary',
        };
    }
}
