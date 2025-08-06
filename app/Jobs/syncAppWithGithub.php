<?php

namespace App\Jobs;

use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Symfony\Component\Process\Process;

/**
 * Job to sync the application codebase with the latest changes from GitHub.
 * Typically triggered by a webhook event (e.g., push).
 */
class SyncAppWithGithub extends ProcessWebhookJob
{
    /**
     * Handle the webhook and run 'git pull' to update the app code.
     *
     * @return void
     */
    public function handle(): void
    {
        // The webhookCall property contains the webhook payload and metadata.

        // Create a process to run 'git pull' to fetch any new changes from GitHub.
        $process = new Process(['git', 'pull']);

        info("Start deploy process - Running 'git pull'");

        $alreadyUpToDate = false;

        // Start the process and stream output.
        $process->run(function ($type, $buffer) use (&$alreadyUpToDate): void {
            // Check if output indicates code is already up to date.
            if ($buffer === "Already up to date.\n") {
                $alreadyUpToDate = true;
            }
        });

        // You could also add conditional logging based on $alreadyUpToDate if needed.

        info('Deploy Complete Successfully');
    }
}
