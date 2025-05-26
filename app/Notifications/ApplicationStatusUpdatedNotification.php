<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\User; // Added for type hinting $notifiable
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route; // Added for consistency

class ApplicationStatusUpdatedNotification extends Notification implements ShouldQueue
{
  use Queueable;

  private EmailApplication|LoanApplication|Model $application;
  private string $oldStatus;
  private string $newStatus;

  public function __construct(
    EmailApplication|LoanApplication|Model $application,
    string $oldStatus,
    string $newStatus
  ) {
    $this->application = $application;
    $this->oldStatus = $oldStatus;
    $this->newStatus = $newStatus;
    if ($this->application instanceof Model) {
      $this->application->loadMissing('user');
    }
  }

  public function via(User $notifiable): array // Type hinted $notifiable
  {
    return ['mail', 'database'];
  }

  public function toMail(User $notifiable): MailMessage // Type hinted $notifiable
  {
    $applicantName = $this->application->user?->name ?? $notifiable->name ?? __('Pemohon');

    $applicationTypeDisplay = 'Permohonan'; // Default
    if ($this->application instanceof EmailApplication) {
      $applicationTypeDisplay = __('Permohonan Akaun E-mel/ID Pengguna');
    } elseif ($this->application instanceof LoanApplication) {
      $applicationTypeDisplay = __('Permohonan Pinjaman Peralatan ICT');
    }

    $applicationId = $this->application->id ?? 'N/A';
    $applicationSummary = $this->application->purpose ?? ($this->application->application_reason_notes ?? __('Permohonan Berkaitan'));

    // Attempt to get human-readable status labels if possible
    // This assumes your models might have a static method or property for status labels
    $oldStatusDisplay = $this->getStatusLabel($this->application, $this->oldStatus);
    $newStatusDisplay = $this->getStatusLabel($this->application, $this->newStatus);


    $mailMessage = (new MailMessage())
      ->subject(__("Status :appType Anda Dikemaskini (#:appId)", ['appType' => $applicationTypeDisplay, 'appId' => $applicationId]))
      ->greeting(__('Salam Sejahtera, :name,', ['name' => $applicantName]))
      ->line(__("Status :appType anda telah dikemaskini dalam sistem.", ['appType' => $applicationTypeDisplay]))
      ->line(__('**Nombor Rujukan Permohonan:** #:id', ['id' => $applicationId]))
      ->line(__('**Ringkasan:** :summary', ['summary' => $applicationSummary]))
      ->line(__('Status terdahulu: **:oldStatus**', ['oldStatus' => $oldStatusDisplay]))
      ->line(__('Status terkini: **:newStatus**', ['newStatus' => $newStatusDisplay]))
      ->line(__('Sila log masuk ke sistem untuk melihat butiran penuh permohonan anda.'));

    $applicationUrl = '#';
    $routeParameters = [];
    $routeName = null;

    if ($this->application instanceof EmailApplication && $this->application->id) {
      $routeName = 'resource-management.my-applications.email.show';
      $routeParameters = ['email_application' => $this->application->id];
    } elseif ($this->application instanceof LoanApplication && $this->application->id) {
      $routeName = 'resource-management.my-applications.loan.show';
      $routeParameters = ['loan_application' => $this->application->id];
    }

    if ($routeName && Route::has($routeName)) {
      try {
        $applicationUrl = route($routeName, $routeParameters);
      } catch (\Exception $e) {
        Log::error('Error generating URL for ApplicationStatusUpdatedNotification mail: ' . $e->getMessage(), [
          'application_id' => $this->application->id ?? null,
          'route_name' => $routeName,
        ]);
        $applicationUrl = '#'; // Fallback
      }
    }

    if ($applicationUrl !== '#') {
      $mailMessage->action(__('Lihat Permohonan'), $applicationUrl);
    }

    return $mailMessage->salutation(__('Sekian, terima kasih.'));
  }

  public function toArray(User $notifiable): array // Type hinted $notifiable
  {
    $applicationTypeDisplay = __('Permohonan Umum'); // Default
    $applicationMorphClass = $this->application->getMorphClass();
    if ($this->application instanceof EmailApplication) {
      $applicationTypeDisplay = __('Permohonan Akaun E-mel/ID Pengguna');
    } elseif ($this->application instanceof LoanApplication) {
      $applicationTypeDisplay = __('Permohonan Pinjaman Peralatan ICT');
    }

    $applicationId = $this->application->id ?? null;
    $applicantId = $this->application->user_id ?? ($notifiable->id ?? null);

    // Get human-readable status labels
    $oldStatusDisplay = $this->getStatusLabel($this->application, $this->oldStatus);
    $newStatusDisplay = $this->getStatusLabel($this->application, $this->newStatus);

    $applicationUrl = null;
    $routeParameters = [];
    $routeName = null;

    if ($applicationId !== null) {
      if ($this->application instanceof EmailApplication) {
        $routeName = 'resource-management.my-applications.email.show';
        $routeParameters = ['email_application' => $applicationId];
      } elseif ($this->application instanceof LoanApplication) {
        $routeName = 'resource-management.my-applications.loan.show';
        $routeParameters = ['loan_application' => $applicationId];
      }

      if ($routeName && Route::has($routeName)) {
        try {
          $generatedUrl = route($routeName, $routeParameters);
          if (filter_var($generatedUrl, FILTER_VALIDATE_URL)) {
            $applicationUrl = $generatedUrl;
          }
        } catch (\Exception $e) {
          Log::error('Error generating URL for ApplicationStatusUpdatedNotification toArray: ' . $e->getMessage(), [
            'application_id' => $applicationId,
            'route_name' => $routeName,
          ]);
        }
      }
    }

    return [
      'application_type_morph' => $applicationMorphClass,
      'application_type_display' => $applicationTypeDisplay,
      'application_id' => $applicationId,
      'applicant_id' => $applicantId,
      'subject' => __("Status :appType Dikemaskini (#:id)", ['appType' => $applicationTypeDisplay, 'id' => $applicationId ?? 'N/A']),
      'message' => __("Status :appType anda (#:id) telah dikemaskini dari **:oldStatus** ke **:newStatus**.", [
        'appType' => $applicationTypeDisplay,
        'id' => $applicationId ?? 'N/A',
        'oldStatus' => $oldStatusDisplay,
        'newStatus' => $newStatusDisplay,
      ]),
      'url' => $applicationUrl,
      'old_status_key' => $this->oldStatus,
      'new_status_key' => $this->newStatus,
      'old_status_display' => $oldStatusDisplay,
      'new_status_display' => $newStatusDisplay,
      'icon' => 'ti ti-refresh-alert', // Example icon
    ];
  }

  /**
   * Helper to get status label if model provides it.
   */
  private function getStatusLabel(Model $application, string $statusKey): string
  {
    if (method_exists($application, 'getStatusOptions') || property_exists($application, 'STATUS_OPTIONS')) {
      $options = $application::getStatusOptions(); // Assuming a static method or public static property
      return $options[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey));
    }
    return ucfirst(str_replace('_', ' ', $statusKey));
  }
}
