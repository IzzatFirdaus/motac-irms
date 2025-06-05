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
use Illuminate\Support\Facades\Route; // For checking and generating routes
use Illuminate\Support\Str; // For string manipulations

/**
 * Notification sent to an applicant when their application (Loan or Email) has been successfully submitted.
 * System Design: Section 5.1, 5.2 (Notifications), 9.2, 9.3
 */
final class ApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public EmailApplication|LoanApplication $application;
    public string $applicationTypeDisplay; // Human-readable application type

    /**
     * Create a new notification instance.
     *
     * @param EmailApplication|LoanApplication $application
     */
    public function __construct(EmailApplication|LoanApplication $application)
    {
        $this->application = $application;
        // Eager load necessary relations
        if ($this->application instanceof LoanApplication) {
            $this->application->loadMissing(['user:id,name,email', 'loanApplicationItems:id,loan_application_id,equipment_type,quantity_requested']);
            $this->applicationTypeDisplay = __('Permohonan Pinjaman Peralatan ICT');
        } elseif ($this->application instanceof EmailApplication) {
            $this->application->loadMissing(['user:id,name,email']);
            $this->applicationTypeDisplay = __('Permohonan Akaun E-mel/ID Pengguna');
        } else {
            $this->applicationTypeDisplay = __('Permohonan Umum'); // Fallback
        }
        Log::info('ApplicationSubmitted notification INSTANTIATED for ' . $this->application::class . " ID: {$this->application->id}. Notifying user ID: {$this->application->user_id}");
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  User  $notifiable The user model instance being notified.
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        // System Design 9.5: Channels: Email and Database (in-app)
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     * Design Language: Email Design Specifics (Official Branding & Tone, Clear Subject, Structured Content)
     *
     * @param  User  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(User $notifiable): MailMessage
    {
        $applicantName = $this->application->user?->name ?? $notifiable->name ?? __('Pemohon');
        $applicationId = $this->application->id ?? 'N/A';
        $subject = __(':appType Anda Telah Dihantar (ID: #:id)', ['appType' => $this->applicationTypeDisplay, 'id' => $applicationId]);

        $mailMessage = (new MailMessage())
            ->subject($subject)
            ->greeting(__('Salam Sejahtera, :name,', ['name' => $applicantName]))
            ->line(__(':appType anda dengan nombor rujukan #:id telah berjaya dihantar dan diterima untuk semakan.', [
                'appType' => $this->applicationTypeDisplay,
                'id' => $applicationId
            ]));

        if ($this->application instanceof LoanApplication) {
            /** @var LoanApplication $loanApp */
            $loanApp = $this->application;
            $mailMessage->line(__('Butiran Permohonan Pinjaman:'))
                        ->line(__('Tujuan: :purpose', ['purpose' => Str::limit($loanApp->purpose, 100) ?? __('Tidak dinyatakan')]))
                        ->line(__('Tarikh Pinjaman: :startDate hingga :endDate', [
                            'startDate' => $this->formatDate($loanApp->loan_start_date),
                            'endDate' => $this->formatDate($loanApp->loan_end_date)
                        ]));

            if ($loanApp->loanApplicationItems && $loanApp->loanApplicationItems->count() > 0) {
                $mailMessage->line(" ")->line(__('Senarai Peralatan Dimohon:'));
                foreach ($loanApp->loanApplicationItems as $item) {
                    $mailMessage->line(__('- Jenis: :eqType (Kuantiti: :qty)', [
                        'eqType' => $item->equipment_type,
                        'qty' => $item->quantity_requested
                    ]));
                }
            }
        } elseif ($this->application instanceof EmailApplication) {
            /** @var EmailApplication $emailApp */
            $emailApp = $this->application;
            if ($emailApp->application_reason_notes) {
                $mailMessage->line(__('Tujuan/Catatan Permohonan E-mel: :reason', ['reason' => Str::limit($emailApp->application_reason_notes, 100)]));
            }
            if ($emailApp->proposed_email) {
                $mailMessage->line(__('Cadangan E-mel ID: :email', ['email' => $emailApp->proposed_email]));
            }
        }

        $mailMessage->line(" ") // Empty line for spacing
                    ->line(__('Permohonan anda kini sedang dalam proses semakan oleh pegawai yang bertanggungjawab. Anda akan dimaklumkan melalui e-mel dan notifikasi sistem mengenai sebarang perkembangan status permohonan ini.'))
                    ->line(__('Sila semak status permohonan anda dari semasa ke semasa melalui pautan di bawah atau melalui papan pemuka sistem.'));


        // Generate URL to view the application
        $viewUrl = '#'; // Default fallback
        $routeName = null;
        $routeParameters = [];

        if ($this->application->id) {
            if ($this->application instanceof LoanApplication) {
                $routeName = 'resource-management.my-applications.loan-applications.show'; // Matches web.php
                $routeParameters = ['loan_application' => $this->application->id];
            } elseif ($this->application instanceof EmailApplication) {
                $routeName = 'resource-management.my-applications.email-applications.show'; // Matches web.php
                $routeParameters = ['email_application' => $this->application->id];
            }

            if ($routeName && Route::has($routeName)) {
                try {
                    $viewUrl = route($routeName, $routeParameters);
                } catch (\Exception $e) {
                    Log::error("Error generating URL for ApplicationSubmitted mail notification: " . $e->getMessage(), [
                        'application_id' => $this->application->id,
                        'application_type' => $this->application::class,
                        'route_name' => $routeName,
                    ]);
                    $viewUrl = url('/'); // Fallback to dashboard or home
                }
            } else {
                Log::warning("Route '{$routeName}' not found for ApplicationSubmitted mail notification.", ['application_id' => $this->application->id]);
                $viewUrl = url('/'); // Fallback
            }
        }

        if ($viewUrl !== '#' && filter_var($viewUrl, FILTER_VALIDATE_URL)) {
            $mailMessage->action(__('Lihat Status Permohonan'), $viewUrl);
        }

        return $mailMessage->salutation(__('Sekian, terima kasih.'))
                           ->line(__('Yang menjalankan amanah,'))
                           ->line(__(config('variables.templateName', 'Sistem Pengurusan Sumber MOTAC')));
    }

    /**
     * Get the array representation of the notification (for database storage).
     * System Design 9.5 (Notification model stores data for in-app display)
     *
     * @param  User  $notifiable
     * @return array<string, mixed>
     */
    public function toArray(User $notifiable): array
    {
        $applicationId = $this->application->id ?? null;
        $applicantName = $this->application->user?->name ?? $notifiable->name ?? __('Pemohon');
        $applicationMorphClass = $this->application->getMorphClass(); // e.g., 'App\Models\LoanApplication'

        // Generate URL to view the application for database notification
        $viewUrl = '#'; // Default fallback
        $routeName = null;
        $routeParameters = [];

        if ($applicationId) {
            if ($this->application instanceof LoanApplication) {
                $routeName = 'resource-management.my-applications.loan-applications.show';
                $routeParameters = ['loan_application' => $applicationId];
            } elseif ($this->application instanceof EmailApplication) {
                $routeName = 'resource-management.my-applications.email-applications.show';
                $routeParameters = ['email_application' => $applicationId];
            }

            if ($routeName && Route::has($routeName)) {
                try {
                    $viewUrl = route($routeName, $routeParameters);
                } catch (\Exception $e) {
                    Log::error("Error generating URL for ApplicationSubmitted database notification: " . $e->getMessage(), [
                        'application_id' => $applicationId, 'route_name' => $routeName,
                    ]);
                    $viewUrl = url('/'); // Fallback
                }
            } else {
                Log::warning("Route '{$routeName}' not found for ApplicationSubmitted database notification.", ['application_id' => $applicationId]);
                $viewUrl = url('/'); // Fallback
            }
        }

        return [
            'application_id' => $applicationId,
            'application_type_morph' => $applicationMorphClass,
            'application_type_display' => $this->applicationTypeDisplay, // Human-readable
            'applicant_name' => $applicantName,
            'status_key' => $this->application->status ?? 'submitted', // Reflects current status at time of submission
            'title' => __(':appType Dihantar (ID: #:id)', ['appType' => $this->applicationTypeDisplay, 'id' => $applicationId ?? 'N/A']),
            'message' => __(':appType anda (ID: #:id) telah berjaya dihantar dan sedang diproses.', ['appType' => $this->applicationTypeDisplay, 'id' => $applicationId ?? 'N/A']),
            'icon' => ($this->application instanceof LoanApplication) ? 'ti ti-file-invoice' : 'ti ti-mail-forward', // Example icons
            'url' => ($viewUrl !== '#' && filter_var($viewUrl, FILTER_VALIDATE_URL)) ? $viewUrl : null,
            'notification_type' => 'application_event', // For categorizing notifications
        ];
    }

    /**
     * Format a date string using the application's configured format.
     */
    private function formatDate(?Carbon $date): string
    {
        if ($date instanceof Carbon) {
            // Use translatedFormat for month names if needed, or simple format for d/m/Y
            return $date->translatedFormat(config('app.date_format_my_short', 'd M Y'));
        }
        return __('Tidak Dinyatakan');
    }
}
