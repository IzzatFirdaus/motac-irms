<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmailApplicationRequest;
use App\Http\Requests\UpdateEmailApplicationRequest;
use App\Models\EmailApplication;
use App\Services\EmailApplicationService;
use Exception; // Keep for explicit catch if needed
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View; // Keep for return type hinting

class EmailApplicationController extends Controller
{
    protected EmailApplicationService $emailApplicationService;

    public function __construct(EmailApplicationService $emailApplicationService)
    {
        $this->emailApplicationService = $emailApplicationService;
        $this->middleware('auth');
        // Automatically authorize resource methods based on EmailApplicationPolicy
        // The route parameter name for EmailApplication should be 'emailApplication' (singular)
        // or 'email_application' (snake_case) to match policy expectations.
        // Default is usually snake_case: 'email_application'.
        // Ensuring policy methods like view(User $user, EmailApplication $emailApplication) exist.
        $this->authorizeResource(EmailApplication::class, 'email_application', [
            'except' => ['index', 'submit'], // 'index' might have viewAny, 'submit' is custom
        ]);
    }

    /**
     * Display a listing of the user's email applications.
     * Primary user list is typically handled by Livewire: App\Livewire\ResourceManagement\MyApplications\Email\Index
     * This controller method can serve as an alternative or for specific non-Livewire views if needed.
     */
    public function index(): View|RedirectResponse
    {
        $this->authorize('viewAny', EmailApplication::class); // Explicit authorize for index

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            Log::error('Authenticated user is null in EmailApplicationController index.');
            return redirect()->route('login')->with('error', __('Sila log masuk semula.'));
        }

        // This method primarily serves as a placeholder if a non-Livewire index is needed.
        // The Livewire component is the main interface for users to see their applications.
        // If this were an admin view, it would fetch applications differently.
        Log::info('EmailApplicationController@index called for User ID: ' . $user->id . '. Redirecting to Livewire view equivalent.');
        // Consider redirecting to the Livewire page if this controller route is accessed directly by users
        return redirect()->route('resource-management.my-applications.email.index');
        // OR if an admin view:
        // $emailApplications = $this->emailApplicationService->getApplicationsForUser($user, request()->all()); // Example
        // return view('resource-management.email-applications.index', compact('emailApplications'));
    }

    /**
     * Show the form for creating a new email application.
     * This is typically handled by a Livewire component like EmailApplicationForm.
     * This route/method might be redundant if the Livewire form is directly accessed.
     */
    public function create(): View
    {
        $this->authorize('create', EmailApplication::class);
        Log::debug('EmailApplicationController@create: Displaying create form (likely handled by Livewire).');
        // Typically, the Livewire component App\Livewire\EmailApplicationForm is routed directly.
        // If this controller method is used, it would return a view that embeds the Livewire component.
        return view('resource-management.email-applications.create'); // Assumes this view embeds <livewire:email-application-form />
    }

    /**
     * Store a newly created email application draft.
     * This might be called by a standard HTML form if not using Livewire for creation.
     */
    public function store(StoreEmailApplicationRequest $request): RedirectResponse
    {
        // Authorization for 'create' is handled by authorizeResource or StoreEmailApplicationRequest::authorize()
        /** @var \App\Models\User $user */
        $user = $request->user();
        Log::debug("EmailApplicationController@store: Attempting to store new email application for User ID: {$user->id}.");

        $validatedData = $request->validated();

        try {
            $application = $this->emailApplicationService->createApplication(
                $validatedData,
                $user
            );
            Log::info('Email application draft created successfully.', [
                'application_id' => $application->id,
                'user_id' => $user->id,
            ]);

            // Assuming your route model binding uses 'email_application'
            return redirect()
                ->route('resource-management.email-applications.show', ['email_application' => $application->id])
                ->with('success', __('Draf permohonan e-mel berjaya dicipta. Anda boleh menyunting atau menghantarnya untuk kelulusan.'));
        } catch (Exception $e) {
            Log::error('Error storing email application.', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('Gagal mencipta permohonan e-mel: ') . $e->getMessage());
        }
    }

    /**
     * Display the specified email application.
     * Route model binding: EmailApplication $emailApplication (parameter name should match policy/authorizeResource)
     */
    public function show(EmailApplication $email_application): View // Parameter name matches typical binding
    {
        // Authorization handled by authorizeResource for 'view'
        Log::debug("EmailApplicationController@show: Loading email application ID {$email_application->id}");

        $email_application->loadMissing([
            'user.department',
            'supportingOfficer', // Officer named in the application
            'approvals.officer', // Approvers for each approval step
            'creator',
            'updater',
        ]);

        return view('resource-management.email-applications.show', ['emailApplication' => $email_application]);
    }

    /**
     * Show the form for editing the specified email application.
     * Route model binding: EmailApplication $emailApplication
     */
    public function edit(EmailApplication $email_application): View|RedirectResponse
    {
        // Authorization handled by authorizeResource for 'update' (as edit leads to update)
        Log::debug("EmailApplicationController@edit: Displaying edit form for email application ID {$email_application->id}");

        if ($email_application->isDraft()) { // Assumes isDraft() method exists on model
            return view('resource-management.email-applications.edit', ['emailApplication' => $email_application]);
        }

        Log::warning('Attempt to edit non-draft email application.', [
            'application_id' => $email_application->id,
            'user_id' => Auth::id(),
            'status' => $email_application->status,
        ]);

        return redirect()
            ->route('resource-management.email-applications.show', ['email_application' => $email_application->id])
            ->with('error', __('Hanya permohonan draf yang boleh disunting.'));
    }

    /**
     * Update the specified email application in storage.
     * Route model binding: EmailApplication $emailApplication
     */
    public function update(
        UpdateEmailApplicationRequest $request,
        EmailApplication $email_application
    ): RedirectResponse {
        // Authorization handled by authorizeResource for 'update' or UpdateEmailApplicationRequest
        /** @var \App\Models\User $user */
        $user = $request->user();
        Log::debug("EmailApplicationController@update: Attempting to update email application ID {$email_application->id} by User ID {$user->id}");

        if (!$email_application->isDraft() && !$user->can('manage', $email_application) /* Example admin override */) {
            Log::warning('Attempt to update non-draft email application by non-authorized user.', [
                'application_id' => $email_application->id,
                'user_id' => $user->id,
                'status' => $email_application->status,
            ]);
            return redirect()
                ->route('resource-management.email-applications.show', ['email_application' => $email_application->id])
                ->with('error', __('Hanya permohonan draf yang boleh dikemaskini atau anda tiada kebenaran.'));
        }

        $validatedData = $request->validated();

        try {
            $updatedApplication = $this->emailApplicationService->updateApplication(
                $email_application,
                $validatedData,
                $user
            );
            Log::info('Email application updated successfully.', [
                'application_id' => $updatedApplication->id,
                'user_id' => $user->id,
            ]);

            return redirect()
                ->route('resource-management.email-applications.show', ['email_application' => $updatedApplication->id])
                ->with('success', __('Permohonan e-mel berjaya dikemaskini.'));
        } catch (Exception $e) {
            Log::error('Error updating email application.', [
                'application_id' => $email_application->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->withInput()->with('error', __('Gagal mengemaskini permohonan e-mel: ') . $e->getMessage());
        }
    }

    /**
     * Submit a draft application for approval.
     * Route: email-applications/{emailApplication}/submit
     * Parameter name should match {emailApplication} for binding.
     */
    public function submit(EmailApplication $emailApplication): RedirectResponse // Changed param name for consistency
    {
        $this->authorize('submit', $emailApplication); // Specific authorization for submit action
        /** @var \App\Models\User $user */
        $user = Auth::user();
        Log::debug("EmailApplicationController@submit: Attempting to submit email application ID {$emailApplication->id} by User ID {$user->id}");

        try {
            $submittedApplication = $this->emailApplicationService->submitApplication(
                $emailApplication,
                $user
            );
            Log::info('Email application submitted successfully.', [
                'application_id' => $submittedApplication->id,
                'user_id' => $user->id,
            ]);

            return redirect()
                ->route('resource-management.email-applications.show', ['email_application' => $submittedApplication->id])
                ->with('success', __('Permohonan e-mel berjaya dihantar untuk kelulusan.'));
        } catch (Exception $e) {
            Log::error('Error submitting email application.', [
                'application_id' => $emailApplication->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', __('Gagal menghantar permohonan e-mel: ') . $e->getMessage());
        }
    }

    /**
     * Remove the specified email application (soft delete).
     * Route model binding: EmailApplication $emailApplication
     */
    public function destroy(EmailApplication $email_application): RedirectResponse
    {
        // Authorization handled by authorizeResource for 'delete'
        /** @var \App\Models\User $user */
        $user = Auth::user();
        Log::debug("EmailApplicationController@destroy: Attempting to delete email application ID {$email_application->id} by User ID {$user->id}");

        if (!$email_application->isDraft() && !$user->can('forceDelete', $email_application) /* Example admin override */) {
             Log::warning('Attempt to delete non-draft/non-deletable email application.', [
                'application_id' => $email_application->id,
                'user_id' => $user->id,
                'status' => $email_application->status,
            ]);
            return redirect()
                ->route('resource-management.email-applications.show', ['email_application' => $email_application->id])
                ->with('error', __('Hanya permohonan draf yang boleh dibuang atau anda tiada kebenaran.'));
        }

        try {
            $this->emailApplicationService->deleteApplication($email_application, $user);
            Log::info('Email application soft deleted successfully.', [
                'application_id' => $email_application->id,
                'user_id' => $user->id,
            ]);

            return redirect()
                ->route('resource-management.my-applications.email.index') // Redirect to user's list
                ->with('success', __('Permohonan e-mel berjaya dibuang.'));
        } catch (Exception $e) {
            Log::error('Error deleting email application.', [
                'application_id' => $email_application->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', __('Gagal membuang permohonan e-mel: ') . $e->getMessage());
        }
    }
}
