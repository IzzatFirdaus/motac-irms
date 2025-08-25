<?php

namespace App\Jobs;

use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Symfony\Component\Process\Process;

/**
<<<<<<< HEAD
 * Job to sync the application codebase with the latest changes from GitHub.
 * Typically triggered by a webhook event (e.g., push).
=======
 * Handles deployment synchronization with Github push events.
 * Runs 'git pull' as a background process.
>>>>>>> release/v4.0
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
<<<<<<< HEAD
        // The webhookCall property contains the webhook payload and metadata.
=======
        // $this->webhookCall contains an instance of WebhookCall
>>>>>>> release/v4.0

        // Create a process to run 'git pull' to fetch any new changes from GitHub.
        $process = new Process(['git', 'pull']);

        info("Start deploy process - Running 'git pull'");

        $alreadyUpToDate = false;
<<<<<<< HEAD

        // Start the process and stream output.
        $process->run(function ($type, $buffer) use (&$alreadyUpToDate): void {
            // Check if output indicates code is already up to date.
            if ($buffer === "Already up to date.\n") {
                $alreadyUpToDate = true;
            }
        });

        // You could also add conditional logging based on $alreadyUpToDate if needed.

        info('Deploy Complete Successfully');
=======
        $process->run(function ($type, $buffer) use (&$alreadyUpToDate): void {
            if ($buffer === "Already up to date.\n") {
                $alreadyUpToDate = true;
            }
            // Optional: Log buffer for debug
            info("Deploy Output: " . $buffer);
        });

        if ($alreadyUpToDate) {
            info('No changes pulled, repo already up to date.');
        } else {
            info('Deploy Complete Successfully');
        }
>>>>>>> release/v4.0
    }
}
