<?php

namespace App\Notifications;

use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/**
 * Class ApplicationSubmitted
 *
 * Generic notification sent to the applicant when their application (Loan or Email) has been submitted.
 */
final class ApplicationSubmitted extends Notification implements ShouldQueue
{
  use Queueable;

  private EmailApplication|LoanApplication $application;

  public function __construct(EmailApplication|LoanApplication $application)
  {
    $this->application = $application;
    if ($this->application instanceof LoanApplication) {
      $this->application->loadMissing(['user', 'responsibleOfficer', 'items']);
    } else {
      $this->application->loadMissing(['user']);
    }
    Log::info('ApplicationSubmitted notification created for ' . $application::class . " ID: {$application->id}.");
  }

  public function getApplication(): EmailApplication|LoanApplication
  {
    return $this->application;
  }

  public function via(User $notifiable): array
  {
    return ['mail', 'database'];
  }

  private function formatDate($date): string
  {
    if ($date instanceof Carbon) {
      return $date->format(config('app.date_format_my', 'd/m/Y'));
    }
    if (is_string($date)) {
      try {
        return Carbon::parse($date)->format(config('app.date_format_my', 'd/m/Y'));
      } catch (\Exception $e) {
        return __('Tidak dinyatakan');
      }
    }
    return __('Tidak dinyatakan');
  }

  public function toMail(User $notifiable): MailMessage
  {
    $applicantName = $this->application->user?->name ?? $notifiable->name ?? __('Pemohon');
    $applicationId = $this->application->id ?? 'N/A';

    $isLoanApp = $this->application instanceof LoanApplication;
    $applicationTypeDisplay = $isLoanApp
      ? __('Permohonan Pinjaman Peralatan ICT')
      : __('Permohonan Akaun E-mel/ID Pengguna');

    $mailMessage = (new MailMessage())
      ->subject(__(':appType Dihantar (#:id)', ['appType' => $applicationTypeDisplay, 'id' => $applicationId]))
      ->greeting(__('Salam Sejahtera, :name!', ['name' => $applicantName]))
      ->line(__(':appType anda dengan ID #:id telah berjaya dihantar.', ['appType' => $applicationTypeDisplay, 'id' => $applicationId]));

    if ($isLoanApp) {
      /** @var LoanApplication $loanApp */
      $loanApp = $this->application;
      $startDate = $this->formatDate($loanApp->loan_start_date);
      $endDate = $this->formatDate($loanApp->loan_end_date);

      $mailMessage->line(__('Tujuan: :purpose', ['purpose' => $loanApp->purpose ?? __('Tidak dinyatakan')]));
      $mailMessage->line(__('Tarikh Pinjaman: :startDate hingga :endDate', ['startDate' => $startDate, 'endDate' => $endDate]));

      if ($loanApp->items && $loanApp->items->count() > 0) {
        $mailMessage->line(''); // Empty line for spacing
        $mailMessage->line(__('Butiran Peralatan Dimohon:'));
        foreach ($loanApp->items as $item) {
          $mailMessage->line(__('- :eqType (Kuantiti: :qty)', ['eqType' => $item->equipment_type, 'qty' => $item->quantity_requested]));
        }
      }
    } else {
      /** @var EmailApplication $emailApp */
      $emailApp = $this->application;
      // $statusText = $emailApp->status ? (EmailApplication::getStatusOptions()[$emailApp->status] ?? ucfirst(str_replace('_', ' ', $emailApp->status))) : __('Tidak Diketahui');
      // $mailMessage->line(__('Status semasa permohonan anda ialah: **:status**', ['status' => $statusText]));
      if ($emailApp->application_reason_notes) {
        $mailMessage->line(__('Tujuan/Catatan: :reason', ['reason' => $emailApp->application_reason_notes]));
      }
    }
    $mailMessage->line(''); // Empty line for spacing
    $mailMessage->line(__('Permohonan anda sedang dalam proses semakan. Anda akan dimaklumkan mengenai status permohonan ini dari semasa ke semasa.'));

    $viewUrl = '#';
    $routeName = null;
    $routeParameters = [];

    if ($this->application->id) {
      if ($isLoanApp) {
        $routeName = 'resource-management.my-applications.loan.show';
        $routeParameters = ['loan_application' => $this->application->id];
      } else {
        $routeName = 'resource-management.my-applications.email.show';
        $routeParameters = ['email_application' => $this->application->id];
      }

      if ($routeName && Route::has($routeName)) {
        try {
          $viewUrl = route($routeName, $routeParameters);
        } catch (\Exception $e) {
          Log::error('Error generating URL for ApplicationSubmitted mail: ' . $e->getMessage(), [
            'application_id' => $this->application->id,
            'application_type' => $this->application::class,
            'route_name' => $routeName,
          ]);
          $viewUrl = '#'; // Fallback
        }
      }
    }

    if ($viewUrl !== '#' && filter_var($viewUrl, FILTER_VALIDATE_URL)) {
      $mailMessage->action(__('Lihat Permohonan'), $viewUrl);
    }

    return $mailMessage->salutation(__('Sekian, terima kasih.'));
  }

  public function toArray(User $notifiable): array
  {
    $applicationId = $this->application->id ?? null;
    $applicantName = $this->application->user?->name ?? $notifiable->name ?? __('Pemohon');
    $applicantId = $this->application->user_id ?? $notifiable->id;
    $applicationMorphClass = $this->application->getMorphClass();

    $isLoanApp = $this->application instanceof LoanApplication;
    $applicationTypeDisplay = $isLoanApp
      ? __('Permohonan Pinjaman Peralatan ICT')
      : __('Permohonan Akaun E-mel/ID Pengguna');

    $statusKey = $this->application->status ?? 'N/A'; // Generic status key

    $data = [
      'application_id' => $applicationId,
      'application_type_morph' => $applicationMorphClass,
      'application_type_display' => $applicationTypeDisplay,
      'applicant_id' => $applicantId,
      'applicant_name' => $applicantName,
      'status_key' => $statusKey,
      'subject' => __(':appType Dihantar (#:id)', ['appType' => $applicationTypeDisplay, 'id' => $applicationId ?? 'N/A']),
      'message' => __(':appType anda (#:id) oleh :name telah dihantar.', ['appType' => $applicationTypeDisplay, 'id' => $applicationId ?? 'N/A', 'name' => $applicantName]),
      'icon' => $isLoanApp ? 'ti ti-archive' : 'ti ti-mail-forward',
    ];

    if ($isLoanApp) {
      /** @var LoanApplication $loanApp */
      $loanApp = $this->application;
      $data['purpose'] = $loanApp->purpose ?? null;
      $data['loan_start_date'] = $this->formatDate($loanApp->loan_start_date);
      $data['loan_end_date'] = $this->formatDate($loanApp->loan_end_date);
      // Ensure LoanApplication::STATUS_PENDING constant exists or use a string 'pending'
      $data['status_key'] = defined(LoanApplication::class . '::STATUS_PENDING') ? LoanApplication::STATUS_PENDING : 'pending_approval';
    } else {
      /** @var EmailApplication $emailApp */
      $emailApp = $this->application;
      $data['application_reason_notes'] = $emailApp->application_reason_notes ?? null;
      // Ensure EmailApplication::STATUS_PENDING_SUPPORT constant exists or use a string
      $data['status_key'] = defined(EmailApplication::class . '::STATUS_PENDING_SUPPORT') ? EmailApplication::STATUS_PENDING_SUPPORT : 'pending_approval';
    }

    $viewUrl = '#';
    $routeName = null;
    $routeParameters = [];

    if ($applicationId) {
      if ($isLoanApp) {
        $routeName = 'resource-management.my-applications.loan.show';
        $routeParameters = ['loan_application' => $applicationId];
      } else {
        $routeName = 'resource-management.my-applications.email.show';
        $routeParameters = ['email_application' => $applicationId];
      }

      if ($routeName && Route::has($routeName)) {
        try {
          $viewUrl = route($routeName, $routeParameters);
        } catch (\Exception $e) {
          Log::error('Error generating URL for ApplicationSubmitted toArray: ' . $e->getMessage(), [
            'application_id' => $applicationId,
            'application_type' => $applicationMorphClass,
            'route_name' => $routeName,
          ]);
          $viewUrl = '#'; // Fallback
        }
      }
    }
    $data['url'] = ($viewUrl !== '#' && filter_var($viewUrl, FILTER_VALIDATE_URL)) ? $viewUrl : null;

    return $data;
  }
}
