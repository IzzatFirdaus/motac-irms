<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Optimized Factory for Notification model.
 *
 * - Uses static cache for User IDs to avoid repeated DB queries for notifiable_id.
 * - Never creates related models in definition() (ensures batch seeding is fast).
 * - Type and notifiable_id can be set via state; otherwise, random from existing.
 * - Use in a seeder only after users exist.
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        // Static cache for user IDs
        static $userIds;
        if (!isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }

        // Pick a random user as notifiable (or null if none exist)
        $notifiableUserId = !empty($userIds) ? Arr::random($userIds) : null;

        // Use a static Malaysian faker for realism and speed
        static $msFaker;
        if (!$msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }

        // Supported notification types (update as needed to match app/Notifications/)
        static $notificationTypes = [
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

        // Supported icons for notification payloads
        static $icons = [
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
            'ti ti-mood-empty',          // Lost
        ];

        // Notification data payload
        $payloadData = [
            'subject'   => $msFaker->sentence(6),
            'message'   => $msFaker->paragraph(2),
            'url'       => $this->faker->optional(0.7)->url,
            'icon'      => Arr::random(self::$icons ?? $icons),
            'status'    => $this->faker->optional(0.6)->randomElement([
                'pending', 'approved', 'rejected', 'issued', 'returned', 'overdue'
            ]),
            'action_required' => $this->faker->optional(0.2)->boolean(),
            'created_at' => now()->toDateTimeString(),
        ];

        // Read at: 30% chance to be read
        $readAt = $this->faker->optional(0.3)->dateTimeThisYear();

        // Audit fields (nullable)
        $createdBy   = $notifiableUserId;
        $updatedBy   = $notifiableUserId;
        $deletedBy   = null;

        return [
            'id'              => (string) Str::uuid(),
            'type'            => Arr::random(self::$notificationTypes ?? $notificationTypes),
            'notifiable_type' => User::class,
            'notifiable_id'   => $notifiableUserId,
            'data'            => $payloadData, // Model will cast to/from array
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
     * State: Mark notification as unread.
     */
    public function unread(): static
    {
        return $this->state([
            'read_at' => null,
        ]);
    }

    /**
     * State: Mark notification as read.
     */
    public function read(): static
    {
        return $this->state([
            'read_at' => now(),
        ]);
    }

    /**
     * State: Mark notification as soft deleted.
     */
    public function deleted(): static
    {
        static $userIds;
        if (!isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        $deleterId = !empty($userIds) ? Arr::random($userIds) : null;
        return $this->state([
            'deleted_at' => now(),
            'deleted_by' => $deleterId,
        ]);
    }
}
