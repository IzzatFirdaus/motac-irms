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
use Illuminate\Support\Facades\Session;

final class Helpers
{
  public static function appClasses(): array
  {
    $themeCustomConfig = Config::get('custom.custom', []);
    $isConsole = App::runningInConsole();

    $defaultLocale = Config::get('app.locale', 'ms');
    $sessionLocale = $isConsole ? $defaultLocale : Session::get('locale', $defaultLocale);
    $currentLocale = str_replace('_', '-', $sessionLocale);

    $defaultStyle = $themeCustomConfig['myStyle'] ?? 'light';
    $currentStyle = $isConsole ? $defaultStyle : Session::get('theme_style', $defaultStyle);

    $layout = $themeCustomConfig['myLayout'] ?? 'vertical';

    $defaultData = [
      'templateName' => config('variables.templateName', __('Sistem Pengurusan Sumber MOTAC')),
      'templateDescription' => config('variables.templateDescription', __('Sistem Dalaman Kementerian Pelancongan, Seni dan Budaya Malaysia untuk Pengurusan Sumber.')),
      'templateKeyword' => config('variables.templateKeyword', __('motac, bpm, sistem dalaman, pengurusan sumber, pinjaman ict, permohonan emel')),
      'appFavicon' => config('variables.appFavicon', 'assets/img/favicon/favicon-motac.ico'), // Centralized favicon
      'templateLogoSvg' => config('variables.templateLogoSvg', 'assets/img/logo/motac-logo.svg'), // Centralized logo

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
      'primaryColor' => $themeCustomConfig['primaryColor'] ?? '#0050A0',
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

  public static function getStatusColorClass(string $status, ?string $context = null): string
  {
    $normalizedStatus = strtolower(str_replace([' ', '-'], '_', $status));

    // Ensure these constants match the values in your updated Model files
    return match ($normalizedStatus) {
        // User Statuses
        User::STATUS_ACTIVE => 'text-bg-success',
        User::STATUS_INACTIVE => 'text-bg-secondary',

        // EmailApplication Statuses
        EmailApplication::STATUS_DRAFT => 'bg-light text-dark border',
        EmailApplication::STATUS_PENDING_SUPPORT => 'text-bg-warning',
        EmailApplication::STATUS_PENDING_ADMIN => 'text-bg-warning',
        EmailApplication::STATUS_APPROVED => 'text-bg-info',
        EmailApplication::STATUS_PROCESSING => 'text-bg-primary',
        EmailApplication::STATUS_COMPLETED => 'text-bg-success',
        EmailApplication::STATUS_REJECTED => 'text-bg-danger',
        EmailApplication::STATUS_PROVISION_FAILED => 'text-danger bg-danger-subtle border border-danger-subtle',

        // LoanApplication Statuses
        LoanApplication::STATUS_DRAFT => 'bg-light text-dark border',
        LoanApplication::STATUS_PENDING_SUPPORT => 'text-bg-warning',
        LoanApplication::STATUS_PENDING_HOD_REVIEW => 'text-bg-warning',
        LoanApplication::STATUS_PENDING_BPM_REVIEW => 'text-bg-warning',
        LoanApplication::STATUS_APPROVED => 'text-bg-primary',
        LoanApplication::STATUS_PARTIALLY_ISSUED => 'text-info bg-info-subtle border border-info-subtle',
        LoanApplication::STATUS_ISSUED => 'text-bg-info',
        LoanApplication::STATUS_RETURNED => 'text-bg-success',
        LoanApplication::STATUS_OVERDUE => 'text-danger bg-danger-subtle border border-danger-subtle',
        LoanApplication::STATUS_CANCELLED => 'text-bg-secondary border',
        LoanApplication::STATUS_REJECTED => 'text-bg-danger',

        // Equipment Statuses (Operational)
        Equipment::STATUS_AVAILABLE => 'text-bg-success',
        Equipment::STATUS_ON_LOAN => 'text-bg-info',
        Equipment::STATUS_UNDER_MAINTENANCE => 'text-bg-warning',
        Equipment::STATUS_DISPOSED => 'text-bg-secondary',
        Equipment::STATUS_LOST => 'text-bg-danger',
        Equipment::STATUS_DAMAGED_NEEDS_REPAIR => 'text-danger bg-warning-subtle border border-warning-subtle',

        // Equipment Condition Statuses
        Equipment::CONDITION_NEW => 'text-bg-primary',
        Equipment::CONDITION_GOOD => 'text-bg-success',
        Equipment::CONDITION_FAIR => 'text-bg-info',
        Equipment::CONDITION_MINOR_DAMAGE => 'text-bg-warning',
        Equipment::CONDITION_MAJOR_DAMAGE => 'text-danger bg-danger-subtle border border-danger-subtle',
        Equipment::CONDITION_UNSERVICEABLE => 'text-bg-secondary border',
        Equipment::CONDITION_LOST => 'text-bg-danger',

        // LoanTransaction Statuses
        LoanTransaction::STATUS_PENDING => 'text-bg-secondary',
        LoanTransaction::STATUS_ISSUED => 'text-bg-info',
        LoanTransaction::STATUS_RETURNED_PENDING_INSPECTION => 'text-warning bg-warning-subtle border border-warning-subtle',
        LoanTransaction::STATUS_RETURNED_GOOD => 'text-bg-success',
        LoanTransaction::STATUS_RETURNED_DAMAGED => 'text-danger bg-warning-subtle border border-warning-subtle',
        LoanTransaction::STATUS_ITEMS_REPORTED_LOST => 'text-bg-danger',
        LoanTransaction::STATUS_RETURNED_WITH_LOSS => 'text-danger bg-warning-subtle border border-warning-subtle',
        LoanTransaction::STATUS_RETURNED_WITH_DAMAGE_AND_LOSS => 'text-danger bg-warning-subtle border border-warning-subtle',
        LoanTransaction::STATUS_COMPLETED => 'text-bg-success',
        LoanTransaction::STATUS_CANCELLED => 'text-bg-danger',

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
        Approval::STATUS_APPROVED => 'text-bg-success',
        Approval::STATUS_REJECTED => 'text-bg-danger',

        default => 'text-bg-light border',
    };
  }

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
  public static function formatDate($date, string $formatType = 'date_format_my') // Allow specifying format type
{
    if (!$date instanceof \Illuminate\Support\Carbon) {
        try {
            $date = \Illuminate\Support\Carbon::parse((string) $date);
        } catch (\Exception $e) {
            return '-';
        }
    }
    // Use config from "Revision 3" (Section 3.3)
    $format = config('app.' . $formatType, ($formatType === 'datetime_format_my' ? 'd/m/Y H:i A' : 'd/m/Y'));
    return $date->translatedFormat($format);
  }
}
