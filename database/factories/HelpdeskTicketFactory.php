<?php

namespace Database\Factories;

use App\Models\HelpdeskTicket;
use App\Models\HelpdeskCategory;
use App\Models\HelpdeskPriority;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Optimized Factory for the HelpdeskTicket model.
 *
 * - Uses static caches for all related model IDs to minimize database queries and maximize performance.
 * - Never creates related records in definition(); expects related records to exist.
 * - All foreign keys (user_id, assigned_to_user_id, category_id, priority_id, etc.) are randomly assigned from cached IDs.
 * - Includes states for setting status and related fields.
 */
class HelpdeskTicketFactory extends Factory
{
    protected $model = HelpdeskTicket::class;

    public function definition(): array
    {
        // --- Static caches for related IDs ---
        static $userIds, $categoryIds, $priorityIds;
        // Cache User IDs
        if (!isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        // Cache Category IDs
        if (!isset($categoryIds)) {
            $categoryIds = HelpdeskCategory::pluck('id')->all();
        }
        // Cache Priority IDs
        if (!isset($priorityIds)) {
            $priorityIds = HelpdeskPriority::pluck('id')->all();
        }

        // Pick random user/category/priority IDs or null if none exist
        $userId = !empty($userIds) ? Arr::random($userIds) : null;
        $assignedToUserId = !empty($userIds) ? Arr::random($userIds) : null;
        $categoryId = !empty($categoryIds) ? Arr::random($categoryIds) : null;
        $priorityId = !empty($priorityIds) ? Arr::random($priorityIds) : null;

        // Use a static Malaysian faker for performance and realism
        static $msFaker;
        if (!$msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }

        // Choose a status from model constants (default is open)
        $statuses = [
            HelpdeskTicket::STATUS_OPEN,
            HelpdeskTicket::STATUS_IN_PROGRESS,
            HelpdeskTicket::STATUS_RESOLVED,
            HelpdeskTicket::STATUS_CLOSED,
        ];
        $status = $this->faker->randomElement($statuses);

        // Set up timestamps
        $createdAt = Carbon::parse($this->faker->dateTimeBetween('-6 months', 'now'));
        $updatedAt = Carbon::parse($this->faker->dateTimeBetween($createdAt, 'now'));

        // Closed fields only set if status is closed
        $closedById = null;
        $closedAt = null;
        if ($status === HelpdeskTicket::STATUS_CLOSED) {
            $closedById = $assignedToUserId;
            $closedAt = Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now'));
        }

        // Resolution notes if resolved or closed
        $resolutionNotes = in_array($status, [HelpdeskTicket::STATUS_RESOLVED, HelpdeskTicket::STATUS_CLOSED])
            ? $msFaker->sentence(8)
            : null;

        // SLA due (optional)
        $slaDueAt = $this->faker->optional(0.4)->dateTimeBetween($createdAt, '+2 weeks');

        // Soft delete fields
        $isDeleted = $this->faker->boolean(2); // ~2% soft deleted
        $deletedAt = $isDeleted ? Carbon::parse($this->faker->dateTimeBetween($updatedAt, 'now')) : null;
        $deletedBy = $isDeleted ? $userId : null;

        // Returned attributes
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
            'sla_due_at'         => $slaDueAt,
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
            'resolution_notes' => null,
        ]);
    }

    /**
     * State for a resolved ticket (not closed).
     */
    public function resolved(): static
    {
        static $msFaker;
        if (!$msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }
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
        static $userIds;
        if (!isset($userIds)) {
            $userIds = User::pluck('id')->all();
        }
        $userId = !empty($userIds) ? Arr::random($userIds) : null;
        static $msFaker;
        if (!$msFaker) {
            $msFaker = \Faker\Factory::create('ms_MY');
        }
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
