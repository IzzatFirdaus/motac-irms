<?php

// File: app/Http\Controllers/ApprovalController.php

namespace App\Http\Controllers;

use App\Http\Requests\RecordApprovalDecisionRequest; // Using the new Form Request
use App\Models\Approval;                                //
use App\Models\EmailApplication;                       //
use App\Models\LoanApplication;                        //
use App\Models\User;                                   //
use App\Services\ApprovalService;                      //
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

    /**
     * Display a listing of pending approval tasks assigned to the current user.
     */
    public function index(): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // The 'auth' middleware should prevent $user from being null.

        // Optional: General policy check if the user can access the approvals section at all.
        // $this->authorize('viewAny', Approval::class); //

        Log::debug("ApprovalController@index: Fetching 'pending' approval tasks for officer ID {$user->id}.");

        $pendingApprovals = Approval::where('officer_id', $user->id)
            ->where('status', Approval::STATUS_PENDING) //
            ->with([
                'approvable' => function ($morphTo) {
                    $morphTo->morphWith([
                        EmailApplication::class => ['user:id,name'], // Ensure 'name' is the correct attribute on User model
                        LoanApplication::class => ['user:id,name'],  // Ensure 'name' is the correct attribute on User model
                    ]);
                },
                // 'officer:id,name', // The officer is the current $user, so no need to eager load this specifically.
            ])
            ->orderBy('created_at', 'asc')
            ->paginate(config('pagination.default_size', 15));

        Log::debug("ApprovalController@index: Fetched {$pendingApprovals->total()} pending approval tasks.");

        return view('approvals.index', ['approvals' => $pendingApprovals]);
    }

    /**
     * Display a listing of completed approval tasks for the current user.
     * Reminder: Ensure a route is defined for this method in web.php, e.g.,
     * Route::get('/history', [ApprovalController::class, 'showHistory'])->name('history');
     * within the 'approvals' route group.
     */
    public function showHistory(): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Optional: General policy check
        // $this->authorize('viewAny', Approval::class); //

        Log::debug("ApprovalController@showHistory: Fetching 'completed' approval tasks for officer ID {$user->id}.");

        $completedApprovals = Approval::where('officer_id', $user->id)
            ->whereIn('status', [Approval::STATUS_APPROVED, Approval::STATUS_REJECTED]) //
            ->with([
                'approvable' => function ($morphTo) {
                    $morphTo->morphWith([
                        EmailApplication::class => ['user:id,name'], //
                        LoanApplication::class => ['user:id,name'],  //
                    ]);
                },
            ])
            ->orderBy('updated_at', 'desc')
            ->paginate(config('pagination.default_size', 15));

        Log::debug("ApprovalController@showHistory: Fetched {$completedApprovals->total()} completed approval tasks.");

        return view('approvals.history', ['approvals' => $completedApprovals]);
    }

    /**
     * Display the specified approval task.
     *
     * @param  \App\Models\Approval  $approval  Route model bound instance.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Approval $approval): View|RedirectResponse
    {
        try {
            $this->authorize('view', $approval); // Uses ApprovalPolicy
        } catch (AuthorizationException $e) {
            Log::warning(
                "ApprovalController@show: Authorization failed for Approval ID {$approval->id}. User ID: ".Auth::id().". Error: {$e->getMessage()}"
            );
            return redirect()->route('approvals.index')->with('error', __('Anda tidak mempunyai kebenaran untuk melihat tugasan kelulusan ini.'));
        }

        Log::debug("ApprovalController@show: Loading approval task ID {$approval->id}.");

        // Assumes loadDefaultRelationships() is implemented in App\Models\Approval as discussed
        $approval->loadDefaultRelationships(); //

        return view('approvals.show', compact('approval'));
    }

    /**
     * Record a decision for the specified approval task.
     *
     * @param  \App\Http\Requests\RecordApprovalDecisionRequest  $request
     * @param  \App\Models\Approval  $approval  Route model bound instance.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function recordDecision(
        RecordApprovalDecisionRequest $request, // Use the Form Request for validation & authorization
        Approval $approval
    ): RedirectResponse {
        /** @var User $processingUser Authenticated user who is making the decision */
        $processingUser = $request->user();
        $validatedData = $request->validated();

        Log::info(
            "ApprovalController@recordDecision: User ID {$processingUser->id} attempting to record decision for Approval Task ID {$approval->id}.",
            ['decision' => $validatedData['decision'], 'has_comments' => !empty($validatedData['comments'])]
        );

        // Authorization to 'update' the $approval is handled by RecordApprovalDecisionRequest::authorize()

        try {
            $this->approvalService->processApprovalDecision(
                $approval,
                $validatedData['decision'],
                $processingUser,
                $validatedData['comments'] ?? null
            ); //

            $decisionText = $validatedData['decision'] === Approval::STATUS_APPROVED ? __('DILULUSKAN') : __('DITOLAK');
            $message = __('Keputusan untuk tugasan #:taskId telah berjaya direkodkan sebagai :decision.', ['taskId' => $approval->id, 'decision' => $decisionText]);

            Log::info(
                "Decision '{$validatedData['decision']}' recorded successfully for Approval ID {$approval->id} by User ID {$processingUser->id}."
            );

            // Redirects to 'approval.dashboard' which is assumed to exist (likely a Livewire page)
            return redirect()->route('approval.dashboard')->with('success', $message);
        } catch (AuthorizationException $e) {
            // This catch block might be redundant if FormRequest handles all auth,
            // but can be kept as a fallback or if service throws its own AuthorizationException.
            Log::error(
                "ApprovalController@recordDecision: Authorization error for Approval ID {$approval->id}. User ID: {$processingUser->id}.",
                ['error' => $e->getMessage()]
            );
            return redirect()->back()->withInput()->with('error', __('Anda tidak mempunyai kebenaran untuk membuat keputusan ini.'));
        } catch (Throwable $e) {
            Log::error(
                "ApprovalController@recordDecision: Error processing approval decision for ID {$approval->id}. User ID: {$processingUser->id}.",
                [
                    'error' => $e->getMessage(),
                    'trace_snippet' => substr($e->getTraceAsString(), 0, 500),
                    'request_data' => $request->except(['_token', '_method', 'password', 'password_confirmation']), // Sanitize logged data
                ]
            );
            return redirect()->back()->withInput()->with('error', __('Gagal merekod keputusan disebabkan oleh ralat sistem: ') . $e->getMessage());
        }
    }
}
