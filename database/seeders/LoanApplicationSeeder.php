<?php

namespace Database\Seeders;

use App\Models\LoanApplication;
use App\Models\User; // Import User for dependency check
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // For truncate
use Illuminate\Support\Facades\Log; // For logging

class LoanApplicationSeeder extends Seeder
{
  public function run(): void
  {
    Log::info('Starting Loan Application seeding...');

    // Optional: Truncate for a clean seed, ensure FK checks are handled
    // DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
    // DB::table('loan_application_items')->truncate(); // Items first due to FK
    // DB::table('loan_applications')->truncate();
    // DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    // Log::info('Truncated loan_applications and loan_application_items tables.');

    // Dependency check
    if (User::count() === 0) {
      Log::error(
        'No Users found. Cannot seed Loan Applications. Please run UserSeeder first.'
      );

      return;
    }

    $auditUserId = User::first()?->id ?? User::factory()->create()->id;

    LoanApplication::factory()
      ->count(15)
      ->draft()
      ->create(['created_by' => $auditUserId, 'updated_by' => $auditUserId]);
    Log::info('Created 15 draft loan applications.');

    LoanApplication::factory()
      ->count(10)
      ->certified()
      ->pendingSupport()
      ->create(['created_by' => $auditUserId, 'updated_by' => $auditUserId]);
    Log::info('Created 10 certified & pending support loan applications.');

    LoanApplication::factory()
      ->count(12)
      ->approved()
      ->withItems(2) // Example with 2 items
      ->create(['created_by' => $auditUserId, 'updated_by' => $auditUserId]);
    Log::info('Created 12 approved loan applications with items.');

    LoanApplication::factory()
      ->count(8)
      ->issued()
      ->withItems(1)
      ->create(['created_by' => $auditUserId, 'updated_by' => $auditUserId]);
    Log::info('Created 8 issued loan applications with items.');

    LoanApplication::factory()
      ->count(6)
      ->returned()
      ->withItems(1)
      ->create(['created_by' => $auditUserId, 'updated_by' => $auditUserId]);
    Log::info('Created 6 returned loan applications with items.');

    LoanApplication::factory()
      ->count(5)
      ->rejected()
      ->create(['created_by' => $auditUserId, 'updated_by' => $auditUserId]);
    Log::info('Created 5 rejected loan applications.');

    LoanApplication::factory()
      ->count(3)
      ->cancelled()
      ->create(['created_by' => $auditUserId, 'updated_by' => $auditUserId]);
    Log::info('Created 3 cancelled loan applications.');

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
