<?php

namespace Database\Factories;

use App\Models\HelpdeskTicket;
use App\Models\HelpdeskCategory;
use App\Models\HelpdeskPriority;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * Factory for the HelpdeskTicket model.
 *
 * Generates helpdesk tickets for testing and seeding,
 * including all required fields, blameable/audit columns, and soft-delete columns.
 *
 * This version is updated to match the schema and model:
 * - Field names now align with migration and model: user_id, assigned_to_user_id, closed_by_id, etc.
 * - Drops reference_number, applicant_id, assigned_to_id (uses correct columns)
 * - Handles closed_at, status, and resolution_notes according to model/migration.
 */
class HelpdeskTicketFactory extends Factory
{
    protected $model = HelpdeskTicket::class;

    public function definition(): array
    {
        // Use Malaysian locale for more realistic content
        $msFaker = \Faker\Factory::create('ms_MY');

        // Foreign key dependencies (create if missing)
        $userId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Applicant (HelpdeskTicketFactory)'])->id;
        $assignedToUserId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Agent (HelpdeskTicketFactory)'])->id;
        $categoryId = HelpdeskCategory::inRandomOrder()->value('id') ?? HelpdeskCategory::factory()->create()->id;
        $priorityId = HelpdeskPriority::inRandomOrder()->value('id') ?? HelpdeskPriority::factory()->create()->id;

        // Status options based on model constants
        $statuses = [
            HelpdeskTicket::STATUS_OPEN,
            HelpdeskTicket::STATUS_IN_PROGRESS,
            HelpdeskTicket::STATUS_RESOLVED,
            HelpdeskTicket::STATUS_CLOSED,
        ];
        $status = $this->faker->randomElement($statuses);

        // Set dates
        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-6 months', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));

        // Closed fields
        $closedById = null;
        $closedAt = null;
        if ($status === HelpdeskTicket::STATUS_CLOSED) {
            $closedById = $assignedToUserId;
            $closedAt = Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now'));
        }

        // Some tickets may be resolved (not closed)
        $resolutionNotes = in_array($status, [HelpdeskTicket::STATUS_RESOLVED, HelpdeskTicket::STATUS_CLOSED])
            ? $msFaker->sentence(8)
            : null;

        // Mark as soft deleted (rare)
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;
        $deletedBy = $isDeleted ? $userId : null;

        return [
            'title'              => $msFaker->sentence(6),
            'description'        => $msFaker->paragraph(3),
            'category_id'        => $categoryId,
            'status'             => $status,
            'priority_id'        => $priorityId,
            'user_id'            => $userId,
            'assigned_to_user_id'=> $assignedToUserId,
            'closed_by_id'       => $closedById,
            'closed_at'          => $closedAt,
            'resolution_notes'   => $resolutionNotes,
            'sla_due_at'         => $this->faker->optional(0.4)->dateTimeBetween($createdAt, '+2 weeks'),
            'created_by'         => $userId,
            'updated_by'         => $userId,
            'deleted_by'         => $deletedBy,
            'created_at'         => $createdAt,
            'updated_at'         => $updatedAt,
            'deleted_at'         => $deletedAt,
        ];
    }

    /**
     * State for an open ticket.
     */
    public function open(): static
    {
        return $this->state([
            'status' => HelpdeskTicket::STATUS_OPEN,
            'closed_by_id' => null,
            'closed_at' => null,
            'resolution_notes' => null,
        ]);
    }

    /**
     * State for an in-progress ticket.
     */
    public function inProgress(): static
    {
        return $this->state([
            'status' => HelpdeskTicket::STATUS_IN_PROGRESS,
            'closed_by_id' => null,
            'closed_at' => null,
        ]);
    }

    /**
     * State for a resolved ticket (not closed).
     */
    public function resolved(): static
    {
        $msFaker = \Faker\Factory::create('ms_MY');
        return $this->state([
            'status' => HelpdeskTicket::STATUS_RESOLVED,
            'resolution_notes' => $msFaker->sentence(8),
            'closed_by_id' => null,
            'closed_at' => null,
        ]);
    }

    /**
     * State for a closed ticket.
     */
    public function closed(): static
    {
        $userId = User::inRandomOrder()->value('id') ?? User::factory()->create()->id;
        $msFaker = \Faker\Factory::create('ms_MY');
        return $this->state([
            'status' => HelpdeskTicket::STATUS_CLOSED,
            'closed_by_id' => $userId,
            'closed_at' => now(),
            'resolution_notes' => $msFaker->sentence(8),
        ]);
    }

    /**
     * State for a ticket assigned to a specific user.
     */
    public function assignedTo(User|int $user): static
    {
        $userId = $user instanceof User ? $user->id : $user;
        return $this->state([
            'assigned_to_user_id' => $userId,
        ]);
    }

    /**
     * State for a ticket under a specific category.
     */
    public function forCategory(HelpdeskCategory|int $category): static
    {
        $categoryId = $category instanceof HelpdeskCategory ? $category->id : $category;
        return $this->state([
            'category_id' => $categoryId,
        ]);
    }

    /**
     * State for a ticket with a specific priority.
     */
    public function withPriority(HelpdeskPriority|int $priority): static
    {
        $priorityId = $priority instanceof HelpdeskPriority ? $priority->id : $priority;
        return $this->state([
            'priority_id' => $priorityId,
        ]);
    }

    /**
     * State for a soft-deleted ticket.
     */
    public function deleted(): static
    {
        $deleterId = User::inRandomOrder()->value('id') ?? User::factory()->create(['name' => 'Deleter User (HelpdeskTicketFactory)'])->id;
        return $this->state([
            'deleted_at' => now(),
            'deleted_by' => $deleterId,
        ]);
    }
}
