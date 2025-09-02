<?php

// backup of clean SyncAppWithGithub
declare(strict_types=1);

namespace App\Jobs;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Symfony\Component\Process\Process;

class SyncAppWithGithub extends ProcessWebhookJob
{
    public function handle(): void
    {
        $process = new Process(['git', 'pull']);
    }
}
