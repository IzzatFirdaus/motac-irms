<?php

// File: app/Http/Controllers/ApprovalController.php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\EmailApplication;
use App\Models\LoanApplication;
use App\Services\ApprovalService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable; // Recommended over global Exception for broader catch

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
     * This is the NON-Livewire version.
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            Log::warning('ApprovalController@index: User not authenticated.');

            return redirect()
                ->route('login')
                ->with('error', __('Authentication required.'));
        }

        Log::debug(
            "ApprovalController@index: Fetching 'pending' approval tasks for officer ID {$user->id}."
        );

        $pendingApprovals = Approval::where('officer_id', $user->id)
            ->where('status', Approval::STATUS_PENDING) // Use model constant
            ->with([
                'approvable' => function ($morphTo): void {
                    $morphTo->morphWith([
                        EmailApplication::class => ['user:id,full_name'], // Select specific columns
                        LoanApplication::class => ['user:id,full_name'],
                    ]);
                },
                // 'officer:id,full_name', // Current user, already have $user
            ])
            ->orderBy('created_at', 'asc')
            ->paginate(10); // Or your preferred pagination size

        Log::debug(
            "ApprovalController@index: Fetched {$pendingApprovals->total()} pending approval tasks."
        );

        return view('approvals.index', ['approvals' => $pendingApprovals]);
    }

    /**
     * Display a listing of completed approval tasks for the current user.
     * This method corresponds to the route for /approvals/history.
     */
    public function showHistory(): View|RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            Log::warning('ApprovalController@showHistory: User not authenticated.');

            return redirect()
                ->route('login')
                ->with('error', __('Authentication required.'));
        }

        Log::debug(
            "ApprovalController@showHistory: Fetching 'completed' (approved/rejected) approval tasks for officer ID {$user->id}."
        );

        $completedApprovals = Approval::where('officer_id', $user->id)
            ->whereIn('status', [Approval::STATUS_APPROVED, Approval::STATUS_REJECTED]) // Use model constants
            ->with([
                'approvable' => function ($morphTo): void {
                    $morphTo->morphWith([
                        EmailApplication::class => ['user:id,full_name'],
                        LoanApplication::class => ['user:id,full_name'],
                    ]);
                },
            ])
            ->orderBy('updated_at', 'desc') // Order by when the decision was made
            ->paginate(10); // Or your preferred pagination size

        Log::debug(
            "ApprovalController@showHistory: Fetched {$completedApprovals->total()} completed approval tasks."
        );

        // You will need to create this Blade view: resources/views/approvals/history.blade.php
        return view('approvals.history', ['approvals' => $completedApprovals]);
    }

    /**
     * Display the specified approval task.
     *
     * @param  Approval  $approval  Route model bound instance.
     */
    public function show(Approval $approval): View|RedirectResponse
    {
        try {
            $this->authorize('view', $approval);
        } catch (AuthorizationException $e) {
            Log::warning(
                "ApprovalController@show: Authorization failed for Approval ID {$approval->id}. User ID: ".
                  Auth::id().
                  ". Error: {$e->getMessage()}"
            );

            return redirect()
                ->route('approvals.index')
                ->with(
                    'error',
                    __('You are not authorized to view this approval task.')
                );
        }

        Log::debug(
            "ApprovalController@show: Loading approval task ID {$approval->id} with eager loaded relationships."
        );

        $approval->loadDefaultRelationships(); // Assuming you add a method in Approval model for common loads

        return view('approvals.show', compact('approval'));
    }

    /**
     * Record a decision for the specified approval task.
     * This method is for form submissions if not using Livewire for this action.
     *
     * @param  Approval  $approval  Route model bound instance.
     */
    public function recordDecision(
        Request $request,
        Approval $approval
    ): RedirectResponse {
        $user = Auth::user();
        if (! $user) {
            Log::error(
                'ApprovalController@recordDecision: Authenticated user is null during decision recording.'
            );

            return redirect()
                ->route('login')
                ->with('error', __('Authentication required.'));
        }

        Log::debug(
            "ApprovalController@recordDecision: Attempting to record decision for Approval Task ID {$approval->id} by User ID {$user->id}."
        );

        try {
            $this->authorize('update', $approval); // Policy check for updating (acting on) the approval
        } catch (AuthorizationException $e) {
            Log::warning(
                "ApprovalController@recordDecision: Authorization failed for Approval ID {$approval->id}. User ID: {$user->id}. Error: {$e->getMessage()}"
            );

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    __('You are not authorized to act on this approval task.')
                );
        }

        $validated = $request->validate([
            'decision' => [
                'required',
                'in:'.Approval::STATUS_APPROVED.','.Approval::STATUS_REJECTED,
            ],
            'comments' => $request->input('decision') === Approval::STATUS_REJECTED
              ? ['required', 'string', 'min:10', 'max:2000']
              : ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $this->approvalService->processApprovalDecision(
                $approval,
                $validated['decision'],
                $user, // Pass the authenticated user who is processing
                $validated['comments']
            );

            $decisionText =
              $validated['decision'] === Approval::STATUS_APPROVED
              ? __('APPROVED')
              : __('REJECTED');
            $message =
              __('Decision for task #').
              $approval->id.
              __(' has been successfully recorded as ').
              $decisionText.
              '.';
            Log::info(
                "ApprovalController@recordDecision: Decision '{$validated['decision']}' recorded successfully for Approval ID {$approval->id} by User ID {$user->id}."
            );

            // Redirect to Livewire dashboard or a relevant page
            return redirect()
                ->route('approval.dashboard') // Ensure 'approval.dashboard' route name exists
                ->with('success', $message);
        } catch (Throwable $e) {
            // Catching Throwable is broader
            Log::error(
                "ApprovalController@recordDecision: Error processing approval decision for ID {$approval->id}.",
                [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(), // Include stack trace for detailed debugging
                    'user_id' => $user->id,
                    'request_data' => $request->all(),
                ]
            );

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    __('Failed to record decision due to an error: ').$e->getMessage()
                );
        }
    }

    // Example: Method in Approval model for default loads
    // public function loadDefaultRelationships() {
    //     $this->load([
    //        'approvable' => function ($morphTo) {
    //            $morphTo->morphWith([
    //                EmailApplication::class => ['user:id,full_name', 'user.department:id,name', /* other relations */],
    //                LoanApplication::class => ['user:id,full_name', 'user.department:id,name', 'items', /* other relations */],
    //            ]);
    //        },
    //        'officer:id,full_name',
    //        'creator:id,full_name',
    //        'updater:id,full_name',
    //    ]);
    // }
}
