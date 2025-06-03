<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\SyncAppWithGithub;
use App\Validator\CustomSignatureValidator; // As per system design [cite: 2]
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle incoming GitHub webhook for deployment.
     * SDD Ref: 3.1
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request): JsonResponse // Renamed from handleDeploy to __invoke for single action controller
    {
        $githubSignature = $request->header('X-Hub-Signature-256');
        $payload = $request->getContent();
        $secret = Config::get('services.github.webhook_secret');

        if (empty($secret)) {
            Log::error('GitHub webhook secret is not configured in services.github.webhook_secret.');
            return response()->json(['message' => 'Webhook secret not configured on server.'], 500);
        }

        if (empty($githubSignature)) {
            Log::warning('GitHub webhook received without X-Hub-Signature-256 header.');
            return response()->json(['message' => 'Signature missing.'], 400);
        }

        // Assumes CustomSignatureValidator::isValid method exists [cite: 2]
        if (!CustomSignatureValidator::isValid($githubSignature, $payload, $secret)) {
            Log::warning('Invalid GitHub webhook signature received.', ['ip_address' => $request->ip()]);
            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        $githubEvent = $request->header('X-GitHub-Event');
        if ($githubEvent !== 'push') {
            Log::info('GitHub webhook received for a non-push event, ignoring.', ['event' => $githubEvent]);
            return response()->json(['message' => 'Event ignored. Only push events are processed.'], 200);
        }

        $data = $request->json()->all();
        $deploymentBranch = Config::get('services.github.deployment_branch', 'refs/heads/main');

        if (($data['ref'] ?? null) !== $deploymentBranch) {
            Log::info('GitHub push event for a non-deployment branch received, ignoring.', [
                'received_ref' => $data['ref'] ?? 'N/A',
                'expected_ref' => $deploymentBranch,
            ]);
            return response()->json(['message' => "Push to branch '{$data['ref']}' ignored. Monitoring '{$deploymentBranch}'."], 200);
        }

        try {
            SyncAppWithGithub::dispatch()->onQueue('deployments'); // Job from SDD [cite: 2]
            Log::info('GitHub deployment job dispatched successfully for branch.', ['branch' => $data['ref']]);
            return response()->json(['message' => 'Deployment job successfully dispatched.'], 200);
        } catch (\Exception $e) {
            Log::critical('Failed to dispatch GitHub deployment job.', [
                'error' => $e->getMessage(),
                'branch' => $data['ref'] ?? 'N/A',
                'exception_trace_snippet' => substr($e->getTraceAsString(), 0, 500),
            ]);
            return response()->json(['message' => 'Server error: Failed to dispatch deployment job.'], 500);
        }
    }
}
