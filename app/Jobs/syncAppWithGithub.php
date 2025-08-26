<?php

namespace App\Jobs;

use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Symfony\Component\Process\Process;

/**
 * Handles deployment synchronization with Github push events.
 * Runs 'git pull' as a background process.
 */
class SyncAppWithGithub extends ProcessWebhookJob
{
    public function handle(): void
    {
        // $this->webhookCall contains an instance of WebhookCall

        $process = new Process(['git', 'pull']);
        info("Start deploy process - Running 'git pull'");

        $alreadyUpToDate = false;
        $process->run(function ($type, $buffer) use (&$alreadyUpToDate): void {
            if ($buffer === "Already up to date.\n") {
                $alreadyUpToDate = true;
            }
            // Optional: Log buffer for debug
            info('Deploy Output: '.$buffer);
        });

        if ($alreadyUpToDate) {
            info('No changes pulled, repo already up to date.');
        } else {
            info('Deploy Complete Successfully');
        }
    }
}
