<?php

namespace App\Jobs;

use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Symfony\Component\Process\Process;

class SyncAppWithGithub extends ProcessWebhookJob
{
    public function handle(): void
    {
        // $this->webhookCall // contains an instance of `WebhookCall`

        $process = new Process(['git', 'pull']);
        info("Start deploy process - Running 'git pull'");

        $process->run(function ($type, $buffer): void {

            if ($buffer == "Already up to date.\n") {
                $alreadyUpToDate = true;
            }

        });

        info('Deploy Complete Successfully');
    }
}
