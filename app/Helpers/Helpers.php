<?php

declare(strict_types=1);

namespace App\Helpers;

// Model imports for status constants
use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User; // Assuming User model for appClasses if roles are checked there

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str; // Added Str for string manipulation

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
    $locale = $isConsole ? $defaultLocale : (Session::has('locale') ? Session::get('locale') : $defaultLocale);
    App::setLocale($locale); // Set application locale
    $textDirection = $locale === 'ar' ? 'rtl' : 'ltr'; // For RTL support if needed

    // Retrieve values from custom.php configuration with default fallbacks
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
    $primaryColor = $themeCustomConfig['primaryColor'] ?? '#0055A4'; // MOTAC Blue default

    // Customizer related settings
    $hasCustomizer = Config::get('custom.hasCustomizer', false); // Whether customizer JS is included
    $displayCustomizer = $themeCustomConfig['displayCustomizer'] ?? false; // Whether customizer UI is visible
    $customizerControls = $themeCustomConfig['customizerControls'] ?? [
      //'style',
      'layoutType',
      'menuFixed',
      'menuCollapsed',
      'layoutNavbarFixed',
      'layoutFooterFixed'
    ];
    if ($myRTLSupport) {
      $customizerControls[] = 'rtl';
    }

    // Layout classes based on configuration
    $navbarDetached = ($myLayout === 'vertical' && $contentNavbar) ? 'navbar-detached' : '';
    $menuFixed = ($myLayout === 'vertical' && $isMenu) ? 'layout-menu-fixed' : '';
    $menuCollapsed = ($myLayout === 'vertical' && ($themeCustomConfig['menuCollapsed'] ?? false)) ? 'layout-menu-collapsed' : '';
    $navbarFixed = ($isNavbar && ($themeCustomConfig['navbarFixed'] ?? true)) ? 'layout-navbar-fixed' : '';
    $footerFixed = ($isFooter && ($themeCustomConfig['footerFixed'] ?? false)) ? 'layout-footer-fixed' : '';

    // Combine all settings for easy access in views
    return [
      'myLayout' => $myLayout,
      'myTheme' => $myTheme,
      'myStyle' => $myStyle,
      'textDirection' => $textDirection,
      'rtlSupport' => $myRTLSupport ? '/rtl' : '', // Path for RTL CSS if needed
      'navbarFull' => $navbarFull,
      'contentNavbar' => $contentNavbar,
      'isMenu' => $isMenu,
      'isNavbar' => $isNavbar,
      'isFooter' => $isFooter,
      'isFlex' => $isFlex,
      'hasCustomizer' => $hasCustomizer,
      'displayCustomizer' => $displayCustomizer,
      'customizerControls' => $customizerControls,
      'primaryColor' => $primaryColor,
      'navbarDetached' => $navbarDetached,
      'menuFixed' => $menuFixed,
      'menuCollapsed' => $menuCollapsed,
      'navbarFixed' => $navbarFixed,
      'footerFixed' => $footerFixed,
      'pageClasses' => implode(' ', [
        $myLayout . '-layout',
        $myTheme,
        $myStyle . '-style',
        $navbarDetached,
        $menuFixed,
        $menuCollapsed,
        $navbarFixed,
        $footerFixed,
      ]),
      'assetsPath' => asset('assets/'), // Base path for assets
      'appLogo' => 'assets/img/logo/motac-logo.svg', // Default MOTAC logo
      'templateName' => 'MOTAC IRMS', // Application name
      'showDropdownOnHover' => $themeCustomConfig['showDropdownOnHover'] ?? false, // For horizontal menu dropdown
    ];
  }

  /**
   * Helper function to update the page configuration based on the current page.
   * This is primarily for theme customization from a `pageConfigs` array.
   *
   * @param array $pageConfigs
   * @return string Empty string (side effect: updates config)
   */
  public static function updatePageConfig(array $pageConfigs): string
  {
    foreach ($pageConfigs as $key => $val) {
      Config::set('custom.custom.' . $key, $val);
    }
    return '';
  }

  /**
   * Checks if a menu item is directly active or if its branch contains the active route.
   */
  public static function isMotacMenuItemActiveRecursiveCheck(
    $item,
    $currentRouteName,
    &$isAnyChildActiveGlobalScope,
    $userRole
  ): bool {
    $canViewItem =
      $userRole === 'Admin' ||
      !isset($item->role) ||
      empty((array) $item->role) ||
      (is_string($item->role) && $userRole === $item->role) ||
      (is_array($item->role) && in_array($userRole, $item->role)); // Added check for array of roles

    if (!$canViewItem) {
      $isAnyChildActiveGlobalScope = false;
      return false;
    }

    $isDirectlyActive =
      (isset($item->routeName) && $currentRouteName === $item->routeName) ||
      (isset($item->url) && Request::is(ltrim($item->url, '/'))) ||
      (isset($item->routeNamePrefix) && str_starts_with($currentRouteName, $item->routeNamePrefix));

    if ($isDirectlyActive) {
      $isAnyChildActiveGlobalScope = true;
      return true;
    }

    if (isset($item->submenu) && is_array($item->submenu) && !empty($item->submenu)) {
      foreach ($item->submenu as $subItem) {
        // Pass $isAnyChildActiveGlobalScope by reference for child checks to update the parent's knowledge
        if (static::isMotacMenuItemActiveRecursiveCheck($subItem, $currentRouteName, $isAnyChildActiveGlobalScope, $userRole)) {
          // If a child path sets $isAnyChildActiveGlobalScope to true, this branch is active
          return true;
        }
      }
    }
    // If this item itself is not active, and no child made the branch active,
    // then this specific path doesn't make the menu active.
    // The $isAnyChildActiveGlobalScope is primarily for the caller to know if *any* part of the
    // originally passed item's tree was active.
    // For the return value of *this specific call*, if not directly active, and no children were active, it's false.
    // The final value of $isAnyChildActiveGlobalScope is determined by the recursive calls.
    return false;
  }

  public static function getStatusColorClass(string $status, string $type): string
  {
    $statusColors = [
      'loan_application' => [ // Changed from 'loan' to be more specific if needed
        LoanApplication::STATUS_DRAFT => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
        LoanApplication::STATUS_PENDING_SUPPORT => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        LoanApplication::STATUS_PENDING_HOD_REVIEW => 'bg-info-subtle text-info-emphasis border border-info-subtle',
        LoanApplication::STATUS_PENDING_BPM_REVIEW => 'bg-info-subtle text-info-emphasis border border-info-subtle',
        LoanApplication::STATUS_APPROVED => 'bg-success-subtle text-success-emphasis border border-success-subtle',
        LoanApplication::STATUS_REJECTED => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
        LoanApplication::STATUS_ISSUED => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
        LoanApplication::STATUS_PARTIALLY_ISSUED => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
        LoanApplication::STATUS_OVERDUE => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
        LoanApplication::STATUS_RETURNED => 'bg-light text-dark border border-secondary-subtle',
        LoanApplication::STATUS_CANCELLED => 'bg-dark-subtle text-dark-emphasis border border-dark-subtle',
        LoanApplication::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION => 'bg-info-subtle text-info-emphasis border border-info-subtle',
      ],
      'email_application' => [ // Changed from 'email'
        EmailApplication::STATUS_DRAFT => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
        EmailApplication::STATUS_PENDING_SUPPORT => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        EmailApplication::STATUS_PENDING_ADMIN => 'bg-info-subtle text-info-emphasis border border-info-subtle',
        EmailApplication::STATUS_APPROVED => 'bg-success-subtle text-success-emphasis border border-success-subtle',
        EmailApplication::STATUS_REJECTED => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
        EmailApplication::STATUS_PROCESSING => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
        EmailApplication::STATUS_COMPLETED => 'bg-success-subtle text-success-emphasis border border-success-subtle',
        EmailApplication::STATUS_PROVISION_FAILED => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
        EmailApplication::STATUS_CANCELLED => 'bg-dark-subtle text-dark-emphasis border border-dark-subtle',
      ],
      'approval' => [
          Approval::STATUS_PENDING => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
          Approval::STATUS_APPROVED => 'bg-success-subtle text-success-emphasis border border-success-subtle',
          Approval::STATUS_REJECTED => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
          // Assuming 'canceled' is a possible status for Approval model. Add more as needed.
          // Note: If Approval model doesn't have a CANCELED constant, this will cause an error.
          // It's good practice to ensure all constants used here are defined in their respective models.
          // For now, I'll add it based on the previous context provided.
          Approval::STATUS_CANCELED => 'bg-dark-subtle text-dark-emphasis border border-dark-subtle',
      ],
      'loan_transaction' => [
          LoanTransaction::STATUS_PENDING => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
          LoanTransaction::STATUS_ISSUED => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
          LoanTransaction::STATUS_RETURNED => 'bg-success-subtle text-success-emphasis border border-success-subtle',
          LoanTransaction::STATUS_RETURNED_PENDING_INSPECTION => 'bg-info-subtle text-info-emphasis border border-info-subtle',
          LoanTransaction::STATUS_RETURNED_GOOD => 'bg-success-subtle text-success-emphasis border border-success-subtle',
          LoanTransaction::STATUS_RETURNED_DAMAGED => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
          LoanTransaction::STATUS_ITEMS_REPORTED_LOST => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
          LoanTransaction::STATUS_RETURNED_WITH_LOSS => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
          LoanTransaction::STATUS_RETURNED_WITH_DAMAGE_AND_LOSS => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
          LoanTransaction::STATUS_PARTIALLY_RETURNED => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
          LoanTransaction::STATUS_COMPLETED => 'bg-success-subtle text-success-emphasis border border-success-subtle',
          LoanTransaction::STATUS_CANCELLED => 'bg-dark-subtle text-dark-emphasis border border-dark-subtle',
          LoanTransaction::STATUS_OVERDUE => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
      ],
      'equipment_status' => [
          Equipment::STATUS_AVAILABLE => 'bg-success-subtle text-success-emphasis border border-success-subtle',
          Equipment::STATUS_ON_LOAN => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
          Equipment::STATUS_UNDER_MAINTENANCE => 'bg-info-subtle text-info-emphasis border border-info-subtle',
          Equipment::STATUS_DAMAGED_NEEDS_REPAIR => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
          Equipment::STATUS_DISPOSED => 'bg-dark-subtle text-dark-emphasis border border-dark-subtle',
      ],
    ];

    $defaultClass = 'bg-light text-dark border';

    return $statusColors[$type][$status] ?? $defaultClass;
  }

  /**
   * Helper function to format dates consistently.
   *
   * @param mixed $dateValue The date to format (Carbon instance, string, or null).
   * @param string $formatKey The key for the date format string in config('app.date_formats')
   * or a direct PHP date format string.
   * @param string|null $default The default string to return if date is invalid or null.
   * @return string|null
   */
  public static function formatDate($dateValue, string $formatKey = 'date_format_my_short', ?string $default = null): ?string
  {
    if (is_null($dateValue)) {
      return $default;
    }

    try {
      $date = ($dateValue instanceof Carbon) ? $dateValue : Carbon::parse((string) $dateValue);

      // Check if formatKey is a key in config or a direct format string
      $formatString = Config::get('app.date_formats.' . $formatKey, $formatKey);

      return $date->translatedFormat($formatString);
    } catch (\Exception $e) {
      Log::warning("Helpers::formatDate - Failed to parse or format date value: " . (is_object($dateValue) ? get_class($dateValue) : (string) $dateValue) . " with format key: {$formatKey}. Error: " . $e->getMessage());
      return $default ?? (is_string($dateValue) ? $dateValue : __('Tarikh Tidak Sah')); // Return original string or default if parsing fails
    }
  }
}
