<?php

namespace Database\Seeders;

use App\Models\LoanApplication;
use App\Models\User; // Import User for dependency check
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // For truncate
use Illuminate\Support\Facades\Log; // For logging

/**
 * Seeder for LoanApplication.
 *
 * This seeder generates a variety of loan applications reflecting different workflow stages.
 * It ensures that each application has appropriate relationships and status to support
 * realistic workflow testing and development.
 *
 * The factory must be aligned with all required states and relationships.
 */
class LoanApplicationSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('Starting Loan Application seeding...');

        // Dependency check - can't proceed if there are no users
        if (User::count() === 0) {
            Log::error(
                'No Users found. Cannot seed Loan Applications. Please run UserSeeder first.'
            );
            return;
        }

        $auditUserId = User::first()?->id ?? User::factory()->create()->id;

        // Create 15 draft applications (not yet submitted)
        LoanApplication::factory()
            ->count(15)
            ->draft()
            ->create([
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
            ]);
        Log::info('Created 15 draft loan applications.');

        // Create 10 certified & pending support applications
        LoanApplication::factory()
            ->count(10)
            ->certified()
            ->pendingSupport()
            ->create([
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
            ]);
        Log::info('Created 10 certified & pending support loan applications.');

        // Create 12 approved applications with items
        LoanApplication::factory()
            ->count(12)
            ->approved()
            ->withItems(2) // Custom state: create 2 items per application
            ->create([
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
            ]);
        Log::info('Created 12 approved loan applications with items.');

        // Create 8 issued applications with items
        LoanApplication::factory()
            ->count(8)
            ->issued()
            ->withItems(1)
            ->create([
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
            ]);
        Log::info('Created 8 issued loan applications with items.');

        // Create 6 returned applications with items
        LoanApplication::factory()
            ->count(6)
            ->returned()
            ->withItems(1)
            ->create([
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
            ]);
        Log::info('Created 6 returned loan applications with items.');

        // Create 5 rejected applications
        LoanApplication::factory()
            ->count(5)
            ->rejected()
            ->create([
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
            ]);
        Log::info('Created 5 rejected loan applications.');

        // Create 3 cancelled applications
        LoanApplication::factory()
            ->count(3)
            ->cancelled()
            ->create([
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
            ]);
        Log::info('Created 3 cancelled loan applications.');

        // Create 2 soft-deleted applications for testing soft deletion
        LoanApplication::factory()
            ->count(2)
            ->deleted()
            ->create([
                'created_by' => $auditUserId,
                'updated_by' => $auditUserId,
                'deleted_by' => $auditUserId,
            ]);
        Log::info('Created 2 deleted loan applications.');

        Log::info('Loan Application seeding complete.');
    }
}
