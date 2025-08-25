<?php

namespace Database\Seeders;

use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Optimized seeder for LoanApplication.
 *
 * This version minimizes database calls and leverages batched factory creation for improved performance.
 * It also avoids repeated lookups by caching IDs and uses afterCreating hooks only where needed.
 */
class LoanApplicationSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('Starting Loan Application seeding (Optimized)...');

        // Dependency check - can't proceed if there are no users
        $userCount = User::count();
        if ($userCount === 0) {
            Log::error('No Users found. Cannot seed Loan Applications. Please run UserSeeder first.');
            return;
        }

        // Get a user ID for audit columns (avoiding repeated lookups)
        $auditUserId = User::query()->value('id') ?? User::factory()->create()->id;

        // Pre-fetch user IDs for applicants and officers (for more realistic/random distribution)
        $userIds = User::pluck('id')->all();
        shuffle($userIds);

        // How many of each type to create
        $counts = [
            'draft' => 15,
            'certified_pending_support' => 10,
            'approved_with_items' => 12,
            'issued_with_items' => 8,
            'returned_with_items' => 6,
            'rejected' => 5,
            'cancelled' => 3,
            'deleted' => 2,
        ];

        // 1. Draft applications (batch create, no after-creation hooks needed)
        LoanApplication::factory()
            ->count($counts['draft'])
            ->draft()
            ->create([
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
                // Randomize applicant
                'user_id' => function () use (&$userIds) {
                    return array_pop($userIds) ?? User::inRandomOrder()->value('id');
                }
            ]);
        Log::info("Created {$counts['draft']} draft loan applications.");

        // 2. Certified & pending support (batch create, combine states)
        LoanApplication::factory()
            ->count($counts['certified_pending_support'])
            ->certified()
            ->pendingSupport()
            ->create([
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
                'user_id' => function () use (&$userIds) {
                    return array_pop($userIds) ?? User::inRandomOrder()->value('id');
                }
            ]);
        Log::info("Created {$counts['certified_pending_support']} certified & pending support loan applications.");

        // 3. Approved applications with items (must use afterCreating for withItems)
        LoanApplication::factory()
            ->count($counts['approved_with_items'])
            ->approved()
            ->withItems(2)
            ->create([
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
                'user_id' => function () use (&$userIds) {
                    return array_pop($userIds) ?? User::inRandomOrder()->value('id');
                }
            ]);
        Log::info("Created {$counts['approved_with_items']} approved loan applications with items.");

        // 4. Issued applications with items (must use afterCreating for withItems)
        LoanApplication::factory()
            ->count($counts['issued_with_items'])
            ->issued()
            ->withItems(1)
            ->create([
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
                'user_id' => function () use (&$userIds) {
                    return array_pop($userIds) ?? User::inRandomOrder()->value('id');
                }
            ]);
        Log::info("Created {$counts['issued_with_items']} issued loan applications with items.");

        // 5. Returned applications with items (must use afterCreating for withItems)
        LoanApplication::factory()
            ->count($counts['returned_with_items'])
            ->returned()
            ->withItems(1)
            ->create([
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
                'user_id' => function () use (&$userIds) {
                    return array_pop($userIds) ?? User::inRandomOrder()->value('id');
                }
            ]);
        Log::info("Created {$counts['returned_with_items']} returned loan applications with items.");

        // 6. Rejected applications (batch create, no after-creation hooks)
        LoanApplication::factory()
            ->count($counts['rejected'])
            ->rejected()
            ->create([
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
                'user_id' => function () use (&$userIds) {
                    return array_pop($userIds) ?? User::inRandomOrder()->value('id');
                }
            ]);
        Log::info("Created {$counts['rejected']} rejected loan applications.");

        // 7. Cancelled applications (batch create, no after-creation hooks)
        LoanApplication::factory()
            ->count($counts['cancelled'])
            ->cancelled()
            ->create([
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
                'user_id' => function () use (&$userIds) {
                    return array_pop($userIds) ?? User::inRandomOrder()->value('id');
                }
            ]);
        Log::info("Created {$counts['cancelled']} cancelled loan applications.");

        // 8. Soft-deleted applications (batch create)
        LoanApplication::factory()
            ->count($counts['deleted'])
            ->deleted()
            ->create([
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
                'deleted_by' => $auditUserId,
                'user_id' => function () use (&$userIds) {
                    return array_pop($userIds) ?? User::inRandomOrder()->value('id');
                }
            ]);
        Log::info("Created {$counts['deleted']} deleted loan applications.");

        Log::info('Loan Application seeding complete (Optimized).');
    }
}
