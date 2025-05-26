<?php

declare(strict_types=1);

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

final class ApplicationApproved extends Notification implements ShouldQueue
{
    use Queueable;

    private EmailApplication|LoanApplication $application;

    public function __construct(EmailApplication|LoanApplication $application)
    {
        $this->application = $application;
        $this->application->loadMissing('user'); // Eager load user relationship
        Log::info('ApplicationApproved notification created for '.$application::class." ID: {$application->id}.");
    }

    public function getApplication(): EmailApplication|LoanApplication
    {
        return $this->application;
    }

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $applicantName = $this->application->user->name ?? $notifiable->name ?? 'Pemohon';
        $applicationId = $this->application->id ?? 'N/A';

        $applicationTypeDisplay = $this->application instanceof EmailApplication
          ? 'Permohonan Akaun E-mel/ID Pengguna'
          : 'Permohonan Pinjaman Peralatan ICT';

        $purpose = $this->application->purpose ?? null; // Assuming 'purpose' exists on LoanApplication
        $applicationReasonNotes = $this->application->application_reason_notes ?? null; // Assuming this exists on EmailApplication

        // Date handling needs to be conditional as EmailApplication might not have these
        $startDateFormatted = 'N/A';
        $endDateFormatted = 'N/A';
        if ($this->application instanceof LoanApplication) {
            $startDateRaw = $this->application->loan_start_date ?? null;
            $startDateFormatted = $startDateRaw instanceof Carbon ? $startDateRaw->format(config('app.datetime_format_my', 'd/m/Y H:i A')) : ($startDateRaw ?? 'N/A');

            $endDateRaw = $this->application->loan_end_date ?? null;
            $endDateFormatted = $endDateRaw instanceof Carbon ? $endDateRaw->format(config('app.datetime_format_my', 'd/m/Y H:i A')) : ($endDateRaw ?? 'N/A');
        }


        $mailMessage = (new MailMessage())
            ->subject("Permohonan {$applicationTypeDisplay} Diluluskan (#{$applicationId})")
            ->greeting("Assalamualaikum / Salam Sejahtera, {$applicantName},")
            ->line("Permohonan {$applicationTypeDisplay} anda dengan butiran berikut telah **diluluskan**:")
            ->line("ID Permohonan: **#{$applicationId}**");

        if ($this->application instanceof LoanApplication && $purpose) {
            $mailMessage->line("Tujuan: {$purpose}");
            $mailMessage->line("Tarikh Pinjaman: {$startDateFormatted} hingga {$endDateFormatted}");
        } elseif ($this->application instanceof EmailApplication && $applicationReasonNotes) {
            $mailMessage->line("Tujuan/Catatan: {$applicationReasonNotes}");
        }

        $mailMessage
            ->line('') // Empty line for spacing
            ->line('Anda kini boleh meneruskan langkah seterusnya mengikut jenis permohonan yang diluluskan.');

        $viewUrl = '#';
        $routeParameters = [];
        $routeName = null;

        if ($this->application instanceof EmailApplication && $this->application->id) {
            $routeName = 'resource-management.my-applications.email.show'; // Consistent with other notifications
            $routeParameters = ['email_application' => $this->application->id];
        } elseif ($this->application instanceof LoanApplication && $this->application->id) {
            $routeName = 'resource-management.my-applications.loan.show'; // Assuming a similar route exists
            $routeParameters = ['loan_application' => $this->application->id];
        }

        if ($routeName && Route::has($routeName)) {
            try {
                $viewUrl = route($routeName, $routeParameters);
            } catch (\Exception $e) {
                Log::error('Error generating URL for ApplicationApproved notification: '.$e->getMessage(), [
                    'exception' => $e,
                    'application_id' => $this->application->id ?? null,
                    'application_type' => $this->application::class,
                    'route_name' => $routeName,
                ]);
                $viewUrl = '#'; // Fallback
            }
        }


        if ($viewUrl !== '#' && filter_var($viewUrl, FILTER_VALIDATE_URL)) {
            $mailMessage->action('Lihat Permohonan', $viewUrl);
        }

        return $mailMessage->salutation('Sekian, terima kasih.');
    }

    public function toArray(User $notifiable): array
    {
        $applicationId = $this->application->id ?? null;
        $applicationTypeDisplay = $this->application instanceof EmailApplication
          ? 'Permohonan Akaun E-mel/ID Pengguna'
          : 'Permohonan Pinjaman Peralatan ICT';
        $applicationMorphClass = $this->application->getMorphClass();

        $applicationUrl = '#';
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
                    $applicationUrl = route($routeName, $routeParameters);
                } catch (\Exception $e) {
                    Log::error('Error generating URL for ApplicationApproved toArray: '.$e->getMessage(), [
                        'exception' => $e,
                        'application_id' => $applicationId,
                        'application_type' => $applicationMorphClass,
                         'route_name' => $routeName,
                    ]);
                    $applicationUrl = '#'; // Fallback
                }
            } else if ($routeName) {
                 Log::warning('Route not found for in-app ApplicationApproved notification.', [
                    'application_id' => $applicationId,
                    'application_type' => $applicationMorphClass,
                    'route_name' => $routeName,
                ]);
            }
        }

        $subjectText = "Permohonan {$applicationTypeDisplay} Diluluskan";
        if ($applicationId !== null) {
            $subjectText .= " (#{$applicationId})";
        }

        $messageText = __('Permohonan :type anda (#:id) telah diluluskan.', [
            'type' => $applicationTypeDisplay,
            'id' => $applicationId ?? 'N/A',
        ]);


        return [
            'application_id' => $applicationId,
            'application_type_morph' => $applicationMorphClass,
            'application_type_display' => $applicationTypeDisplay,
            'subject' => $subjectText,
            'message' => $messageText,
            'url' => ($applicationUrl !== '#' && filter_var($applicationUrl, FILTER_VALIDATE_URL)) ? $applicationUrl : null,
            'applicant_user_id' => $this->application->user_id ?? $notifiable->id,
            'purpose' => $this->application->purpose ?? ($this->application->application_reason_notes ?? null),
            'icon' => 'ti ti-circle-check', // Example icon
        ];
    }
}
