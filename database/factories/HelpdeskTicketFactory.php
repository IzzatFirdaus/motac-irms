<?php

namespace Database\Factories;

use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Models\HelpdeskCategory;
use App\Models\HelpdeskPriority;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class HelpdeskTicketFactory extends Factory
{
    protected $model = HelpdeskTicket::class;

    public function definition(): array
    {
        $msFaker = \Faker\Factory::create('ms_MY');

        $applicant = User::inRandomOrder()->first() ?? User::factory()->create();
        $assignedTo = $this->faker->boolean(70) ? (User::inRandomOrder()->first() ?? User::factory()->create()) : null;

        $category = HelpdeskCategory::inRandomOrder()->first() ?? HelpdeskCategory::factory()->create();
        $priority = HelpdeskPriority::inRandomOrder()->first() ?? HelpdeskPriority::factory()->create();

        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-6 months', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));

        $statusOptions = HelpdeskTicket::STATUS_OPTIONS; // Assuming you have a STATUS_OPTIONS constant in your HelpdeskTicket model
        $status = $this->faker->randomElement(array_keys($statusOptions));

        $dueDate = null;
        if ($this->faker->boolean(60)) {
            $dueDate = Carbon::parse($this->faker->dateTimeBetween($createdAt, $createdAt->copy()->addDays(30)));
        }

        $closedAt = null;
        $resolutionNotes = null;
        if (in_array($status, [HelpdeskTicket::STATUS_RESOLVED, HelpdeskTicket::STATUS_CLOSED])) {
            $closedAt = Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now'));
            $resolutionNotes = $msFaker->paragraph;
        }

        return [
            'user_id' => $applicant->id,
            'assigned_to_user_id' => $assignedTo?->id,
            'category_id' => $category->id,
            'priority_id' => $priority->id,
            'subject' => $msFaker->sentence(mt_rand(3, 8)),
            'description' => $msFaker->paragraphs(mt_rand(1, 3), true),
            'status' => $status,
            'due_date' => $dueDate,
            'resolution_notes' => $resolutionNotes,
            'closed_at' => $closedAt,
            'created_by' => $applicant->id, // Assuming creator is the applicant
            'updated_by' => $applicant->id,
            'deleted_by' => null,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'deleted_at' => null,
        ];
    }

    public function open(): static
    {
        return $this->state(['status' => HelpdeskTicket::STATUS_OPEN]);
    }

    public function inProgress(): static
    {
        return $this->state(['status' => HelpdeskTicket::STATUS_IN_PROGRESS]);
    }

    public function resolved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => HelpdeskTicket::STATUS_RESOLVED,
                'closed_at' => $attributes['closed_at'] ?? now(),
                'resolution_notes' => $attributes['resolution_notes'] ?? $this->faker->paragraph,
            ];
        });
    }

    public function closed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => HelpdeskTicket::STATUS_CLOSED,
                'closed_at' => $attributes['closed_at'] ?? now(),
                'resolution_notes' => $attributes['resolution_notes'] ?? $this->faker->paragraph,
            ];
        });
    }

    public function withApplicant(User|int $user): static
    {
        return $this->state(['user_id' => $user instanceof User ? $user->id : $user]);
    }

    public function withAgent(User|int $user): static
    {
        return $this->state(['assigned_to_user_id' => $user instanceof User ? $user->id : $user]);
    }

    public function withCategory(HelpdeskCategory|int $category): static
    {
        return $this->state(['category_id' => $category instanceof HelpdeskCategory ? $category->id : $category]);
    }

    public function withPriority(HelpdeskPriority|int $priority): static
    {
        return $this->state(['priority_id' => $priority instanceof HelpdeskPriority ? $priority->id : $priority]);
    }
}
