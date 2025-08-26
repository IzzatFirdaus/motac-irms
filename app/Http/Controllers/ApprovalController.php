<?php

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
        Log::debug("ApprovalController@index: Fetching 'pending' approval tasks for officer ID {$user->id}."); //
        $pendingApprovals = Approval::where('officer_id', $user->id)
            ->where('status', Approval::STATUS_PENDING)
            ->with([ //
                'approvable' => function ($morphTo) { //
                    $morphTo->morphWith([ //
                        EmailApplication::class => ['user:id,name'], //
                        LoanApplication::class  => ['user:id,name'], //
                    ]);
                },
                'officer:id,name', //
            ])
            ->orderBy('created_at', 'asc')
            ->paginate(config('pagination.default_size', 15));
        Log::debug("ApprovalController@index: Fetched {$pendingApprovals->total()} pending approval tasks."); //

        return view('approvals.index', ['approvals' => $pendingApprovals]);
    }

    public function showHistory(): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        Log::debug("ApprovalController@showHistory: Fetching 'completed' approval tasks for officer ID {$user->id}."); //
        $completedApprovals = Approval::where('officer_id', $user->id)
            ->whereIn('status', [Approval::STATUS_APPROVED, Approval::STATUS_REJECTED])
            ->with([ //
                'approvable' => function ($morphTo) { //
                    $morphTo->morphWith([ //
                        EmailApplication::class => ['user:id,name'], //
                        LoanApplication::class  => ['user:id,name'], //
                    ]);
                },
                'officer:id,name', //
            ])
            ->orderBy('updated_at', 'desc')
            ->paginate(config('pagination.default_size', 15));
        Log::debug("ApprovalController@showHistory: Fetched {$completedApprovals->total()} completed approval tasks."); //

        return view('approvals.history', ['approvals' => $completedApprovals]);
    }

    public function show(Approval $approval): View|RedirectResponse
    {
        try {
            $this->authorize('view', $approval);
        } catch (AuthorizationException $e) {
            Log::warning("ApprovalController@show: Authorization failed for Approval ID {$approval->id}. User ID: ".Auth::id().". Error: {$e->getMessage()}"); //

            return redirect()->route('approvals.index')->with('error', __('Anda tidak mempunyai kebenaran untuk melihat tugasan kelulusan ini.'));
        }
        Log::debug("ApprovalController@show: Loading approval task ID {$approval->id}."); //

        // Consolidate and ensure all necessary relations for the 'approvals.show' view are loaded.
        $approval->loadMissing([
            'officer:id,name,grade_id', // Officer assigned this task
            'officer.grade:id,name',    // Grade of the officer
            'approvable' => function ($morphTo) {
                $morphTo->morphWith([
                    EmailApplication::class => [
                        'user:id,name,department_id,grade_id,position_id', // Applicant
                        'user.department:id,name',
                        'user.grade:id,name',
                        'user.position:id,name',
                        'supportingOfficer:id,name,grade_id,position_id', // Nominated supporting officer on email app
                        'supportingOfficer.grade:id,name',
                        'supportingOfficer.position:id,name',
                    ],
                    LoanApplication::class => [
                        'user:id,name,department_id,grade_id,position_id', // Applicant
                        'user.department:id,name',
                        'user.grade:id,name',
                        'user.position:id,name',
                        'responsibleOfficer:id,name,grade_id,position_id',
                        'responsibleOfficer.grade:id,name',
                        'responsibleOfficer.position:id,name',
                        'supportingOfficer:id,name,grade_id,position_id', // Nominated supporting officer on loan app
                        'supportingOfficer.grade:id,name',
                        'supportingOfficer.position:id,name',
                        'loanApplicationItems:id,loan_application_id,equipment_type,quantity_requested,quantity_approved,notes', // Ensure all needed fields are here
                    ],
                ]);
            },
        ]);

        return view('approvals.show', compact('approval'));
    }

    public function recordDecision(
        RecordApprovalDecisionRequest $request,
        Approval $approval
    ): RedirectResponse {
        /** @var User $processingUser */
        $processingUser = $request->user();
        $validatedData  = $request->validated();

        Log::info("ApprovalController@recordDecision: User ID {$processingUser->id} recording decision for Approval Task ID {$approval->id}.", //
            ['decision' => $validatedData['decision'], 'has_comments' => ! empty($validatedData['comments'])]
        );

        $itemQuantitiesForService = null;

        if ($approval->approvable_type === LoanApplication::class && // Ensures quantity adjustment is typically done at the supporting officer stage,
            // or adjust this condition if other stages also adjust quantities.
            $approval->stage === Approval::STAGE_LOAN_SUPPORT_REVIEW && $validatedData['decision'] === Approval::STATUS_APPROVED && isset($validatedData['items_approved']) && is_array($validatedData['items_approved'])) {

            $itemQuantitiesForService = [];
            foreach ($validatedData['items_approved'] as $loanApplicationItemId => $itemData) {
                if (isset($itemData['quantity_approved'])) { // Ensure the specific key exists
                    $itemQuantitiesForService[] = [
                        'loan_application_item_id' => (int) $loanApplicationItemId,
                        'quantity_approved'        => (int) $itemData['quantity_approved'],
                    ];
                }
            }
            Log::info("ApprovalController@recordDecision: Transformed item quantities for LoanApplication ID {$approval->approvable_id}.", ['transformed_quantities_count' => count($itemQuantitiesForService)]); //
        }

        try {
            $this->approvalService->processApprovalDecision( //
                $approval, //
                $validatedData['decision'], //
                $processingUser, //
                $validatedData['comments'] ?? null, //
                $itemQuantitiesForService // Pass the new, possibly transformed, parameter
            );
            $decisionText = $validatedData['decision'] === Approval::STATUS_APPROVED ? __('DILULUSKAN') : __('DITOLAK'); //
            $message      = __('Keputusan untuk tugasan #:taskId telah berjaya direkodkan sebagai :decision.', ['taskId' => $approval->id, 'decision' => $decisionText]); //
            Log::info("Decision '{$validatedData['decision']}' recorded for Approval ID {$approval->id} by User ID {$processingUser->id}."); //

            // Redirect to the specific application's show page after decision
            if ($approval->approvable instanceof LoanApplication) { //
                return redirect()->route('loan-applications.show', $approval->approvable_id)->with('success', $message); //
            } elseif ($approval->approvable instanceof EmailApplication) { //
                return redirect()->route('email-applications.show', $approval->approvable_id)->with('success', $message); //
            }

            return redirect()->route('approvals.dashboard')->with('success', $message); // Fallback redirect

        } catch (AuthorizationException $e) { //
            Log::error("ApprovalController@recordDecision: Authorization error for Approval ID {$approval->id}. User ID: {$processingUser->id}.", ['error' => $e->getMessage()]); //

            return redirect()->back()->withInput()->with('error', __('Anda tidak mempunyai kebenaran untuk membuat keputusan ini.')); //
        } catch (Throwable $e) { //
            // EDIT: Temporarily throw the exception during testing to see the real error message.
            // Remember to change this back to the redirect after debugging.
            throw $e;

            /*
            Log::error("ApprovalController@recordDecision: Error processing approval for ID {$approval->id}. User ID: {$processingUser->id}.", [ //
                'error' => $e->getMessage(), //
                'trace' => substr($e->getTraceAsString(), 0, 500), //
                'request_data' => $request->except(['_token', '_method']), //
            ]);

            return redirect()->back()->withInput()->with('error', __('Gagal merekod keputusan disebabkan oleh ralat sistem: ').$e->getMessage()); //
            */
        }
    }
}
