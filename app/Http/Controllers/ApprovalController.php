<?php

// File: app/Http\Controllers/ApprovalController.php

namespace App\Http\Controllers;

use App\Http\Requests\RecordApprovalDecisionRequest; // v2
use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\ApprovalService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class ApprovalController extends Controller
{
    protected ApprovalService $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->middleware('auth');
        $this->approvalService = $approvalService;
    }

    public function index(): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        Log::debug("ApprovalController@index: Fetching 'pending' approval tasks for officer ID {$user->id}.");
        $pendingApprovals = Approval::where('officer_id', $user->id)
            ->where('status', Approval::STATUS_PENDING)
            ->with([
                'approvable' => function ($morphTo) {
                    $morphTo->morphWith([
                        EmailApplication::class => ['user:id,name'],
                        LoanApplication::class => ['user:id,name'],
                    ]);
                },
                 // Eager load the officer who needs to approve (which is the current user, but good for consistency)
                'officer:id,name'
            ])
            ->orderBy('created_at', 'asc')
            ->paginate(config('pagination.default_size', 15));
        Log::debug("ApprovalController@index: Fetched {$pendingApprovals->total()} pending approval tasks.");
        return view('approvals.index', ['approvals' => $pendingApprovals]);
    }

    public function showHistory(): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        Log::debug("ApprovalController@showHistory: Fetching 'completed' approval tasks for officer ID {$user->id}.");
        $completedApprovals = Approval::where('officer_id', $user->id)
            ->whereIn('status', [Approval::STATUS_APPROVED, Approval::STATUS_REJECTED])
            ->with([
                'approvable' => function ($morphTo) {
                    $morphTo->morphWith([
                        EmailApplication::class => ['user:id,name'],
                        LoanApplication::class => ['user:id,name'],
                    ]);
                },
                'officer:id,name' // The officer who made the decision (current user in this context)
            ])
            ->orderBy('updated_at', 'desc')
            ->paginate(config('pagination.default_size', 15));
        Log::debug("ApprovalController@showHistory: Fetched {$completedApprovals->total()} completed approval tasks.");
        return view('approvals.history', ['approvals' => $completedApprovals]);
    }

    public function show(Approval $approval): View|RedirectResponse
    {
        try {
            $this->authorize('view', $approval);
        } catch (AuthorizationException $e) {
            Log::warning("ApprovalController@show: Authorization failed for Approval ID {$approval->id}. User ID: ".Auth::id().". Error: {$e->getMessage()}");
            return redirect()->route('approvals.index')->with('error', __('Anda tidak mempunyai kebenaran untuk melihat tugasan kelulusan ini.'));
        }
        Log::debug("ApprovalController@show: Loading approval task ID {$approval->id}.");
        if (method_exists($approval, 'loadDefaultRelationships')) { // Assumes Approval model might have this
            $approval->loadDefaultRelationships();
        } else {
            $approval->loadMissing([
                'approvable.user:id,name,department_id,grade_id', // User who made the application
                'approvable.user.department:id,name',
                'approvable.user.grade:id,name',
                'officer:id,name,grade_id', // Officer assigned this approval task
                'officer.grade:id,name',
                // If approvable is EmailApplication, load its specific relations
                'approvable' => function ($morphTo) {
                    $morphTo->morphWith([
                        EmailApplication::class => [
                            'user.position:id,name',
                            'supportingOfficer:id,name', // The nominated supporting officer on the application
                            'supportingOfficer.grade:id,name',
                        ],
                        LoanApplication::class => [
                            'user.position:id,name',
                            'responsibleOfficer:id,name',
                            'supportingOfficer:id,name',
                            'applicationItems',
                        ],
                    ]);
                }
            ]);
        }
        return view('approvals.show', compact('approval'));
    }

    public function recordDecision(
        RecordApprovalDecisionRequest $request, // v2
        Approval $approval
    ): RedirectResponse {
        /** @var User $processingUser */
        $processingUser = $request->user();
        $validatedData = $request->validated();

        Log::info("ApprovalController@recordDecision: User ID {$processingUser->id} recording decision for Approval Task ID {$approval->id}.",
            ['decision' => $validatedData['decision'], 'has_comments' => !empty($validatedData['comments'])]
        );
        try {
            $this->approvalService->processApprovalDecision(
                $approval,
                $validatedData['decision'],
                $processingUser,
                $validatedData['comments'] ?? null
            );
            $decisionText = $validatedData['decision'] === Approval::STATUS_APPROVED ? __('DILULUSKAN') : __('DITOLAK');
            $message = __('Keputusan untuk tugasan #:taskId telah berjaya direkodkan sebagai :decision.', ['taskId' => $approval->id, 'decision' => $decisionText]);
            Log::info("Decision '{$validatedData['decision']}' recorded for Approval ID {$approval->id} by User ID {$processingUser->id}.");
            return redirect()->route('approvals.dashboard')->with('success', $message);
        } catch (AuthorizationException $e) {
            Log::error("ApprovalController@recordDecision: Authorization error for Approval ID {$approval->id}. User ID: {$processingUser->id}.", ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', __('Anda tidak mempunyai kebenaran untuk membuat keputusan ini.'));
        } catch (Throwable $e) {
            Log::error("ApprovalController@recordDecision: Error processing approval for ID {$approval->id}. User ID: {$processingUser->id}.", [
                'error' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 500),
                'request_data' => $request->except(['_token', '_method']),
            ]);
            return redirect()->back()->withInput()->with('error', __('Gagal merekod keputusan disebabkan oleh ralat sistem: ') . $e->getMessage());
        }
    }
}
