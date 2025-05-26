<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\SyncAppWithGithub;
use App\Validator\CustomSignatureValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle incoming GitHub webhook for deployment.
     *
     * This method validates the webhook signature and, if valid,
     * dispatches a job to sync the application with the GitHub repository.
     */
    public function handleDeploy(Request $request): JsonResponse
    {
        // 1. Validate Signature
        $githubSignature = $request->header('X-Hub-Signature-256');
        $payload = $request->getContent();
        $secret = config('services.github.webhook_secret'); // Store your secret in config/services.php or .env

        if (empty($secret)) {
            Log::error('GitHub webhook secret is not configured.');
            return response()->json(['message' => 'Webhook secret not configured.'], 500);
        }

        if (empty($githubSignature)) {
            Log::warning('GitHub webhook received without signature.');
            return response()->json(['message' => 'Signature missing.'], 400);
        }

        // Use the CustomSignatureValidator as per the system design [cite: 1]
        if (! CustomSignatureValidator::isValid($githubSignature, $payload, $secret)) {
            Log::warning('Invalid GitHub webhook signature received.');
            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        // 2. Verify Event Type and Branch
        $githubEvent = $request->header('X-GitHub-Event');
        if ($githubEvent !== 'push') {
            Log::info('GitHub webhook received for non-push event.', ['event' => $githubEvent]);
            return response()->json(['message' => 'Ignoring non-push event.'], 200);
        }

        $data = $request->json()->all();
        $deploymentBranch = config('services.github.deployment_branch', 'refs/heads/main'); // Default to main branch

        if (($data['ref'] ?? null) !== $deploymentBranch) {
            Log::info('GitHub push event for non-deployment branch received.', [
                'received_ref' => $data['ref'] ?? 'N/A',
                'expected_ref' => $deploymentBranch,
            ]);
            return response()->json(['message' => "Push to non-deployment branch ({$data['ref']}) ignored."], 200);
        }

        // 3. Dispatch Deployment Job
        try {
            SyncAppWithGithub::dispatch()->onQueue('deployments'); // Dispatch to a specific queue if desired [cite: 1]
            Log::info('GitHub deployment job dispatched successfully for branch.', ['branch' => $data['ref']]);
            return response()->json(['message' => 'Deployment job dispatched.'], 200);
        } catch (\Exception $e) {
            Log::error('Failed to dispatch GitHub deployment job.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Failed to dispatch deployment job.'], 500);
        }
    }
}
