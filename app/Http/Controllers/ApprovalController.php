<?php

// File: app/Http/Controllers/ApprovalController.php

namespace App\Http\Controllers;

use App\Http\Requests\RecordApprovalDecisionRequest; // v2
use App\Models\Approval;
// use App\Models\EmailApplication; // REMOVED: EmailApplication is being removed
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
        Log::debug(sprintf("ApprovalController@index: Fetching 'pending' approval tasks for officer ID %d.", $user->id)); //
        $pendingApprovals = Approval::where('officer_id', $user->id)
            ->where('status', Approval::STATUS_PENDING)
            ->with([ //
                'approvable' => function ($morphTo): void { //
                    $morphTo->morphWith([ //
                        // REMOVED: EmailApplication::class => ['user:id,name'], // EmailApplication is being removed
                        LoanApplication::class => ['user:id,name'],
                    ]);
                },
            ])->paginate(10);

        return view('approvals.index', compact('pendingApprovals'));
    }

    /**
     * Displays a specific approval task for review.
     */
    public function show(Approval $approval): View
    {
        $this->authorize('view', $approval); // Ensure user can view this approval
        $approval->loadDefaultRelationships(); // Eager load necessary relationships (defined in Approval model)
        Log::debug(sprintf('ApprovalController@show: Displaying Approval ID %d.', $approval->id), ['user_id' => Auth::id()]);

        return view('approvals.show', compact('approval'));
    }

    /**
     * Records the decision for an approval task.
     * System Design Ref: 9.4.2 (Record Approval Decision)
     */
    public function recordDecision(RecordApprovalDecisionRequest $request, Approval $approval): RedirectResponse
    {
        /** @var \App\Models\User $processingUser */
        $processingUser = Auth::user();

        try {
            $this->authorize('performApproval', $approval); // Policy to check if the user can approve/reject this task

            $validatedData = $request->validated();

            // Call the service method to record the decision
            // Corrected method name from recordDecision to recordApprovalDecision
            $this->approvalService->recordApprovalDecision(
                $approval,
                $validatedData['decision'],
                $validatedData['notes'],
                $request->get('approval_items', []) // Pass approval_items for loan applications
            );

            $message = __('Keputusan kelulusan berjaya direkodkan.');
            Log::info(sprintf('ApprovalController@recordDecision: Approval ID %d decision recorded as %s by User ID %d.', $approval->id, $validatedData['decision'], $processingUser->id));

            // Redirect based on the type of approvable
            if ($approval->approvable instanceof LoanApplication) {
                return redirect()->route('loan-applications.show', $approval->approvable_id)->with('success', $message);
            }
            // REMOVED: if ($approval->approvable instanceof EmailApplication) {
            // REMOVED:    return redirect()->route('email-applications.show', $approval->approvable_id)->with('success', $message);
            // REMOVED: }

            return redirect()->route('approvals.dashboard')->with('success', $message); // Fallback redirect

        } catch (AuthorizationException $e) {
            Log::error(sprintf('ApprovalController@recordDecision: Authorization error for Approval ID %d. User ID: %d.', $approval->id, $processingUser->id), ['error' => $e->getMessage()]);

            return redirect()->back()->withInput()->with('error', __('Anda tidak mempunyai kebenaran untuk membuat keputusan ini.'));
        } catch (Throwable $e) {
            // EDIT: Temporarily throw the exception during testing to see the real error message.
            // Remember to change this back to the redirect after debugging.
            throw $e;
            /*
            Log::error("ApprovalController@recordDecision: Error processing approval for ID {$approval->id}. User ID: {$processingUser->id}.", [
                'error' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 500),
                'request_data' => $request->except(['_token', '_method']),
            ]);

            return redirect()->back()->withInput()->with('error', __('Gagal merekod keputusan disebabkan oleh ralat sistem: ').$e->getMessage());
            */
        }
    }
}
