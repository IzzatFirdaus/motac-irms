<?php

declare(strict_types=1);

namespace App\Notifications;

// Removed: use App\Models\EmailApplication;
use App\Models\LoanApplication; // Keep this import
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

    public LoanApplication $application; // Updated type hint

    public function __construct(LoanApplication $application) // Updated type hint
    {
        $this->application = $application;
        $this->application->loadMissing('user');
        Log::info('ApplicationApproved notification created for '.$application::class.sprintf(' ID: %d.', $application->id));
    }

    public function getApplication(): LoanApplication // Updated return type
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
        // This condition becomes simpler as only LoanApplication is expected
        $defaultFormat = config('app.datetime_format_my', 'd/m/Y H:i A');


        if ($date instanceof Carbon) {
            return $date->format($defaultFormat);
        }
        if (is_string($date)) {
            try {
                return Carbon::parse($date)->format($defaultFormat);
            } catch (\Exception $e) {
                return __('Tarikh tidak sah');
            }
        }

        return __('Tarikh tidak sah');
    }

    public function toMail(User $notifiable): MailMessage
    {
        $applicantName = $this->application->user?->name ?? $notifiable->name ?? __('Pemohon');
        $applicationTypeDisplay = __('Permohonan Pinjaman Peralatan ICT');
        $applicationId = $this->application->id ?? 'N/A';

        $subject = __('Permohonan :appType Anda Diluluskan (#:appId)', ['appType' => $applicationTypeDisplay, 'appId' => $applicationId]);

        return (new MailMessage)
            ->subject($subject)
            ->greeting(__('Assalamualaikum / Salam Sejahtera, :name,', ['name' => $applicantName]))
            ->line(__('Permohonan :appType anda dengan ID #:appId telah **diluluskan** oleh pihak kami.', ['appType' => $applicationTypeDisplay, 'appId' => $applicationId]))
            ->line(__('Butiran permohonan:'))
            ->line(new \Illuminate\Support\HtmlString('<ul>
                <li><strong>Tujuan:</strong> '.($this->application->purpose ?? __('Tidak dinyatakan')).'</li>
                <li><strong>Lokasi Penggunaan:</strong> '.($this->application->location ?? __('Tidak dinyatakan')).'</li>
                <li><strong>Tarikh Pinjaman:</strong> '.$this->formatDate($this->application->loan_start_date).'</li>
                <li><strong>Tarikh Pemulangan:</strong> '.$this->formatDate($this->application->loan_end_date).'</li>
            </ul>'))
            ->action(__('Lihat Permohonan Anda'), $this->getActionUrl())
            ->line(__('Sila hubungi Bahagian Pengurusan Maklumat untuk urusan pengambilan peralatan.'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(User $notifiable): array
    {
        $applicationId = $this->application->id ?? null;
        $applicationMorphClass = $this->application->getMorphClass();
        $applicationTypeDisplay = __('Permohonan Pinjaman Peralatan ICT'); // Simplified

        $applicantName = $this->application->user?->name ?? __('Pemohon');

        $applicationUrl = '#';
        $routeName = '';
        $routeParameters = [];

        if ($applicationId) {
            // Now only LoanApplication is supported
            $isLoanApp = $this->application instanceof LoanApplication;

            if ($isLoanApp) {
                $routeName = 'resource-management.my-applications.loan.show';
                $routeParameters = ['loan_application' => $applicationId];
            }
            // Removed else block for EmailApplication

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
                    $applicationUrl = '#';
                }
            } elseif ($routeName !== '' && $routeName !== '0') {
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

    private function getActionUrl(): string
    {
        $viewUrl = '#';
        $routeName = 'resource-management.my-applications.loan.show';
        $routeParameters = ['loan_application' => $this->application->id];

        if (Route::has($routeName)) {
            try {
                $viewUrl = route($routeName, $routeParameters);
            } catch (\Exception $e) {
                Log::error('Error generating URL for ApplicationApproved mail: '.$e->getMessage(), [
                    'application_id' => $this->application->id,
                    'application_type' => $this->application->getMorphClass(),
                ]);
            }
        }

        return $viewUrl;
    }
}
