<?php

namespace Database\Factories;

use App\Models\Approval;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

// use Illuminate\Support\Arr; // Not explicitly used after review

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Approval>
 */
class ApprovalFactory extends Factory
{
    protected $model = Approval::class;

    public function definition(): array
    {
        $officer = User::inRandomOrder()->first() ?? User::factory()->create(['name' => 'Officer Fallback (ApprovalFactory)']);
        // For created_by/updated_by, BlameableObserver handles it if Auth::user() exists.
        // If seeding without an authenticated user, these might need a fallback.
        // However, Approval model design suggests these are nullable or handled by an observer.
        // Let's assume BlameableObserver handles these if auth()->user() is available, otherwise they remain null or set by seeder context.
        // $auditUser = User::orderBy('id')->first() ?? User::factory()->create(['name' => 'Audit User Fallback (ApprovalFactory)']);

        $stages = Approval::getStages(); // Uses the getter from Approval model
        $stageKeys = array_keys($stages);

        return [
            'officer_id' => $officer->id,
            'stage' => empty($stageKeys) ? 'support_review' : $this->faker->randomElement($stageKeys), // Fallback stage if getStages is empty
            'status' => Approval::STATUS_PENDING,
            'comments' => null,
            'approval_timestamp' => null,
            // 'created_by' => $auditUser->id, // Typically handled by BlameableObserver
            // 'updated_by' => $auditUser->id, // Typically handled by BlameableObserver
            'deleted_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function status(string $statusValue): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $statusValue,
            'approval_timestamp' => in_array($statusValue, [Approval::STATUS_APPROVED, Approval::STATUS_REJECTED])
                                    ? ($attributes['approval_timestamp'] ?? now()) // Set timestamp if approved/rejected
                                    : null,
            'comments' => $statusValue === Approval::STATUS_REJECTED && empty($attributes['comments'])
                            ? $this->faker->sentence() // Add a comment if rejected and no comment exists
                            : ($attributes['comments'] ?? null),
        ]);
    }

    public function approved(): static
    {
        return $this->status(Approval::STATUS_APPROVED)->state(fn (array $attributes) => [
            'comments' => $attributes['comments'] ?? $this->faker->optional()->sentence(),
        ]);
    }

    public function rejected(): static
    {
        return $this->status(Approval::STATUS_REJECTED)->state(fn (array $attributes) => [
            'comments' => $attributes['comments'] ?? $this->faker->sentence(), // Ensure rejection has a comment
        ]);
    }

    public function pending(): static
    {
        return $this->status(Approval::STATUS_PENDING)->state(fn (array $attributes) => [
            'comments' => null,
            'approval_timestamp' => null,
        ]);
    }

    public function stage(string $stage): static
    {
        // Ensure stage is valid if possible by checking against Approval::getStages() keys
        $validStages = array_keys(Approval::getStages());
        if (!in_array($stage, $validStages) && !empty($validStages)) {
            // Log warning or use a default valid stage
            // For now, we'll allow it, assuming validation occurs elsewhere or stages can be dynamic.
        }
        return $this->state(fn (array $attributes) => ['stage' => $stage]);
    }

    public function deleted(): static
    {
        // BlameableObserver should handle deleted_by if Auth::user() exists.
        // This state ensures deleted_at is set for soft delete simulation.
        return $this->state(fn (array $attributes) => [
            'deleted_at' => now(),
            // 'deleted_by' => $attributes['deleted_by'] ?? (User::orderBy('id')->first()?->id ?? User::factory()->create()->id), // Fallback if observer not active
        ]);
    }

    public function forApprovable(Model $approvable): static
    {
        return $this->state(fn (array $attributes) => [
            'approvable_type' => $approvable->getMorphClass(),
            'approvable_id' => $approvable->id,
        ]);
    }
}
