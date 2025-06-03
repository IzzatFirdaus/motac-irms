<?php

declare(strict_types=1);

namespace App\Helpers; // Ensure this namespace is correct for your application structure

// Model imports for status constants
use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log; // For logging potential issues
use Illuminate\Support\Facades\Route; // Added for currentRouteName
use Illuminate\Support\Facades\Request; // Added for Request::is

final class Helpers
{
    /**
     * Provides application-wide classes and configurations for views.
     * Aligns with MOTAC Design Language and System Design (Rev. 3).
     * Design Language Refs: 1.2 (Bahasa Melayu First), 2.1 (Color Palette), 7.1 (Logo Usage)
     * System Design Refs: 3.3 (AppServiceProvider for global data), 6.1 (Branding and Layout)
     */
    public static function appClasses(): array
    {
        $themeCustomConfig = Config::get('custom.custom', []); // Theme-specific customizations
        $isConsole = App::runningInConsole();

        // Locale and Text Direction (Design Language 1.2: Bahasa Melayu First - LTR)
        $defaultLocale = Config::get('app.locale', 'ms'); // Default to 'ms' (Bahasa Melayu)
        $sessionLocale = $isConsole ? $defaultLocale : Session::get('locale', $defaultLocale);
        $currentLocale = str_replace('_', '-', $sessionLocale);

        $defaultStyle = $themeCustomConfig['myStyle'] ?? 'light'; // Default to light mode
        $currentStyle = $isConsole ? $defaultStyle : Session::get('theme_style', $defaultStyle);
        $layout = $themeCustomConfig['myLayout'] ?? 'vertical';

        $defaultData = [
            'templateName' => __(config('variables.templateName', 'Sistem Pengurusan Sumber Bersepadu MOTAC')),
            'templateDescription' => __(config('variables.templateDescription', 'Sistem Dalaman Kementerian Pelancongan, Seni dan Budaya Malaysia untuk Pengurusan Sumber Elektronik dan ICT.')),
            'templateKeyword' => __(config('variables.templateKeyword', 'motac, bpm, sistem bersepadu, pengurusan sumber, pinjaman ict, permohonan emel, kerajaan malaysia')),
            'appFavicon' => config('variables.appFavicon', 'assets/img/favicon/favicon-motac.ico'),
            'templateLogoSvg' => config('variables.templateLogoSvg', 'assets/img/logo/motac-logo.svg'),
            'productPage' => $isConsole ? config('app.url', '/') : url('/'),
            'repositoryUrl' => config('variables.repositoryUrl', ''),
            'locale' => $currentLocale,
            'style' => $currentStyle,
            'theme' => $themeCustomConfig['myTheme'] ?? 'theme-motac',
            'layout' => $layout,
            'isMenu' => $themeCustomConfig['isMenu'] ?? true,
            'isNavbar' => $themeCustomConfig['isNavbar'] ?? true,
            'isFooter' => $themeCustomConfig['isFooter'] ?? true,
            'contentNavbar' => $themeCustomConfig['contentNavbar'] ?? ($layout === 'horizontal' ? false : true),
            'menuFixed' => $themeCustomConfig['menuFixed'] ?? true,
            'menuCollapsed' => $themeCustomConfig['menuCollapsed'] ?? false,
            'navbarFixed' => $themeCustomConfig['navbarFixed'] ?? true,
            'primaryColor' => $themeCustomConfig['primaryColor'] ?? '#0055A4',
            'navbarDetached' => $themeCustomConfig['navbarDetached'] ?? ($layout === 'vertical' ? true : false),
            'footerFixed' => $themeCustomConfig['footerFixed'] ?? false,
            'container' => $themeCustomConfig['container'] ?? 'container-fluid',
            'containerNav' => $themeCustomConfig['containerNav'] ?? 'container-fluid',
            'hasCustomizer' => $themeCustomConfig['hasCustomizer'] ?? false,
            'displayCustomizer' => $themeCustomConfig['displayCustomizer'] ?? false,
            'customizerHidden' => !($themeCustomConfig['displayCustomizer'] ?? false),
            'assetsPath' => $isConsole
                ? (rtrim(config('app.asset_url', config('app.url', '/')) ?? '', '/') . '/assets/') // MODIFIED LINE
                : (function_exists('asset') ? asset('assets/') : '/assets/'),
            'baseUrl' => $isConsole
                ? config('app.url', '/')
                : (function_exists('url') ? url('/') : '/'),
            'myRTLSupport' => $themeCustomConfig['myRTLSupport'] ?? true,
            'defaultTextDirectionFromConfig' => ($themeCustomConfig['myRTLMode'] ?? false) ? 'rtl' : 'ltr',
            'isFlex' => $themeCustomConfig['isFlex'] ?? false,
            'showMenu' => $themeCustomConfig['showMenu'] ?? true,
            'contentLayout' => $themeCustomConfig['contentLayout'] ?? ($layout === 'horizontal' ? 'compact' : 'wide'),
        ];

        $sessionTextDirection = $isConsole ? null : Session::get('textDirection');
        if (isset($sessionTextDirection) && in_array($sessionTextDirection, ['ltr', 'rtl'])) {
            $textDirection = $sessionTextDirection;
        } elseif ($defaultData['myRTLSupport'] && in_array($currentLocale, ['ar', 'he', 'fa'])) {
            $textDirection = 'rtl';
        } else {
            $textDirection = $defaultData['defaultTextDirectionFromConfig'];
        }

        $defaultData['textDirection'] = $textDirection;
        $defaultData['rtlSupport'] = ($textDirection === 'rtl' && $defaultData['myRTLSupport']) ? '/rtl' : '';
        $defaultData['bsTheme'] = ($defaultData['style'] === 'dark') ? 'dark' : 'light';

        return array_merge($defaultData, $themeCustomConfig);
    }

    public static function updatePageConfig(array $pageConfigs): void
    {
        if (empty($pageConfigs)) {
            return;
        }
        $existingConfig = Config::get('custom.custom', []);
        $newConfig = array_merge($existingConfig, $pageConfigs);
        Config::set('custom.custom', $newConfig);
    }

    public static function getStatusColorClass(string $status, ?string $context = null): string
    {
        $normalizedStatus = strtolower(str_replace([' ', '-'], '_', $status));
        return match ($normalizedStatus) {
            User::STATUS_ACTIVE => 'text-bg-success',
            User::STATUS_INACTIVE => 'text-bg-secondary',
            EmailApplication::STATUS_DRAFT => 'bg-light text-dark border',
            EmailApplication::STATUS_PENDING_SUPPORT => 'text-bg-warning',
            EmailApplication::STATUS_PENDING_ADMIN => 'text-bg-info',
            EmailApplication::STATUS_APPROVED => 'text-primary bg-primary-subtle border border-primary-subtle',
            EmailApplication::STATUS_PROCESSING => 'text-bg-primary',
            EmailApplication::STATUS_COMPLETED => 'text-bg-success',
            EmailApplication::STATUS_REJECTED => 'text-bg-danger',
            EmailApplication::STATUS_PROVISION_FAILED => 'text-danger bg-danger-subtle border border-danger-subtle',
            LoanApplication::STATUS_DRAFT => 'bg-light text-dark border',
            LoanApplication::STATUS_PENDING_SUPPORT, LoanApplication::STATUS_PENDING_HOD_REVIEW => 'text-bg-warning',
            LoanApplication::STATUS_PENDING_BPM_REVIEW => 'text-info bg-info-subtle border border-info-subtle',
            LoanApplication::STATUS_APPROVED => 'text-primary bg-primary-subtle border border-primary-subtle',
            LoanApplication::STATUS_PARTIALLY_ISSUED => 'text-info bg-info-subtle border border-info-subtle',
            LoanApplication::STATUS_ISSUED => 'text-bg-info',
            LoanApplication::STATUS_RETURNED => 'text-bg-success',
            LoanApplication::STATUS_OVERDUE => 'text-danger bg-danger-subtle border border-danger-subtle',
            LoanApplication::STATUS_CANCELLED, LoanApplication::STATUS_REJECTED => 'text-bg-danger',
            Equipment::STATUS_AVAILABLE => 'text-success bg-success-subtle border border-success-subtle',
            Equipment::STATUS_ON_LOAN => 'text-bg-info',
            Equipment::STATUS_UNDER_MAINTENANCE => 'text-warning bg-warning-subtle border border-warning-subtle',
            Equipment::STATUS_DISPOSED => 'text-bg-secondary border',
            Equipment::STATUS_LOST => 'text-bg-danger',
            Equipment::STATUS_DAMAGED_NEEDS_REPAIR => 'text-danger bg-warning-subtle border border-warning-subtle',
            Equipment::CONDITION_NEW => 'text-primary bg-primary-subtle border border-primary-subtle',
            Equipment::CONDITION_GOOD => 'text-success bg-success-subtle border border-success-subtle',
            Equipment::CONDITION_FAIR => 'text-info bg-info-subtle border border-info-subtle',
            Equipment::CONDITION_MINOR_DAMAGE => 'text-warning bg-warning-subtle border border-warning-subtle',
            Equipment::CONDITION_MAJOR_DAMAGE => 'text-danger bg-danger-subtle border border-danger-subtle',
            Equipment::CONDITION_UNSERVICEABLE => 'text-bg-secondary border',
            LoanTransaction::STATUS_PENDING => 'text-bg-secondary',
            LoanTransaction::STATUS_ISSUED => 'text-bg-info',
            LoanTransaction::STATUS_RETURNED_PENDING_INSPECTION => 'text-warning bg-warning-subtle border border-warning-subtle',
            LoanTransaction::STATUS_RETURNED_GOOD => 'text-bg-success',
            LoanTransaction::STATUS_RETURNED_DAMAGED => 'text-danger bg-warning-subtle border border-warning-subtle',
            LoanTransaction::STATUS_ITEMS_REPORTED_LOST => 'text-bg-danger',
            LoanTransaction::STATUS_COMPLETED => 'text-bg-success',
            LoanTransaction::STATUS_CANCELLED => 'text-bg-danger border',
            LoanTransactionItem::STATUS_ITEM_ISSUED => 'text-info bg-info-subtle border border-info-subtle',
            LoanTransactionItem::STATUS_ITEM_RETURNED_PENDING_INSPECTION => 'text-warning bg-warning-subtle border border-warning-subtle',
            LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD => 'text-success bg-success-subtle border border-success-subtle',
            LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE => 'text-warning bg-warning-subtle border border-warning-subtle',
            LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE => 'text-danger bg-danger-subtle border border-danger-subtle',
            LoanTransactionItem::STATUS_ITEM_REPORTED_LOST => 'text-bg-danger',
            LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN => 'text-bg-secondary border',
            Approval::STATUS_PENDING => 'text-bg-warning',
            Approval::STATUS_APPROVED => 'text-bg-success',
            default => 'text-dark bg-light border',
        };
    }

    public static function getAlertClass(string $statusType): string
    {
        return match (strtolower($statusType)) {
            'success', 'completed', 'approved' => 'success',
            'info', 'information', 'notice', 'processing' => 'info',
            'warning', 'pending', 'attention' => 'warning',
            'error', 'danger', 'failed', 'rejected', 'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public static function formatDate($date, string $formatType = 'date_format_my'): string
    {
        if (is_null($date)) {
            return '-';
        }
        if (!$date instanceof Carbon) {
            try {
                $date = Carbon::parse((string) $date);
            } catch (\Exception $e) {
                Log::warning("Helpers::formatDate - Could not parse date: " . (string)$date, ['exception' => $e->getMessage()]);
                return (string) $date;
            }
        }
        $defaultDateFormat = 'd/m/Y';
        $defaultDateTimeFormat = 'd/m/Y, h:i A';
        $format = config('app.' . $formatType, ($formatType === 'datetime_format_my' ? $defaultDateTimeFormat : $defaultDateFormat));
        return $date->translatedFormat($format);
    }

    /**
     * Checks if a menu item is directly active based on current route or URL.
     *
     * @param object $menuItem The menu item (expected to have properties like url, routeName, routeNamePrefix).
     * @param string|null $currentRouteName The name of the current route.
     * @return bool True if the menu item is directly active, false otherwise.
     */
    public static function isMenuItemDirectlyActive(object $menuItem, ?string $currentRouteName): bool
    {
        // Check URL first if present and not a placeholder
        if (isset($menuItem->url) && $menuItem->url !== 'javascript:void(0);' && $menuItem->url !== '#') {
            $itemUrl = ltrim($menuItem->url, '/'); // Normalize for Request::is()
            if (Request::is($itemUrl) || Request::is($itemUrl . '/*')) {
                return true;
            }
        }
        // Check exact route name match
        if (isset($menuItem->routeName) && $currentRouteName && $menuItem->routeName === $currentRouteName) {
            return true;
        }
        // Check route name prefix match
        if (isset($menuItem->routeNamePrefix) && $currentRouteName && str_starts_with($currentRouteName, $menuItem->routeNamePrefix)) {
            return true;
        }
        // Fallback for 'slug' if used and no routeName/routeNamePrefix, implies direct match
        if (isset($menuItem->slug) && !isset($menuItem->routeName) && !isset($menuItem->routeNamePrefix) && $menuItem->slug === $currentRouteName) {
            return true;
        }
        return false;
    }

    /**
     * Checks if a menu item or any of its children (recursively) is active.
     * This determines if a parent branch should be marked as "open".
     *
     * @param object $menuItem The menu item.
     * @param string|null $currentRouteName The name of the current route.
     * @param string|null $currentUserRole The role of the current user (for permission checks).
     * @return bool True if the branch (item or its children) is active, false otherwise.
     */
    public static function isMenuBranchActive(object $menuItem, ?string $currentRouteName, ?string $currentUserRole): bool
    {
        if (static::isMenuItemDirectlyActive($menuItem, $currentRouteName)) {
            return true;
        }

        if (isset($menuItem->submenu) && is_array($menuItem->submenu) && !empty($menuItem->submenu)) {
            foreach ($menuItem->submenu as $subMenuItem) {
                // Check if user can view this submenu item first
                $canViewSubmenu = false;
                if ($currentUserRole === 'Admin') {
                    $canViewSubmenu = true;
                } elseif (isset($subMenuItem->role)) {
                    $rolesArray = is_array($subMenuItem->role) ? $subMenuItem->role : [$subMenuItem->role];
                    $canViewSubmenu = in_array($currentUserRole, $rolesArray);
                } else {
                    $canViewSubmenu = true; // No specific role, assume viewable
                }

                if ($canViewSubmenu && static::isMenuBranchActive($subMenuItem, $currentRouteName, $currentUserRole)) {
                    return true; // If any child branch is active, this parent branch is active
                }
            }
        }
        return false;
    }
}
