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

    private function formatDate($date, $format = null): string
    {
        $format = $format ?? config('app.date_format_my', 'd/m/Y');
        if ($date instanceof Carbon) {
            return $date->format($format);
        }
        if (is_string($date)) {
            try {
                return Carbon::parse($date)->format($format);
            } catch (\Exception $e) {
                return __('Tidak dinyatakan');
            }
        }
        return __('Tidak dinyatakan');
    }


    public function toMail(User $notifiable): MailMessage
    {
        $applicantName = $this->application->user->name ?? $notifiable->name ?? 'Pemohon';
        $applicationId = $this->application->id ?? 'N/A';

        $isLoanApp = $this->application instanceof LoanApplication;
        $applicationTypeDisplay = $isLoanApp
          ? __('Permohonan Pinjaman Peralatan ICT')
          : __('Permohonan Akaun E-mel/ID Pengguna');

        $mailMessage = (new MailMessage())
            ->subject(__(':appType Diluluskan (#:id)',['appType' => $applicationTypeDisplay, 'id' => $applicationId]))
            ->greeting(__("Salam Sejahtera, :name,", ['name' => $applicantName]))
            ->line(__(':appType anda dengan ID #:id telah **diluluskan**.', ['appType' => $applicationTypeDisplay, 'id' => $applicationId]));


        if ($isLoanApp) {
            /** @var LoanApplication $loanApp */
            $loanApp = $this->application;
            $startDate = $this->formatDate($loanApp->loan_start_date, config('app.datetime_format_my', 'd/m/Y H:i A'));
            $endDate = $this->formatDate($loanApp->loan_end_date, config('app.datetime_format_my', 'd/m/Y H:i A'));

            if ($loanApp->purpose) {
                $mailMessage->line(__('Tujuan: :purpose', ['purpose' => $loanApp->purpose]));
            }
            $mailMessage->line(__('Tempoh Pinjaman: Dari :startDate hingga :endDate', ['startDate' => $startDate, 'endDate' => $endDate]));
            $mailMessage->line(__('Sila berhubung dengan pegawai berkaitan di Bahagian Perkhidmatan Pengurusan Bangunan & Aset (BPM) untuk urusan pengambilan peralatan.'));

        } else {
            /** @var EmailApplication $emailApp */
            $emailApp = $this->application;
            if ($emailApp->application_reason_notes) {
                 $mailMessage->line(__('Tujuan/Catatan: :reason', ['reason' => $emailApp->application_reason_notes]));
            }
             $mailMessage->line(__('Pihak BPM akan memproses permohonan anda dan akan memaklumkan setelah akaun/ID pengguna anda sedia untuk digunakan.'));
        }

        $mailMessage
            ->line(''); // Empty line for spacing

        $viewUrl = '#';
        $routeParameters = [];
        $routeName = null;

        if ($this->application->id) {
            if ($isLoanApp) {
                $routeName = 'resource-management.my-applications.loan.show'; // Or specific admin view if for BPM
                $routeParameters = ['loan_application' => $this->application->id];
            } else {
                $routeName = 'resource-management.my-applications.email.show';
                $routeParameters = ['email_application' => $this->application->id];
            }

            if ($routeName && Route::has($routeName)) {
                try {
                    $viewUrl = route($routeName, $routeParameters);
                } catch (\Exception $e) {
                    Log::error('Error generating URL for ApplicationApproved mail: '.$e->getMessage(), [
                        'exception' => $e,
                        'application_id' => $this->application->id ?? null,
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
        $mailMessage->line(__('Terima kasih.'));

        return $mailMessage->salutation(__('Sekian, harap maklum.'));
    }

    public function toArray(User $notifiable): array
    {
        $applicationId = $this->application->id ?? null;
        $isLoanApp = $this->application instanceof LoanApplication;
        $applicationTypeDisplay = $isLoanApp
          ? __('Permohonan Pinjaman Peralatan ICT')
          : __('Permohonan Akaun E-mel/ID Pengguna');
        $applicationMorphClass = $this->application->getMorphClass();


        $subjectText = __(':appType Diluluskan', ['appType' => $applicationTypeDisplay]);
        if ($applicationId !== null) {
            $subjectText .= __(" (#:id)", ['id' => $applicationId]);
        }

        $messageText = __('Permohonan :type anda (#:id) telah diluluskan.', [
            'type' => $applicationTypeDisplay,
            'id' => $applicationId ?? 'N/A',
        ]);

        $data = [
            'application_id' => $applicationId,
            'application_type_morph' => $applicationMorphClass,
            'application_type_display' => $applicationTypeDisplay,
            'applicant_user_id' => $this->application->user_id ?? $notifiable->id,
            'status_key' => 'approved', // Generic approved status
            'subject' => $subjectText,
            'message' => $messageText,
            'icon' => 'ti ti-circle-check',
        ];

        if ($isLoanApp) {
            /** @var LoanApplication $loanApp */
            $loanApp = $this->application;
            $data['purpose'] = $loanApp->purpose ?? null;
            $data['loan_start_date'] = $this->formatDate($loanApp->loan_start_date);
            $data['loan_end_date'] = $this->formatDate($loanApp->loan_end_date);
        } else {
            /** @var EmailApplication $emailApp */
            $emailApp = $this->application;
            $data['application_reason_notes'] = $emailApp->application_reason_notes ?? null;
        }


        $applicationUrl = '#';
        $routeParameters = [];
        $routeName = null;

         if ($applicationId !== null) {
            if ($isLoanApp) {
                $routeName = 'resource-management.my-applications.loan.show';
                $routeParameters = ['loan_application' => $applicationId];
            } else {
                $routeName = 'resource-management.my-applications.email.show';
                $routeParameters = ['email_application' => $applicationId];
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
        $data['url'] = ($applicationUrl !== '#' && filter_var($applicationUrl, FILTER_VALIDATE_URL)) ? $applicationUrl : null;


        return $data;
    }
}
