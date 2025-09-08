<?php
declare(strict_types=1);

namespace App\Jobs;

use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Symfony\Component\Process\Process;

class SyncAppWithGithub extends ProcessWebhookJob
{
    public function handle(): void
    {
        $process = new Process(['git', 'pull']);

        info("Start deploy process - Running 'git pull'");

        $alreadyUpToDate = false;

        $process->run(function (string $type, string $buffer) use (&$alreadyUpToDate): void {
            if (trim($buffer) === 'Already up to date.') {
                $alreadyUpToDate = true;
            }

            info('Deploy Output: ' . $buffer);
        });

        if ($alreadyUpToDate) {
            info('No changes pulled, repo already up to date.');
        } else {
            info('Deploy Complete Successfully');
        }
    }
}
