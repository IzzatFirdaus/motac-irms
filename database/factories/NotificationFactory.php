<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory for Notification model.
 *
 * Generates fake notification records for various notification types
 * used in the application, matching the fields in the migration,
 * model, and typical payloads from the app\Notifications directory.
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        // Use Malaysian locale for more realistic sample data
        $msFaker = \Faker\Factory::create('ms_MY');

        // Pick a random user as the notifiable entity (required for notifiable_type/id)
        $notifiableUser = User::inRandomOrder()->first() ?? User::factory()->create();

        // All supported notification classes (see app\Notifications)
        $notificationTypes = [
            \App\Notifications\ApplicationApproved::class,
            \App\Notifications\ApplicationNeedsAction::class,
            \App\Notifications\ApplicationRejected::class,
            \App\Notifications\ApplicationStatusUpdatedNotification::class,
            \App\Notifications\DefaultNotification::class,
            \App\Notifications\DefaultUserNotification::class,
            \App\Notifications\EquipmentIncidentNotification::class,
            \App\Notifications\EquipmentIssuedNotification::class,
            \App\Notifications\EquipmentReturnedNotification::class,
            \App\Notifications\EquipmentOverdueNotification::class,
            \App\Notifications\EquipmentReturnReminderNotification::class,
            \App\Notifications\LoanApplicationReadyForIssuanceNotification::class,
            \App\Notifications\OrphanedApplicationRequiresAttentionNotification::class,
            \App\Notifications\SupportPendingApprovalNotification::class,
            \App\Notifications\TicketAssignedNotification::class,
            \App\Notifications\TicketCommentAddedNotification::class,
            \App\Notifications\TicketCreatedNotification::class,
            \App\Notifications\TicketStatusUpdatedNotification::class,
        ];
        $chosenType = $this->faker->randomElement($notificationTypes);

        // A variety of icons as used in the notification payloads
        $icons = [
            'ti ti-circle-check',        // Approved
            'ti ti-bell-ringing',        // Needs action
            'ti ti-circle-x',            // Rejected
            'ti ti-refresh-alert',       // Status update
            'ti ti-file-invoice',        // Loan submit
            'ti ti-mail-forward',        // Email submit
            'ti ti-user-check',          // Provisioned
            'ti ti-alert-triangle',      // Incident / Orphaned / Action needed
            'ti ti-transfer-out',        // Issued
            'ti ti-transfer-in',         // Returned
            'ti ti-calendar-event',      // Reminder
            'ti ti-alarm-snooze',        // Overdue
            'ti ti-package',             // Ready for issuance
            'ti ti-alert-octagon',       // Provisioning failed
            'ti ti-info-circle',         // Generic info
            'ti ti-alert-circle',        // Incident
            'ti ti-mood-empty'           // Lost
        ];
        $chosenIcon = $this->faker->randomElement($icons);

        // Generate the notification data payload, mimicking the structure from app\Notifications
        $payloadData = [
            'subject'   => $msFaker->sentence(6),
            'message'   => $msFaker->paragraph(2),
            'url'       => $this->faker->optional(0.7)->url,
            'icon'      => $chosenIcon,
            // Additional fields for certain notification types (optional, for realism)
            'status'    => $this->faker->optional(0.6)->randomElement(['pending', 'approved', 'rejected', 'issued', 'returned', 'overdue']),
            'action_required' => $this->faker->optional(0.2)->boolean(),
            'created_at' => now()->toDateTimeString(),
        ];

        // Set read_at randomly (some notifications are read, some are unread)
        $readAt = $this->faker->optional(0.3)->dateTimeThisYear();

        // Audit fields (nullable, as handled by observers in the model)
        $createdBy   = $notifiableUser->id;
        $updatedBy   = $notifiableUser->id;
        $deletedBy   = null;

        return [
            'id'              => Str::uuid()->toString(),
            'type'            => $chosenType,
            'notifiable_type' => User::class,
            'notifiable_id'   => $notifiableUser->id,
            'data'            => $payloadData, // Model will cast this to/from array
            'read_at'         => $readAt,
            'created_by'      => $createdBy,
            'updated_by'      => $updatedBy,
            'deleted_by'      => $deletedBy,
            'created_at'      => now(),
            'updated_at'      => now(),
            'deleted_at'      => null,
        ];
    }

    /**
     * Indicate that the notification is unread.
     */
    public function unread(): static
    {
        return $this->state([
            'read_at' => null,
        ]);
    }

    /**
     * Indicate that the notification has been read.
     */
    public function read(): static
    {
        return $this->state([
            'read_at' => now(),
        ]);
    }

    /**
     * Mark the notification as soft deleted.
     */
    public function deleted(): static
    {
        $deleterId = User::inRandomOrder()->value('id') ?? User::factory()->create()->id;
        return $this->state([
            'deleted_at' => now(),
            'deleted_by' => $deleterId,
        ]);
    }
}
