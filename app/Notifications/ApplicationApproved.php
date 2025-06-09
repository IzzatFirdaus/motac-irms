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

    public EmailApplication|LoanApplication $application;

    public function __construct(EmailApplication|LoanApplication $application)
    {
        $this->application = $application;
        $this->application->loadMissing('user');
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

    public function formatDate($date, $format = null): string
    {
        $defaultFormat = $format ?? config('app.date_format_my', 'd/m/Y');
        if ($this->application instanceof LoanApplication && $format === null) {
            $defaultFormat = config('app.datetime_format_my', 'd/m/Y H:i A');
        }

        if ($date instanceof Carbon) {
            return $date->format($defaultFormat);
        }
        if (is_string($date)) {
            try {
                return Carbon::parse($date)->format($defaultFormat);
            } catch (\Exception $e) {
                return __('Tidak dinyatakan');
            }
        }

        return __('Tidak dinyatakan');
    }

    public function toMail(User $notifiable): MailMessage
    {
        $isLoanApp = $this->application instanceof LoanApplication;
        $applicationTypeDisplay = $isLoanApp
            ? __('Permohonan Pinjaman Peralatan ICT')
            : __('Permohonan Akaun E-mel/ID Pengguna');
        $applicationId = $this->application->id ?? 'N/A';
        $subject = __(':appType Diluluskan (#:id)', ['appType' => $applicationTypeDisplay, 'id' => $applicationId]);

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.application-approved', ['notification' => $this]);
    }

    public function getActionUrl(): string
    {
        $viewUrl = '#';
        $isLoanApp = $this->application instanceof LoanApplication;

        if ($this->application->id) {
            $routeName = $isLoanApp
                ? 'resource-management.my-applications.loan.show'
                : 'resource-management.my-applications.email.show';
            $routeParameters = $isLoanApp
                ? ['loan_application' => $this->application->id]
                : ['email_application' => $this->application->id];

            if (Route::has($routeName)) {
                try {
                    $viewUrl = route($routeName, $routeParameters);
                } catch (\Exception $e) {
                    Log::error('Error generating URL for ApplicationApproved mail: '.$e->getMessage(), ['application_id' => $this->application->id]);

                    return '#';
                }
            }
        }

        return $viewUrl;
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
            $subjectText .= __(' (#:id)', ['id' => $applicationId]);
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
            'status_key' => 'approved',
            'subject' => $subjectText,
            'message' => $messageText,
            'icon' => 'ti ti-circle-check',
        ];

        if ($isLoanApp) {
            /** @var LoanApplication $loanApp */
            $loanApp = $this->application;
            $data['purpose'] = $loanApp->purpose ?? null;
            // Using date_format_my for consistency in array data if datetime is not needed
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
            } elseif ($routeName) { // Log if route name was set but not found
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
