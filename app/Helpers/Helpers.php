<?php

declare(strict_types=1);

namespace App\Helpers;

// Model imports for status constants
use App\Models\Equipment;
use App\Models\LoanApplication;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

final class Helpers
{
    /**
     * Returns an array of app layout/theme config for use in Blade.
     */
    public static function appClasses(): array
    {
        $themeCustomConfig = Config::get('custom.custom', []);
        $isConsole         = App::runningInConsole();
        $defaultLocale     = Config::get('app.locale', 'ms');
        $locale            = $isConsole ? $defaultLocale : (Session::has('locale') ? Session::get('locale') : $defaultLocale);
        App::setLocale($locale);
        $textDirection      = $locale === 'ar' ? 'rtl' : 'ltr';
        $myLayout           = $themeCustomConfig['myLayout']           ?? 'vertical';
        $myTheme            = $themeCustomConfig['myTheme']            ?? 'theme-motac';
        $myStyle            = $themeCustomConfig['myStyle']            ?? 'light';
        $myRTLSupport       = $themeCustomConfig['myRTLSupport']       ?? false;
        $navbarFull         = $themeCustomConfig['navbarFull']         ?? false;
        $contentNavbar      = $themeCustomConfig['contentNavbar']      ?? true;
        $isMenu             = $themeCustomConfig['isMenu']             ?? true;
        $isNavbar           = $themeCustomConfig['isNavbar']           ?? true;
        $isFooter           = $themeCustomConfig['isFooter']           ?? true;
        $customizerControls = $themeCustomConfig['customizerControls'] ?? [
            'rtl',
            'style',
            'headerType',
            'contentLayout',
            'layoutCollapsed',
            'showFooter',
            'showMenu',
            'showNavbar',
        ];

        // Check if the current route is one of the listed routes to apply 'layout-without-menu'
        $bodyClasses = [];
        if (in_array(Request::route()->getName(), ['resource-management.equipment-admin.show', 'some.other.route'])) {
            $bodyClasses[] = 'layout-without-menu';
        }

        return [
            'myLayout'           => $myLayout,
            'myTheme'            => $myTheme,
            'myStyle'            => $myStyle,
            'myRTLSupport'       => $myRTLSupport,
            'navbarFull'         => $navbarFull,
            'contentNavbar'      => $contentNavbar,
            'isMenu'             => $isMenu,
            'isNavbar'           => $isNavbar,
            'isFooter'           => $isFooter,
            'customizerControls' => $customizerControls,
            'horizontalMenuType' => $themeCustomConfig['horizontalMenuType'] ?? 'sticky',
            'bodyClasses'        => implode(' ', $bodyClasses),
        ];
    }

    /**
     * Returns color string for loan application status.
     */
    public static function getLoanStatusColor(string $status): string
    {
        return match ($status) {
            LoanApplication::STATUS_PENDING_SUPPORT => 'warning',
            LoanApplication::STATUS_APPROVED        => 'primary',
            LoanApplication::STATUS_ISSUED, LoanApplication::STATUS_PARTIALLY_ISSUED => 'info',
            LoanApplication::STATUS_RETURNED => 'success',
            LoanApplication::STATUS_REJECTED => 'danger',
            LoanApplication::STATUS_OVERDUE  => 'dark',
            default                          => 'secondary',
        };
    }

    /**
     * Returns color string for equipment status.
     * Matches status values from Equipment model.
     */
    public static function getEquipmentStatusColor(string $status): string
    {
        return match ($status) {
            Equipment::STATUS_AVAILABLE         => 'success',
            Equipment::STATUS_ON_LOAN           => 'info', // Corrected: use STATUS_ON_LOAN instead of STATUS_LOANED
            Equipment::STATUS_UNDER_MAINTENANCE => 'warning', // Corrected: use STATUS_UNDER_MAINTENANCE instead of STATUS_IN_REPAIR
            Equipment::STATUS_DAMAGED           => 'danger', // use STATUS_DAMAGED
            Equipment::STATUS_DISPOSED          => 'dark',
            Equipment::STATUS_LOST              => 'secondary',
            default                             => 'secondary',
        };
    }

    /**
     * Returns a Bootstrap color class for status value.
     * Used for badges, pills, etc. in Blade views.
     * Supports equipment, department, and generic status.
     */
    public static function getStatusColorClass(string $status): string
    {
        // You may extend this logic for other status types as needed.
        // Try equipment status first, fallback to loan status, then generic.
        $equipmentColors = [
            Equipment::STATUS_AVAILABLE            => 'bg-success',
            Equipment::STATUS_ON_LOAN              => 'bg-info',
            Equipment::STATUS_UNDER_MAINTENANCE    => 'bg-warning',
            Equipment::STATUS_DAMAGED              => 'bg-danger',
            Equipment::STATUS_DISPOSED             => 'bg-dark',
            Equipment::STATUS_LOST                 => 'bg-secondary',
            Equipment::STATUS_RETIRED              => 'bg-secondary',
            Equipment::STATUS_DAMAGED_NEEDS_REPAIR => 'bg-danger',
        ];

        if (isset($equipmentColors[$status])) {
            return $equipmentColors[$status];
        }

        // Extend: For department status or other entity, add more mappings as needed.
        // Example: 'active', 'inactive', 'pending' etc.
        $genericColors = [
            'active'   => 'bg-success',
            'inactive' => 'bg-secondary',
            'pending'  => 'bg-warning',
            'rejected' => 'bg-danger',
            'approved' => 'bg-primary',
            'on_hold'  => 'bg-warning',
        ];

        return $genericColors[$status] ?? 'bg-secondary';
    }

    /**
     * Returns the total count of equipment by status.
     */
    public static function checkEquipmentStatusCount(string $status): int
    {
        return Equipment::where('status', $status)->count();
    }

    /**
     * Formats bytes to human-readable string (e.g., 1.50 MB).
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow   = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }

    /**
     * Formats a date string to various formats for display.
     */
    public static function formattedDate(?string $dateValue, string $formatKey = 'default', string $default = '-'): string
    {
        if (empty($dateValue)) {
            return $default;
        }

        $formatTemplates = [
            'default'      => 'd/m/Y',
            'datetime'     => 'd/m/Y H:i A',
            'month_year'   => 'M Y',
            'date_time_ss' => 'd/m/Y H:i:s',
        ];

        $format = $formatTemplates[$formatKey] ?? $formatTemplates['default'];

        try {
            return Carbon::parse($dateValue)->translatedFormat($format);
        } catch (\Exception $exception) {
            Log::error('Error parsing date: '.(is_object($dateValue) ? get_class($dateValue) : (string) $dateValue).sprintf(' with format key: %s. Error: ', $formatKey).$exception->getMessage());

            return $default; // Return default or null on error
        }
    }

    /**
     * Returns 'active' if route matches, used for navigation highlighting.
     */
    public static function isActiveRoute(string $route, array $params = []): string
    {
        if (Request::routeIs($route)) {
            if (empty($params)) {
                return 'active';
            }
            $currentParams = Request::route()->parameters();
            foreach ($params as $key => $value) {
                if (! isset($currentParams[$key]) || $currentParams[$key] !== $value) {
                    return '';
                }
            }

            return 'active';
        }

        return '';
    }

    /**
     * Returns 'active' if route matches, for URL highlighting.
     */
    public static function getActiveUrl($route): string
    {
        if (Request::routeIs($route)) {
            return 'active';
        }

        return '';
    }

    /**
     * Returns 'open' if route matches, for menu open state.
     */
    public static function getOpenClass($route): string
    {
        if (Request::routeIs($route)) {
            return 'open';
        }

        return '';
    }

    /**
     * Returns 'active' if $routeName matches $currentRouteName.
     */
    public static function isActiveLink($routeName, $currentRouteName): string
    {
        return $routeName === $currentRouteName ? 'active' : '';
    }

    /**
     * Returns 'open' if any route in $routeNames matches current route.
     */
    public static function isMenuOpen($routeNames): string
    {
        foreach ($routeNames as $routeName) {
            if (Request::routeIs($routeName)) {
                return 'open';
            }
        }

        return '';
    }

    /**
     * Returns 'active' if any route in $routeNames matches current route.
     */
    public static function isMenuActive($routeNames): string
    {
        foreach ($routeNames as $routeName) {
            if (Request::routeIs($routeName)) {
                return 'active';
            }
        }

        return '';
    }

    /**
     * Updates the page configuration.
     */
    public static function updatePageConfig(array $pageConfigs): void
    {
        // Merge the provided configs into the current config for the request
        foreach ($pageConfigs as $key => $value) {
            Config::set('custom.custom.'.$key, $value);
        }
    }
}
