<?php

declare(strict_types=1);

namespace App\Helpers;

// Model imports for status constants
use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

final class Helpers
{
    public static function appClasses(): array
    {
        $themeCustomConfig = Config::get('custom.custom', []);
        $isConsole = App::runningInConsole();
        $defaultLocale = Config::get('app.locale', 'ms');
        $locale = $isConsole ? $defaultLocale : (Session::has('locale') ? Session::get('locale') : $defaultLocale);
        App::setLocale($locale);
        $textDirection = $locale === 'ar' ? 'rtl' : 'ltr';
        $myLayout = $themeCustomConfig['myLayout'] ?? 'vertical';
        $myTheme = $themeCustomConfig['myTheme'] ?? 'theme-motac';
        $myStyle = $themeCustomConfig['myStyle'] ?? 'light';
        $myRTLSupport = $themeCustomConfig['myRTLSupport'] ?? false;
        $navbarFull = $themeCustomConfig['navbarFull'] ?? false;
        $contentNavbar = $themeCustomConfig['contentNavbar'] ?? true;
        $isMenu = $themeCustomConfig['isMenu'] ?? true;
        $isNavbar = $themeCustomConfig['isNavbar'] ?? true;
        $isFooter = $themeCustomConfig['isFooter'] ?? true;
        $isFlex = $themeCustomConfig['isFlex'] ?? false;
        $primaryColor = $themeCustomConfig['primaryColor'] ?? '#0055A4';
        $hasCustomizer = Config::get('custom.hasCustomizer', false);
        $displayCustomizer = $themeCustomConfig['displayCustomizer'] ?? false;
        $customizerControls = $themeCustomConfig['customizerControls'] ?? ['layoutType', 'menuFixed', 'menuCollapsed', 'layoutNavbarFixed', 'layoutFooterFixed'];
        if ($myRTLSupport) {
            $customizerControls[] = 'rtl';
        }
        $navbarDetached = ($myLayout === 'vertical' && $contentNavbar) ? 'navbar-detached' : '';
        $menuFixed = ($myLayout === 'vertical' && $isMenu) ? 'layout-menu-fixed' : '';
        $menuCollapsed = ($myLayout === 'vertical' && ($themeCustomConfig['menuCollapsed'] ?? false)) ? 'layout-menu-collapsed' : '';
        $navbarFixed = ($isNavbar && ($themeCustomConfig['navbarFixed'] ?? true)) ? 'layout-navbar-fixed' : '';
        $footerFixed = ($isFooter && ($themeCustomConfig['footerFixed'] ?? false)) ? 'layout-footer-fixed' : '';

        return [
            'myLayout' => $myLayout, 'myTheme' => $myTheme, 'myStyle' => $myStyle, 'textDirection' => $textDirection, 'rtlSupport' => $myRTLSupport ? '/rtl' : '', 'navbarFull' => $navbarFull, 'contentNavbar' => $contentNavbar, 'isMenu' => $isMenu, 'isNavbar' => $isNavbar, 'isFooter' => $isFooter, 'isFlex' => $isFlex, 'hasCustomizer' => $hasCustomizer, 'displayCustomizer' => $displayCustomizer, 'customizerControls' => $customizerControls, 'primaryColor' => $primaryColor, 'navbarDetached' => $navbarDetached, 'menuFixed' => $menuFixed, 'menuCollapsed' => $menuCollapsed, 'navbarFixed' => $navbarFixed, 'footerFixed' => $footerFixed, 'pageClasses' => implode(' ', [$myLayout . '-layout', $myTheme, $myStyle . '-style', $navbarDetached, $menuFixed, $menuCollapsed, $navbarFixed, $footerFixed, ]), 'assetsPath' => asset('assets/'), 'appLogo' => 'assets/img/logo/motac-logo.svg', 'templateName' => Config::get('variables.templateName', 'MOTAC IRMS'), 'showDropdownOnHover' => $themeCustomConfig['showDropdownOnHover'] ?? false,
        ];
    }

    public static function updatePageConfig(array $pageConfigs): string
    {
        foreach ($pageConfigs as $key => $val) {
            Config::set('custom.custom.' . $key, $val);
        }
        return '';
    }

    public static function isMotacMenuItemActiveRecursiveCheck($item, $currentRouteName, &$isAnyChildActiveGlobalScope, $userRole): bool
    {
        $canViewItem = $userRole === 'Admin' || !isset($item->role) || empty((array) $item->role) || (is_string($item->role) && $userRole === $item->role) || (is_array($item->role) && in_array($userRole, $item->role));
        if (!$canViewItem) {
            $isAnyChildActiveGlobalScope = false;
            return false;
        }
        $isDirectlyActive = (isset($item->routeName) && $currentRouteName === $item->routeName) || (isset($item->url) && Request::is(ltrim($item->url, '/'))) || (isset($item->routeNamePrefix) && str_starts_with((string) $currentRouteName, (string) $item->routeNamePrefix));
        if ($isDirectlyActive) {
            $isAnyChildActiveGlobalScope = true;
            return true;
        }
        if (isset($item->submenu) && is_array($item->submenu) && !empty($item->submenu)) {
            foreach ($item->submenu as $subItem) {
                if (self::isMotacMenuItemActiveRecursiveCheck($subItem, $currentRouteName, $isAnyChildActiveGlobalScope, $userRole)) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function getStatusColorClass(string $status, string $type): string
    {
        $statusColors = [
            'loan_application' => [LoanApplication::STATUS_DRAFT => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle', LoanApplication::STATUS_PENDING_SUPPORT => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle', LoanApplication::STATUS_PENDING_APPROVER_REVIEW => 'bg-info-subtle text-info-emphasis border border-info-subtle', LoanApplication::STATUS_PENDING_BPM_REVIEW => 'bg-info-subtle text-info-emphasis border border-info-subtle', LoanApplication::STATUS_APPROVED => 'bg-success-subtle text-success-emphasis border border-success-subtle', LoanApplication::STATUS_REJECTED => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle', LoanApplication::STATUS_ISSUED => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle', LoanApplication::STATUS_PARTIALLY_ISSUED => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle', LoanApplication::STATUS_OVERDUE => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle', LoanApplication::STATUS_RETURNED => 'bg-light text-dark border border-secondary-subtle', LoanApplication::STATUS_CANCELLED => 'bg-dark-subtle text-dark-emphasis border border-dark-subtle', LoanApplication::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION => 'bg-info-subtle text-info-emphasis border border-info-subtle', ],
            'email_application' => [EmailApplication::STATUS_DRAFT => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle', EmailApplication::STATUS_PENDING_SUPPORT => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle', EmailApplication::STATUS_PENDING_ADMIN => 'bg-info-subtle text-info-emphasis border border-info-subtle', EmailApplication::STATUS_APPROVED => 'bg-success-subtle text-success-emphasis border border-success-subtle', EmailApplication::STATUS_REJECTED => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle', EmailApplication::STATUS_PROCESSING => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle', EmailApplication::STATUS_COMPLETED => 'bg-success-subtle text-success-emphasis border border-success-subtle', EmailApplication::STATUS_PROVISION_FAILED => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle', EmailApplication::STATUS_CANCELLED => 'bg-dark-subtle text-dark-emphasis border border-dark-subtle', ],
            'approval' => [Approval::STATUS_PENDING => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle', Approval::STATUS_APPROVED => 'bg-success-subtle text-success-emphasis border border-success-subtle', Approval::STATUS_REJECTED => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle', Approval::STATUS_CANCELED => 'bg-dark-subtle text-dark-emphasis border border-dark-subtle', ],
            'loan_transaction' => [LoanTransaction::STATUS_PENDING => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle', LoanTransaction::STATUS_ISSUED => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle', LoanTransaction::STATUS_RETURNED => 'bg-success-subtle text-success-emphasis border border-success-subtle', LoanTransaction::STATUS_RETURNED_PENDING_INSPECTION => 'bg-info-subtle text-info-emphasis border border-info-subtle', LoanTransaction::STATUS_RETURNED_GOOD => 'bg-success-subtle text-success-emphasis border border-success-subtle', LoanTransaction::STATUS_RETURNED_DAMAGED => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle', LoanTransaction::STATUS_ITEMS_REPORTED_LOST => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle', LoanTransaction::STATUS_RETURNED_WITH_LOSS => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle', LoanTransaction::STATUS_RETURNED_WITH_DAMAGE_AND_LOSS => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle', LoanTransaction::STATUS_PARTIALLY_RETURNED => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle', LoanTransaction::STATUS_COMPLETED => 'bg-success-subtle text-success-emphasis border border-success-subtle', LoanTransaction::STATUS_CANCELLED => 'bg-dark-subtle text-dark-emphasis border border-dark-subtle', LoanTransaction::STATUS_OVERDUE => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle', ],
            'equipment_status' => [Equipment::STATUS_AVAILABLE => 'bg-success-subtle text-success-emphasis border border-success-subtle', Equipment::STATUS_ON_LOAN => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle', Equipment::STATUS_UNDER_MAINTENANCE => 'bg-info-subtle text-info-emphasis border border-info-subtle', Equipment::STATUS_DAMAGED_NEEDS_REPAIR => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle', Equipment::STATUS_DISPOSED => 'bg-dark-subtle text-dark-emphasis border border-dark-subtle', Equipment::STATUS_RETURNED_PENDING_INSPECTION => 'text-bg-secondary', Equipment::STATUS_LOST => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle', ],
            'user_status' => [User::STATUS_ACTIVE => 'bg-success-subtle text-success-emphasis border border-success-subtle', User::STATUS_INACTIVE => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle', User::STATUS_PENDING => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle', ],
        ];
        $defaultClass = 'bg-light text-dark border';
        return $statusColors[$type][$status] ?? $defaultClass;
    }

    public static function formatDate($dateValue, string $formatKey = 'date_my', ?string $default = null): ?string
    {
        if (is_null($dateValue)) {
            return $default;
        }
        try {
            $date = ($dateValue instanceof Carbon) ? $dateValue : Carbon::parse((string) $dateValue);
            $formatString = Config::get('motac.date_formats.' . $formatKey);
            if (!$formatString) {
                $formatString = Config::get('app.date_formats.' . $formatKey, $formatKey);
            }
            return $date->translatedFormat($formatString);
        } catch (\Exception $e) {
            Log::warning('Helpers::formatDate - Failed to parse or format date value: ' . (is_object($dateValue) ? get_class($dateValue) : (string) $dateValue) . " with format key: {$formatKey}. Error: " . $e->getMessage());
            return $default ?? (is_string($dateValue) ? $dateValue : __('Tarikh Tidak Sah'));
        }
    }
}
