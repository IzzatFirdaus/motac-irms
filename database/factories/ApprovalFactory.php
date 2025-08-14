<?php

namespace Database\Factories;

use App\Models\Approval;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Approval>
 */
class ApprovalFactory extends Factory
{
    protected $model = Approval::class;

    public function definition(): array
    {
        // Use a Malaysian locale for faker
        \Faker\Factory::create('ms_MY');

        $officer = User::inRandomOrder()->first() ?? User::factory()->create(['name' => 'Officer Fallback (ApprovalFactory)']);
        $stageKeys = Approval::getStageKeys();

        return [
            'officer_id' => $officer->id,
            'stage' => $stageKeys === [] ? 'support_review' : $this->faker->randomElement($stageKeys), // Fallback stage
            'status' => Approval::STATUS_PENDING,
            'comments' => null,
            'approval_timestamp' => null,
            'deleted_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function status(string $statusValue): static
    {
        return $this->state(function (array $attributes) use ($statusValue): array {
            // Use a Malaysian locale for faker
            $msFaker = \Faker\Factory::create('ms_MY');

            return [
                'status' => $statusValue,
                'approval_timestamp' => in_array($statusValue, [Approval::STATUS_APPROVED, Approval::STATUS_REJECTED])
                                        ? ($attributes['approval_timestamp'] ?? now())
                                        : null,
                'comments' => $statusValue === Approval::STATUS_REJECTED && empty($attributes['comments'])
                                ? $msFaker->sentence() // Add a Malay comment if rejected
                                : ($attributes['comments'] ?? null),
            ];
        });
    }

    public function approved(): static
    {
        return $this->status(Approval::STATUS_APPROVED)->state(function (array $attributes): array {
            // Use a Malaysian locale for faker
            $msFaker = \Faker\Factory::create('ms_MY');

            return [
                'comments' => $attributes['comments'] ?? $msFaker->optional()->sentence(),
            ];
        });
    }

    public function rejected(): static
    {
        return $this->status(Approval::STATUS_REJECTED)->state(function (array $attributes): array {
            // Use a Malaysian locale for faker
            $msFaker = \Faker\Factory::create('ms_MY');

            return [
                // Ensure rejection has a comment in Malay
                'comments' => $attributes['comments'] ?? $msFaker->sentence(),
            ];
        });
    }

    public function pending(): static
    {
        return $this->status(Approval::STATUS_PENDING)->state(fn (array $attributes): array => [
            'comments' => null,
            'approval_timestamp' => null,
        ]);
    }

    public function stage(string $stage): static
    {
        $validStages = Approval::getStageKeys();

        if (! in_array($stage, $validStages) && $validStages !== []) {
            // Optional: Log a warning for invalid stages.
        }

        return $this->state(fn (array $attributes): array => ['stage' => $stage]);
    }

    public function deleted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'deleted_at' => now(),
        ]);
    }

    public function forApprovable(Model $approvable): static
    {
        return $this->state(fn (array $attributes): array => [
            'approvable_type' => $approvable->getMorphClass(),
            'approvable_id' => $approvable->id,
        ]);
    }
}
