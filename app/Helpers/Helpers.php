<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\Equipment;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session; // Corrected import
use Illuminate\Support\Carbon; // Added for Carbon::parse

final class Helpers
{
  /**
   * Provides application-wide classes and configurations for views.
   * System Design Reference: 3.3 AppServiceProvider, 6.1 Branding and Layout
   */
  public static function appClasses(): array
  {
    $themeCustomConfig = Config::get('custom.custom', []);
    $isConsole = App::runningInConsole();

    $defaultLocale = Config::get('app.locale', 'ms');
    $sessionLocale = $isConsole ? $defaultLocale : Session::get('locale', $defaultLocale);
    $currentLocale = str_replace('_', '-', $sessionLocale); //

    $defaultStyle = $themeCustomConfig['myStyle'] ?? 'light';
    $currentStyle = $isConsole ? $defaultStyle : Session::get('theme_style', $defaultStyle);

    $layout = $themeCustomConfig['myLayout'] ?? 'vertical';

    $defaultData = [
      'templateName' => config('variables.templateName', __('Sistem Pengurusan Sumber MOTAC')), //
      'templateDescription' => config('variables.templateDescription', __('Sistem Dalaman Kementerian Pelancongan, Seni dan Budaya Malaysia untuk Pengurusan Sumber.')),
      'templateKeyword' => config('variables.templateKeyword', __('motac, bpm, sistem dalaman, pengurusan sumber, pinjaman ict, permohonan emel')),
      'appFavicon' => config('variables.appFavicon', 'assets/img/favicon/favicon-motac.ico'),
      'templateLogoSvg' => config('variables.templateLogoSvg', 'assets/img/logo/motac-logo.svg'),

      'productPage' => $isConsole ? config('app.url', '/') : url('/'),
      'repositoryUrl' => config('variables.repositoryUrl', ''),

      'locale' => $currentLocale, //
      'style' => $currentStyle,
      'theme' => $themeCustomConfig['myTheme'] ?? 'theme-motac', // Example theme name
      'layout' => $layout, //

      'isMenu' => $themeCustomConfig['isMenu'] ?? true,
      'isNavbar' => $themeCustomConfig['isNavbar'] ?? true,
      'isFooter' => $themeCustomConfig['isFooter'] ?? true,
      'contentNavbar' => $themeCustomConfig['contentNavbar'] ?? ($layout === 'horizontal' ? false : true),

      'menuFixed' => $themeCustomConfig['menuFixed'] ?? true,
      'menuCollapsed' => $themeCustomConfig['menuCollapsed'] ?? false,
      'navbarFixed' => $themeCustomConfig['navbarFixed'] ?? true,
      'navbarDetached' => $themeCustomConfig['navbarDetached'] ?? ($layout === 'vertical' ? true : false),
      'footerFixed' => $themeCustomConfig['footerFixed'] ?? false,

      'container' => $themeCustomConfig['container'] ?? 'container-fluid',
      'containerNav' => $themeCustomConfig['containerNav'] ?? 'container-fluid',

      'hasCustomizer' => $themeCustomConfig['hasCustomizer'] ?? false,
      'displayCustomizer' => $themeCustomConfig['displayCustomizer'] ?? false,
      'customizerHidden' => !($themeCustomConfig['displayCustomizer'] ?? false),

      'assetsPath' => $isConsole
        ? (rtrim(config('app.asset_url', config('app.url', '/')), '/') . '/assets/')
        : (function_exists('asset') ? asset('/assets') . '/' : '/assets/'),

      'baseUrl' => $isConsole
        ? config('app.url', '/')
        : (function_exists('url') ? url('/') : '/'),

      'myRTLSupport' => $themeCustomConfig['myRTLSupport'] ?? true,
      'defaultTextDirectionFromConfig' => ($themeCustomConfig['myRTLMode'] ?? false) ? 'rtl' : 'ltr',
      'primaryColor' => $themeCustomConfig['primaryColor'] ?? '#0050A0', // Example Primary Color
      'isFlex' => $themeCustomConfig['isFlex'] ?? false,
      'showMenu' => $themeCustomConfig['showMenu'] ?? true,
      'contentLayout' => $themeCustomConfig['contentLayout'] ?? ($layout === 'horizontal' ? 'compact' : 'wide'),
    ];

    $sessionTextDirection = $isConsole ? null : Session::get('textDirection');
    if (!$sessionTextDirection) {
      $textDirection = ($currentLocale === 'ar' && $defaultData['myRTLSupport']) ? 'rtl' : $defaultData['defaultTextDirectionFromConfig'];
    } else {
      $textDirection = $sessionTextDirection;
    }

    $defaultData['textDirection'] = $textDirection;
    $defaultData['rtlSupport'] = ($textDirection === 'rtl' && $defaultData['myRTLSupport']) ? '/rtl' : '';
    $defaultData['bsTheme'] = ($defaultData['style'] === 'dark') ? 'dark' : 'light';

    return array_merge($defaultData, $themeCustomConfig);
  }

  public static function updatePageConfig(array $pageConfigs): void
  {
    $configBasePath = 'custom.custom';
    if (empty($pageConfigs)) {
      return;
    }
    foreach ($pageConfigs as $configKey => $value) {
      if (is_string($configKey)) {
        Config::set($configBasePath . '.' . $configKey, $value);
      }
    }
  }

  /**
   * Returns Bootstrap 5 CSS color classes for different application statuses.
   * System Design Reference: 6.3 Reusable Blade Components
   */
  public static function getStatusColorClass(string $status, ?string $context = null): string
  {
    $normalizedStatus = strtolower(str_replace([' ', '-'], '_', $status));

    // Constants from respective models as per System Design Section 4
    return match ($normalizedStatus) {
        // User Statuses
        User::STATUS_ACTIVE => 'text-bg-success',
        User::STATUS_INACTIVE => 'text-bg-secondary',

        // EmailApplication Statuses
        EmailApplication::STATUS_DRAFT => 'bg-light text-dark border',
        EmailApplication::STATUS_PENDING_SUPPORT => 'text-bg-warning',
        EmailApplication::STATUS_PENDING_ADMIN => 'text-bg-primary', // Changed from warning for distinction
        EmailApplication::STATUS_APPROVED => 'text-bg-info', // Ready for IT processing
        EmailApplication::STATUS_PROCESSING => 'text-bg-primary',
        EmailApplication::STATUS_COMPLETED => 'text-bg-success',
        EmailApplication::STATUS_REJECTED => 'text-bg-danger',
        EmailApplication::STATUS_PROVISION_FAILED => 'text-danger bg-danger-subtle border border-danger-subtle',

        // LoanApplication Statuses
        LoanApplication::STATUS_DRAFT => 'bg-light text-dark border',
        LoanApplication::STATUS_PENDING_SUPPORT => 'text-bg-warning',
        LoanApplication::STATUS_PENDING_HOD_REVIEW => 'text-bg-warning', // Grouped with other pending for now
        LoanApplication::STATUS_PENDING_BPM_REVIEW => 'text-bg-info', // BPM review is distinct
        LoanApplication::STATUS_APPROVED => 'text-bg-primary', // Approved, ready for issuance
        LoanApplication::STATUS_PARTIALLY_ISSUED => 'text-info bg-info-subtle border border-info-subtle',
        LoanApplication::STATUS_ISSUED => 'text-bg-info',
        LoanApplication::STATUS_RETURNED => 'text-bg-success',
        LoanApplication::STATUS_OVERDUE => 'text-danger bg-danger-subtle border border-danger-subtle',
        LoanApplication::STATUS_CANCELLED => 'text-bg-secondary border',
        // LoanApplication::STATUS_REJECTED is covered below with Approval::STATUS_REJECTED if same class is desired

        // Equipment Statuses (Operational)
        Equipment::STATUS_AVAILABLE => 'text-bg-success',
        Equipment::STATUS_ON_LOAN => 'text-bg-info',
        Equipment::STATUS_UNDER_MAINTENANCE => 'text-bg-warning',
        Equipment::STATUS_DISPOSED => 'text-bg-secondary',
        Equipment::STATUS_LOST => 'text-bg-danger', // Matched with Equipment::CONDITION_LOST
        Equipment::STATUS_DAMAGED_NEEDS_REPAIR => 'text-danger bg-warning-subtle border border-warning-subtle',

        // Equipment Condition Statuses
        Equipment::CONDITION_NEW => 'text-bg-primary',
        Equipment::CONDITION_GOOD => 'text-bg-success',
        Equipment::CONDITION_FAIR => 'text-bg-info',
        Equipment::CONDITION_MINOR_DAMAGE => 'text-warning bg-warning-subtle border border-warning-subtle', // More distinct warning
        Equipment::CONDITION_MAJOR_DAMAGE => 'text-danger bg-danger-subtle border border-danger-subtle',
        Equipment::CONDITION_UNSERVICEABLE => 'text-bg-secondary border',
        // Equipment::CONDITION_LOST is covered by Equipment::STATUS_LOST if same class is desired

        // LoanTransaction Statuses
        LoanTransaction::STATUS_PENDING => 'text-bg-secondary', // Generic pending for new transactions before specific states
        LoanTransaction::STATUS_ISSUED => 'text-bg-info', // Transaction itself is 'issued'
        LoanTransaction::STATUS_RETURNED_PENDING_INSPECTION => 'text-warning bg-warning-subtle border border-warning-subtle',
        LoanTransaction::STATUS_RETURNED_GOOD => 'text-bg-success',
        LoanTransaction::STATUS_RETURNED_DAMAGED => 'text-danger bg-warning-subtle border border-warning-subtle', // More distinct
        LoanTransaction::STATUS_ITEMS_REPORTED_LOST => 'text-bg-danger',
        // LoanTransaction::STATUS_RETURNED_WITH_LOSS => 'text-danger bg-warning-subtle border border-warning-subtle', // Covered by generic damage/loss styling
        // LoanTransaction::STATUS_RETURNED_WITH_DAMAGE_AND_LOSS => 'text-danger bg-warning-subtle border border-warning-subtle', // Covered
        LoanTransaction::STATUS_COMPLETED => 'text-bg-success', // For transaction lifecycle, if applicable
        LoanTransaction::STATUS_CANCELLED => 'text-bg-danger', // If a transaction can be cancelled

        // LoanTransactionItem Statuses
        LoanTransactionItem::STATUS_ITEM_ISSUED => 'text-bg-info',
        LoanTransactionItem::STATUS_ITEM_RETURNED_PENDING_INSPECTION => 'text-bg-warning',
        LoanTransactionItem::STATUS_ITEM_RETURNED_GOOD => 'text-bg-success',
        LoanTransactionItem::STATUS_ITEM_RETURNED_MINOR_DAMAGE => 'text-warning bg-warning-subtle border border-warning-subtle',
        LoanTransactionItem::STATUS_ITEM_RETURNED_MAJOR_DAMAGE => 'text-danger bg-danger-subtle border border-danger-subtle',
        LoanTransactionItem::STATUS_ITEM_REPORTED_LOST => 'text-bg-danger',
        LoanTransactionItem::STATUS_ITEM_UNSERVICEABLE_ON_RETURN => 'text-bg-secondary border',

        // Approval Statuses
        Approval::STATUS_PENDING => 'text-bg-warning',
        Approval::STATUS_APPROVED => 'text-bg-success', // General approval success
        Approval::STATUS_REJECTED => 'text-bg-danger', // General rejection

        default => 'text-dark bg-light border', // Default for unknown statuses
    };
  }

  /**
   * Gets a Bootstrap alert type class.
   */
  public static function getAlertClass(string $statusType): string
  {
    return match (strtolower($statusType)) {
      'success' => 'success',
      'info', 'information', 'notice' => 'info',
      'warning' => 'warning',
      'error', 'danger', 'failed' => 'danger',
      default => 'secondary',
    };
  }

  /**
   * Formats a date string or Carbon instance according to application's configured formats.
   * System Design Reference: 3.3 Date Formatting
   */
  public static function formatDate($date, string $formatType = 'date_format_my'): string
  {
    if (!$date instanceof Carbon) {
        try {
            $date = Carbon::parse((string) $date);
        } catch (\Exception $e) {
            // Log::warning("Helpers::formatDate - Could not parse date: " . (string)$date, ['exception' => $e->getMessage()]);
            return '-'; // Return a placeholder for unparseable dates
        }
    }
    // Use config from "Revision 3" (Section 3.3)
    $format = config('app.' . $formatType, ($formatType === 'datetime_format_my' ? 'd/m/Y H:i A' : 'd/m/Y'));
    return $date->translatedFormat($format); // Uses Carbon's localization features
  }
}
