<?php

namespace Database\Factories;

use App\Models\Notification; // Your custom Notification model
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        // Use a Malaysian locale for faker
        $msFaker = \Faker\Factory::create('ms_MY');
        $notifiableUser = User::inRandomOrder()->first() ?? User::factory()->create();

        // Updated and complete list of all notification classes provided.
        $notificationTypes = [
            \App\Notifications\ApplicationApproved::class,
            \App\Notifications\ApplicationNeedsAction::class,
            \App\Notifications\ApplicationRejected::class,
            \App\Notifications\ApplicationStatusUpdatedNotification::class,
            \App\Notifications\ApplicationSubmitted::class,
            \App\Notifications\DefaultNotification::class,
            \App\Notifications\DefaultUserNotification::class,
            \App\Notifications\EmailApplicationReadyForProcessingNotification::class,
            \App\Notifications\EmailProvisionedNotification::class,
            \App\Notifications\EquipmentIncidentNotification::class,
            \App\Notifications\EquipmentIssuedNotification::class,
            \App\Notifications\EquipmentReturnedNotification::class,
            \App\Notifications\EquipmentReturnReminderNotification::class,
            \App\Notifications\LoanApplicationReadyForIssuanceNotification::class,
            \App\Notifications\OrphanedApplicationRequiresAttentionNotification::class,
            \App\Notifications\ProvisioningFailedNotification::class,
        ];
        $chosenNotificationType = $this->faker->randomElement($notificationTypes);

        // Generate a realistic payload with common fields and a wider range of icons.
        $payloadData = [
            'subject' => $msFaker->sentence(6), // Localized subject in Malay.
            'message' => $msFaker->paragraph(2), // Localized message in Malay.
            'url' => $this->faker->optional(0.7)->url,
            'icon' => $this->faker->randomElement([
                'ti ti-circle-check', // Approve
                'ti ti-bell-ringing', // Needs action
                'ti ti-circle-x',     // Reject
                'ti ti-refresh-alert', // Status update
                'ti ti-file-invoice', // Loan submit
                'ti ti-mail-forward', // Email submit
                'ti ti-user-check',   // Provisioned
                'ti ti-alert-triangle', // Incident / Orphaned
                'ti ti-transfer-out', // Issued
                'ti ti-transfer-in',  // Returned
                'ti ti-calendar-event', // Reminder
                'ti ti-alarm-snooze', // Overdue
                'ti ti-package',      // Ready for issuance
                'ti ti-alert-octagon', // Provisioning failed
            ]),
        ];

        return [
            'id' => Str::uuid()->toString(),
            'type' => $chosenNotificationType,
            'notifiable_type' => User::class,
            'notifiable_id' => $notifiableUser->id,
            'data' => json_encode($payloadData),
            'read_at' => $this->faker->optional(0.3)->dateTimeThisYear(),
            // 'created_by', 'updated_by' are handled by BlameableObserver
            'deleted_by' => null,
        ];
    }

    /**
     * Indicate that the notification is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => ['read_at' => null]);
    }

    /**
     * Indicate that the notification has been read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => ['read_at' => now()]);
    }
}
