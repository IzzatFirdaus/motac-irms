<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckOverdueReturns extends Command
{
  /**
   * The name and signature of the console command.\n   *\n   * @var string
   */
  protected $signature = 'loan:check-overdue-returns';

  /**
   * The console command description.\n   *\n   * @var string
   */
  protected $description = 'Check for overdue loan applications and escalate via notification.';

  private NotificationService $notificationService;

  public function __construct(NotificationService $notificationService)
  {
    parent::__construct();
    $this->notificationService = $notificationService;
  }

  /**
   * Execute the console command.\n   */
  public function handle(): int
  {
    $now = Carbon::now();
    $overdueApplications = LoanApplication::query()
      ->whereIn('status', [
        LoanApplication::STATUS_ISSUED,
        LoanApplication::STATUS_PARTIALLY_ISSUED,
      ])
      ->where('loan_end_date', '<', $now)
      ->get();

    if ($overdueApplications->isEmpty()) {
      $this->info('No overdue applications found.');
      return \Symfony\Component\Console\Command\Command::SUCCESS;
    }

    foreach ($overdueApplications as $application) {
      $user = $application->user;
      $latestIssueTransaction = $application->latestIssueTransaction; // Get the latest issue transaction

      if (!$user) {
          Log::warning('Overdue loan application (ID: ' . $application->id . ') has no associated user. Skipping notification.', [
              'loan_application_id' => $application->id,
          ]);
          continue;
      }

      if (!$latestIssueTransaction) {
          Log::warning('Overdue loan application (ID: ' . $application->id . ') has no issue transaction. Skipping notification.', [
              'loan_application_id' => $application->id,
              'user_id' => $user->id,
          ]);
          continue;
      }

      // Cast to int to match the expected type in notifyEquipmentOverdue
      $overdueDays = (int) $now->diffInDays($application->loan_end_date);

      Log::warning('Overdue loan application detected', [
        'loan_application_id' => $application->id,
        'user_id' => $user->id,
        'due_date' => $application->loan_end_date,
        'overdue_days' => $overdueDays,
      ]);

      // Notify applicant using the correct method and parameters
      $this->notificationService->notifyEquipmentOverdue($user, $latestIssueTransaction, $overdueDays);

      // Optionally, notify supervisor or department head if required
      // $this->notificationService->notifySupervisorOfOverdue($application);
    }

    $this->info('Overdue applications processed: ' . $overdueApplications->count());
    return \Symfony\Component\Console\Command\Command::SUCCESS;
  }
}
