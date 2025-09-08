<?php

namespace App\Notifications;

use App\Models\EmailApplication;
use App\Models\User; // Added for type hinting $notifiable
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route; // Added for consistency

class EmailApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    protected EmailApplication $application;

    public function __construct(EmailApplication $application)
    {
        $this->application = $application->loadMissing('user'); // Eager load user
    }

    public function via(User $notifiable): array // Type hinted $notifiable
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage // Type hinted $notifiable
    {
        $applicationId = $this->application->id ?? 'N/A';
        $applicantName = $this->application->user?->name ?? $notifiable->name ?? __('Pengguna');

        // Assuming EmailApplication model has getStatusOptions() or STATUS_OPTIONS for labels
        $statusText = $this->application->status ? (EmailApplication::getStatusOptions()[$this->application->status] ?? ucfirst(str_replace('_', ' ', $this->application->status))) : __('Tidak Diketahui');

        $showRoute = '#';
        $routeName = 'resource-management.my-applications.email.show'; // Standardized route
        if ($this->application->id && Route::has($routeName)) {
            try {
                $showRoute = route($routeName, $this->application->id);
            } catch (\Exception $e) {
                Log::error("Error generating route for EmailApplicationSubmitted notification: {$e->getMessage()}", ['application_id' => $applicationId, 'route_name' => $routeName]);
                $showRoute = '#'; // Fallback
            }
        }

        return (new MailMessage())
            ->subject(__("Permohonan Akaun E-mel/ID Pengguna Dihantar (#:id)", ['id' => $applicationId]))
            ->greeting(__('Salam Sejahtera, :name!', ['name' => $applicantName]))
            ->line(__('Permohonan anda untuk Akaun E-mel / ID Pengguna MOTAC (#:id) telah berjaya dihantar.', ['id' => $applicationId]))
            ->line(__('Status semasa permohonan anda ialah: **:status**', ['status' => $statusText]))
            ->action(__('Lihat Status Permohonan'), ($showRoute !== '#') ? $showRoute : url('/')) // Provide a generic fallback if route fails
            ->line(__('Anda akan dimaklumkan melalui e-mel mengenai sebarang kemas kini permohonan anda.'))
            ->salutation(__('Sekian, terima kasih.'));
    }

    public function toArray(User $notifiable): array // Type hinted $notifiable
    {
        $applicationId = $this->application->id ?? null;
        $applicantName = $this->application->user?->name ?? __('Pengguna');
        $statusText = $this->application->status ? (EmailApplication::getStatusOptions()[$this->application->status] ?? ucfirst(str_replace('_', ' ', $this->application->status))) : __('Tidak Diketahui');

        $showRoute = '#';
        $routeName = 'resource-management.my-applications.email.show';
        if ($applicationId && Route::has($routeName)) {
            try {
                $showRoute = route($routeName, $applicationId);
            } catch (\Exception $e) {
                 Log::error("Error generating URL for EmailApplicationSubmitted toArray: " . $e->getMessage(), ['application_id' => $applicationId]);
                 $showRoute = '#'; // Fallback
            }
        }

        return [
            'application_id' => $applicationId,
            'application_type_morph' => $this->application->getMorphClass(),
            'applicant_name' => $applicantName,
            'status_key' => $this->application->status ?? 'N/A',
            'status_display' => $statusText,
            'subject' => __('Permohonan E-mel Dihantar (#:id)', ['id' => $applicationId ?? 'N/A']),
            'message' => __('Permohonan e-mel/ID pengguna anda #:id oleh :name telah dihantar.', ['id' => $applicationId ?? 'N/A', 'name' => $applicantName]),
            'url' => ($showRoute !== '#') ? $showRoute : null,
            'icon' => 'ti ti-mail-forward',
        ];
    }
}
