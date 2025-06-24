<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\EmailApplication;
use App\Models\User; // Ensured User model is imported
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString; // Correct import for HtmlString

/**
 * Class ProvisioningFailedNotification
 *
 * Notification sent to IT Admins/BPM staff when automated provisioning for an Email Application fails.
 * This notification alerts staff to a technical issue requiring manual intervention.
 */
class ProvisioningFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The Email Application model instance related to the provisioning failure.
     */
    protected EmailApplication $application;

    /**
     * The error message from the provisioning attempt.
     */
    protected string $errorMessage;

    /**
     * The admin user who triggered the process (optional).
     */
    protected ?User $adminUser;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\EmailApplication  $application  The application related to the failure.
     * @param  string  $errorMessage  The error message from the provisioning attempt.
     * @param  \App\Models\User|null  $adminUser  The admin user who triggered the process (optional).
     */
    public function __construct(
        EmailApplication $application,
        string $errorMessage,
        ?User $adminUser = null
    ) {
        $this->application = $application;
        $this->application->loadMissing('user');
        $this->errorMessage = $errorMessage;
        $this->adminUser = $adminUser;

        Log::info(
            'ProvisioningFailedNotification created for EmailApplication ID: '.
            ($this->application->id ?? 'N/A').
            ('. Error: '.$this->errorMessage).
            ($this->adminUser instanceof \App\Models\User ? '. Triggered by Admin ID: '.$this->adminUser->id : '')
        );

        // The warning for null user is good as is.
        if ($this->application->user === null) {
            Log::warning(
                'ProvisioningFailedNotification: Applicant user relationship is null for application ID '.
                  ($this->application->id ?? 'N/A').
                  '.'
            );
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $notifiable): MailMessage
    {
        /** @phpstan-ignore-next-line nullsafe.neverNull, nullCoalesce.expr */
        $applicantName = $this->application->user?->name ?? __('Tidak Dikenali');
        $applicationId = $this->application->id ?? 'N/A';
        $triggeredByAdminInfo = $this->adminUser instanceof \App\Models\User ? __(' Proses dicetuskan oleh: :adminName (ID: :adminId).', ['adminName' => $this->adminUser->name, 'adminId' => $this->adminUser->id]) : '';

        $viewUrl = '#';
        $adminRouteName = 'admin.email-applications.show'; // Prefer admin-specific route
        $genericRouteName = 'email-applications.show';

        if ($this->application->id !== null) {
            if (Route::has($adminRouteName)) {
                try {
                    $viewUrl = route($adminRouteName, ['email_application' => $this->application->id]);
                } catch (\Exception $e) {
                    Log::error('Error generating admin route for ProvisioningFailedNotification mail: '.$e->getMessage(), ['application_id' => $this->application->id, 'route_name' => $adminRouteName]);
                    // Try generic route if admin route fails or is not available
                    if (Route::has($genericRouteName)) {
                        try {
                            $viewUrl = route($genericRouteName, ['email_application' => $this->application->id]);
                        } catch (\Exception $e_generic) {
                            Log::error('Error generating generic route for ProvisioningFailedNotification mail: '.$e_generic->getMessage(), ['application_id' => $this->application->id, 'route_name' => $genericRouteName]);
                        }
                    }
                }
            } elseif (Route::has($genericRouteName)) {
                try {
                    $viewUrl = route($genericRouteName, ['email_application' => $this->application->id]);
                } catch (\Exception $e_generic) {
                    Log::error('Error generating generic route for ProvisioningFailedNotification mail: '.$e_generic->getMessage(), ['application_id' => $this->application->id, 'route_name' => $genericRouteName]);
                }
            }
        }

        if ($viewUrl === '#') {
            Log::warning(
                'Could not generate a valid URL for ProvisioningFailedNotification mail.',
                ['application_id' => $this->application->id ?? 'N/A']
            );
        }

        $mailMessage = (new MailMessage)
            ->subject(__('Amaran: Gagal Memproses Akaun E-mel/ID Pengguna - Permohonan #:applicationId', ['applicationId' => $applicationId]))
            ->greeting(__('Salam Pentadbir ICT,'))
            ->error() // Mark as important
            ->line(__('Proses penyediaan akaun e-mel/ID pengguna untuk permohonan berikut telah **GAGAL**:'))
            ->line(__('- Jenis Permohonan: :type', ['type' => __('Akaun E-mel/ID Pengguna')]))
            ->line(__('- ID Permohonan: #:id', ['id' => $applicationId]))
            ->line(__('- Pemohon: :name', ['name' => $applicantName]));

        if ($this->application->proposed_email) {
            $mailMessage->line(__('- Cadangan E-mel/ID: :email', ['email' => $this->application->proposed_email]));
        }

        $mailMessage->line('')
            ->line(__('Ralat yang berlaku:'))
            // Using HtmlString to allow preformatted error message display if it contains HTML or newlines.
            ->line(new HtmlString("<pre style='background-color: #f8f8f8; padding: 10px; border: 1px solid #ddd; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word;'>".htmlspecialchars($this->errorMessage).'</pre>'))
            ->line('');

        $mailMessage->line(__('Tindakan manual mungkin diperlukan untuk menyelesaikan isu ini.'.$triggeredByAdminInfo))
            ->line(__('Sila semak log sistem untuk maklumat lanjut dan ambil tindakan pembetulan yang sewajarnya.'));

        if ($viewUrl !== '#') {
            $mailMessage->action(__('Lihat Permohonan E-mel'), $viewUrl);
        }

        return $mailMessage->salutation(__('Sekian, harap maklum.'));
    }

    /**
     * Get the array representation of the notification for the database channel.
     *
     * @param  \App\Models\User  $notifiable  The user who needs to be notified (IT Admin/BPM Staff).
     * @return array<string, mixed> An array of data to be stored.
     */
    public function toArray(User $notifiable): array
    {
        $applicationId = $this->application->id ?? null;
        /** @phpstan-ignore-next-line */
        $applicantId = $this->application->user?->id ?? null;
        /** @phpstan-ignore-next-line */
        $applicantName = $this->application->user?->name ?? __('Tidak Diketahui');
        /** @phpstan-ignore-next-line */
        $adminUserId = $this->adminUser?->id ?? null;
        /** @phpstan-ignore-next-line */
        $adminUserName = $this->adminUser?->name ?? __('Sistem Automasi');

        $applicationUrl = '#';
        $adminRouteName = 'admin.email-applications.show';
        $genericRouteName = 'email-applications.show';

        if ($applicationId !== null) {
            if (Route::has($adminRouteName)) {
                try {
                    $applicationUrl = route($adminRouteName, ['email_application' => $applicationId]);
                } catch (\Exception $e) {
                    Log::error('Error generating admin URL for ProvisioningFailedNotification toArray: '.$e->getMessage(), ['application_id' => $applicationId, 'route_name' => $adminRouteName]);
                    if (Route::has($genericRouteName)) {
                        try {
                            $applicationUrl = route($genericRouteName, ['email_application' => $applicationId]);
                        } catch (\Exception $e_generic) {
                            Log::error('Error generating generic URL for ProvisioningFailedNotification toArray: '.$e_generic->getMessage(), ['application_id' => $applicationId, 'route_name' => $genericRouteName]);
                        }
                    }
                }
            } elseif (Route::has($genericRouteName)) {
                try {
                    $applicationUrl = route($genericRouteName, ['email_application' => $applicationId]);
                } catch (\Exception $e_generic) {
                    Log::error('Error generating generic URL for ProvisioningFailedNotification toArray: '.$e_generic->getMessage(), ['application_id' => $applicationId, 'route_name' => $genericRouteName]);
                }
            }
        }

        if ($applicationUrl === '#') {
            Log::warning(
                'Could not generate a valid URL for in-app ProvisioningFailedNotification.',
                ['application_id' => $applicationId]
            );
        }

        // Assume EmailApplication::STATUS_PROVISION_FAILED exists in your EmailApplication model
        // e.g., public const STATUS_PROVISION_FAILED = 'provision_failed';
        $statusKey = defined(EmailApplication::class.'::STATUS_PROVISION_FAILED')
            ? EmailApplication::STATUS_PROVISION_FAILED
            : 'provisioning_failed';

        return [
            'application_type_display' => __('Permohonan E-mel/ID Pengguna'),
            'application_type_morph' => $this->application->getMorphClass(),
            'email_application_id' => $applicationId, // Retain for specific reference if needed
            'application_id' => $applicationId, // Generic reference
            'applicant_id' => $applicantId,
            'applicant_name' => $applicantName,
            'error_message' => $this->errorMessage,
            'status_key' => $statusKey,
            'subject' => __('Gagal Memproses Akaun E-mel/ID Pengguna (#:id)', ['id' => $applicationId ?? 'N/A']),
            'message' => __(
                'Penyediaan akaun e-mel/ID pengguna untuk :applicant_name (Permohonan #:application_id) telah GAGAL. Ralat: :error',
                [
                    'applicant_name' => $applicantName,
                    'application_id' => $applicationId ?? 'N/A',
                    'error' => substr($this->errorMessage, 0, 100).(strlen($this->errorMessage) > 100 ? '...' : ''), // Show a snippet of the error
                ]
            ),
            'url' => ($applicationUrl !== '#' && filter_var($applicationUrl, FILTER_VALIDATE_URL))
                ? $applicationUrl
                : null,
            'triggered_by_admin_id' => $adminUserId,
            'triggered_by_admin_name' => $adminUserName,
            'icon' => 'ti ti-alert-octagon', // Tabler icon for failure/critical alert
            'action_required' => true,
        ];
    }
}
