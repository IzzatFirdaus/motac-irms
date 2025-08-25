<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecordApprovalDecisionRequest;
use App\Models\Approval;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\ApprovalService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

/**
 * Controller to manage Approval tasks for officers.
 * Handles listing, displaying, and recording decisions for approval tasks.
 */
class ApprovalController extends Controller
{
    protected ApprovalService $approvalService;

    /**
     * Inject the ApprovalService and require authentication.
     */
    public function __construct(ApprovalService $approvalService)
    {
        $this->middleware('auth');
        $this->approvalService = $approvalService;
    }

    /**
     * Display a paginated list of pending approvals for the authenticated officer.
     */
    public function index(): View|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        Log::debug(sprintf("ApprovalController@index: Fetching 'pending' approval tasks for officer ID %d.", $user->id));

        // Fetch pending approvals for the current officer, eager loading approvable (LoanApplication)
        $pendingApprovals = Approval::where('officer_id', $user->id)
            ->where('status', Approval::STATUS_PENDING)
            ->with([
                'approvable' => function ($morphTo) {
                    $morphTo->morphWith([
                        LoanApplication::class => ['user:id,name'],
                    ]);
                },
            ])
            ->paginate(10);

        return view('approvals.index', compact('pendingApprovals'));
    }

    /**
     * Displays a specific approval task for review.
     * Loads necessary relationships for the approval.
     */
    public function show(Approval $approval): View
    {
        $this->authorize('view', $approval); // Ensure user can view this approval
        $approval->loadDefaultRelationships(); // Eager load default relationships (defined in Approval model)
        Log::debug(sprintf('ApprovalController@show: Displaying Approval ID %d.', $approval->id), ['user_id' => Auth::id()]);

        return view('approvals.show', compact('approval'));
    }

    /**
     * Records the decision for an approval task.
     * Handles approval or rejection, and redirects based on approvable type.
     *
     * @param RecordApprovalDecisionRequest $request
     * @param Approval $approval
     * @return RedirectResponse
     * @throws Throwable
     */
    public function recordDecision(RecordApprovalDecisionRequest $request, Approval $approval): RedirectResponse
    {
        /** @var User $processingUser */
        $processingUser = Auth::user();

        try {
            // Check if the user has permission to perform this approval action
            $this->authorize('performApproval', $approval);

            $validatedData = $request->validated();

            // Call the service method to record the approval decision
            $this->approvalService->recordApprovalDecision(
                $approval,
                $validatedData['decision'],
                $validatedData['notes'],
                $request->get('approval_items', []) // Pass approval_items array for loan applications if present
            );

            $message = __('Keputusan kelulusan berjaya direkodkan.');
            Log::info(
                sprintf('ApprovalController@recordDecision: Approval ID %d decision recorded as %s by User ID %d.', $approval->id, $validatedData['decision'], $processingUser->id)
            );

            // Redirect to the show page for the approvable if it's a LoanApplication
            if ($approval->approvable instanceof LoanApplication) {
                return redirect()
                    ->route('loan-applications.show', $approval->approvable_id)
                    ->with('success', $message);
            }

            // Default/fallback redirect to approvals dashboard
            return redirect()->route('approvals.dashboard')->with('success', $message);

        } catch (AuthorizationException $e) {
            Log::error(sprintf('ApprovalController@recordDecision: Authorization error for Approval ID %d. User ID: %d.', $approval->id, $processingUser->id), ['error' => $e->getMessage()]);

            return redirect()->back()->withInput()->with('error', __('Anda tidak mempunyai kebenaran untuk membuat keputusan ini.'));
        } catch (Throwable $e) {
            // During testing, throw exception to reveal error. In production, log and show friendly message.
            throw $e;
            /*
            Log::error("ApprovalController@recordDecision: Error processing approval for ID {$approval->id}. User ID: {$processingUser->id}.", [
                'error' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 500),
                'request_data' => $request->except(['_token', '_method']),
            ]);

            return redirect()->back()->withInput()->with('error', __('Gagal merekod keputusan disebabkan oleh ralat sistem: ') . $e->getMessage());
            */
        }
    }
}
