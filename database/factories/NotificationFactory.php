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
        $notifiableUser = User::inRandomOrder()->first() ?? User::factory()->create();
        // $auditUserId = $notifiableUser->id; // No longer explicitly needed here for blameable fields

        // IMPORTANT: Update this list with your actual Notification class FQCNs
        $notificationTypes = [
            \App\Notifications\ApplicationApproved::class,
            \App\Notifications\ApplicationNeedsAction::class,
            // ... (ensure this list is complete)
            \App\Notifications\DefaultNotification::class, // Example placeholder
        ];
        $chosenNotificationType = $this->faker->randomElement($notificationTypes);
        if (empty($notificationTypes) || $chosenNotificationType === \App\Notifications\DefaultNotification::class && count($notificationTypes) === 1) {
             // Fallback if list is empty or only contains placeholder
            $chosenNotificationType = 'App\\Notifications\\GenericAppNotification';
        }


        $payloadData = [
            'message' => $this->faker->sentence,
            'subject' => $this->faker->words(3, true),
            'action_url' => $this->faker->optional(0.5)->url,
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

    public function unread(): static
    {
        return $this->state(fn (array $attributes) => ['read_at' => null]);
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => ['read_at' => now()]);
    }
}
