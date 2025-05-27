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
        // Ensure at least one user exists to be the notifiable and for audit, create if none.
        $notifiableUser = User::inRandomOrder()->first() ?? User::factory()->create();
        $auditUserId = $notifiableUser->id; // Default audit user to the notifiable user

        // IMPORTANT: Update this list with your actual Notification class FQCNs
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
            \App\Notifications\ProvisioningFailedNotification::class,
            // Add all other relevant notification classes here
        ];
        $chosenNotificationType = $this->faker->randomElement($notificationTypes);

        // Generic data payload, can be customized further with states if needed
        $payloadData = [
            'message' => $this->faker->sentence,
            'subject' => $this->faker->words(3, true),
            'action_url' => $this->faker->optional(0.5)->url, // 50% chance of having a URL
        ];

        return [
            'id' => Str::uuid()->toString(),
            'type' => $chosenNotificationType,
            'notifiable_type' => User::class, // Assuming notifications are primarily for Users
            'notifiable_id' => $notifiableUser->id,
            'data' => json_encode($payloadData), // Data must be JSON encoded string
            'read_at' => $this->faker->optional(0.3)->dateTimeThisYear(), // 30% chance of being read

            // Audit columns - created_by and updated_by will be set by the model's boot method
            // if Auth::check() is true during seeding, or you can set them explicitly here.
            // For factory seeding, explicitly setting is often more reliable.
            'created_by' => $auditUserId,
            'updated_by' => $auditUserId,
            'deleted_by' => null,
            // created_at and updated_at are handled by Eloquent timestamps.
        ];
    }

    public function unread(): static
    {
        return $this->state(fn (array $attributes) => ['read_at' => null]);
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => ['read_at' => now()]);
    }

    // Example state for a specific type of notification - adapt as needed
    // public function forSomeSpecificAction(Model $relatedModel): static
    // {
    //     return $this->state(function (array $attributes) use ($relatedModel) {
    //         return [
    //             'type' => \App\Notifications\SomeSpecificNotification::class,
    //             'notifiable_id' => $relatedModel->user_id, // Example
    //             'data' => json_encode(['message' => "Specific action on {$relatedModel->id}."]),
    //         ];
    //     });
    // }
}
