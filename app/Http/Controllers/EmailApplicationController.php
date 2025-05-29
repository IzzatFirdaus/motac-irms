<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmailApplicationRequest;
use App\Http\Requests\UpdateEmailApplicationRequest;
use App\Models\EmailApplication;
use App\Models\User;
use App\Services\EmailApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class EmailApplicationController extends Controller
{
    protected EmailApplicationService $emailApplicationService;

    public function __construct(EmailApplicationService $emailApplicationService)
    {
        $this->emailApplicationService = $emailApplicationService;
        $this->middleware('auth');

        // Authorize resource methods. 'index' and 'create', 'edit' are primarily handled by Livewire components
        // that might call these controller methods for backend logic or have their own routes.
        // The routes in web.php for 'store', 'show', 'update', 'destroy', 'submitApplication' point here.
        $this->authorizeResource(EmailApplication::class, 'email_application', [ // Use snake_case for parameter name
            'except' => ['index', 'create', 'edit'], // Assuming Livewire handles these views/forms
        ]);
    }

    /**
     * Store a newly created email application draft.
     *
     * @param  \App\Http\Requests\StoreEmailApplicationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreEmailApplicationRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $validatedData = $request->validated();

        Log::info("EmailApplicationController@store: User ID {$user->id} storing new email application draft.");

        try {
            $application = $this->emailApplicationService->createApplication(
                $validatedData,
                $user
            );
            Log::info("Email application draft ID: {$application->id} created successfully by User ID: {$user->id}.");

            // Corrected route name
            return redirect()
                ->route('email-applications.show', $application)
                ->with('success', __('Draf permohonan e-mel berjaya dicipta. Anda boleh menyunting atau menghantarnya untuk kelulusan.'));
        } catch (Throwable $e) {
            Log::error("Error storing email application draft for User ID: {$user->id}.", [
                'error' => $e->getMessage(),
                'exception_trace_snippet' => substr($e->getTraceAsString(), 0, 500),
                'request_data' => $request->except(['_token', 'password', 'password_confirmation']),
            ]);
            return redirect()->back()->withInput()->with('error', __('Gagal mencipta draf permohonan e-mel: ') . $e->getMessage());
        }
    }

    /**
     * Display the specified email application.
     *
     * @param  \App\Models\EmailApplication  $emailApplication
     * @return \Illuminate\View\View
     */
    public function show(EmailApplication $emailApplication): View
    {
        // Authorization handled by authorizeResource for 'email_application' parameter
        // $this->authorize('view', $emailApplication);

        Log::info("EmailApplicationController@show: User ID ".Auth::id()." viewing EmailApplication ID {$emailApplication->id}.");

        $emailApplication->loadMissing([
            'user.department', 'user.grade', 'user.position',
            'supportingOfficerUser.grade', // Ensure this relation exists and is named correctly
            'approvals.officer:id,name',
            'creator:id,name', // If using blameable behavior
            'updater:id,name', // If using blameable behavior
        ]);

        return view('email-applications.show', compact('emailApplication'));
    }

    /**
     * Update the specified email application in storage.
     *
     * @param  \App\Http\Requests\UpdateEmailApplicationRequest  $request
     * @param  \App\Models\EmailApplication  $emailApplication
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateEmailApplicationRequest $request, EmailApplication $emailApplication): RedirectResponse
    {
        // Authorization handled by authorizeResource for 'email_application' parameter
        // $this->authorize('update', $emailApplication);

        /** @var User $user */
        $user = $request->user();
        $validatedData = $request->validated();

        if (!$emailApplication->isDraft()) {
            Log::warning("User ID {$user->id} attempt to update non-draft EmailApplication ID {$emailApplication->id}.", [
                'application_status' => $emailApplication->status,
            ]);
            // Corrected route name
            return redirect()
                ->route('email-applications.show', $emailApplication)
                ->with('error', __('Hanya draf permohonan yang boleh dikemaskini.'));
        }

        Log::info("EmailApplicationController@update: User ID {$user->id} attempting to update EmailApplication ID {$emailApplication->id}.");

        try {
            $updatedApplication = $this->emailApplicationService->updateApplication(
                $emailApplication,
                $validatedData,
                $user
            );
            Log::info("EmailApplication ID {$updatedApplication->id} updated successfully by User ID {$user->id}.");

            // Corrected route name
            return redirect()
                ->route('email-applications.show', $updatedApplication)
                ->with('success', __('Permohonan e-mel berjaya dikemaskini.'));
        } catch (Throwable $e) {
            Log::error("Error updating EmailApplication ID {$emailApplication->id} by User ID {$user->id}.", [
                'error' => $e->getMessage(),
                'exception_trace_snippet' => substr($e->getTraceAsString(), 0, 500),
                'request_data' => $request->except(['_token', 'password', 'password_confirmation']),
            ]);
            return redirect()->back()->withInput()->with('error', __('Gagal mengemaskini permohonan e-mel: ') . $e->getMessage());
        }
    }

    /**
     * Submit a draft application for approval.
     *
     * @param  \App\Models\EmailApplication  $emailApplication
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitApplication(EmailApplication $emailApplication): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // Manually authorize the 'submit' ability from EmailApplicationPolicy
        $this->authorize('submit', $emailApplication);

        Log::info("EmailApplicationController@submitApplication: User ID {$user->id} attempting to submit EmailApplication ID {$emailApplication->id}.");

        try {
            $submittedApplication = $this->emailApplicationService->submitApplication(
                $emailApplication,
                $user
            );
            Log::info("EmailApplication ID {$submittedApplication->id} submitted successfully by User ID {$user->id}. Status: {$submittedApplication->status}");

            // Corrected route name
            return redirect()
                ->route('email-applications.show', $submittedApplication)
                ->with('success', __('Permohonan e-mel berjaya dihantar untuk kelulusan.'));
        } catch (Throwable $e) {
            Log::error("Error submitting EmailApplication ID {$emailApplication->id} by User ID {$user->id}.", [
                'error' => $e->getMessage(),
                'exception_trace_snippet' => substr($e->getTraceAsString(), 0, 500),
            ]);
            $errorMessage = ($e instanceof \RuntimeException || $e instanceof \InvalidArgumentException)
                ? $e->getMessage()
                : __('Gagal menghantar permohonan e-mel disebabkan ralat sistem.');
            // Corrected route name for redirect back
            return redirect()->route('email-applications.show', $emailApplication)->with('error', $errorMessage);
        }
    }

    /**
     * Remove the specified email application (soft delete).
     *
     * @param  \App\Models\EmailApplication  $emailApplication
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(EmailApplication $emailApplication): RedirectResponse
    {
        // Authorization handled by authorizeResource for 'email_application' parameter
        // $this->authorize('delete', $emailApplication);

        /** @var User $user */
        $user = Auth::user();

        if (!$emailApplication->isDraft()) {
            Log::warning("User ID {$user->id} attempt to delete non-draft EmailApplication ID {$emailApplication->id}.", [
                'application_status' => $emailApplication->status,
            ]);
            // Corrected route name
            return redirect()
                ->route('email-applications.show', $emailApplication)
                ->with('error', __('Hanya draf permohonan yang boleh dibuang.'));
        }

        Log::info("EmailApplicationController@destroy: User ID {$user->id} attempting to soft delete EmailApplication ID {$emailApplication->id}.");

        try {
            $deleted = $this->emailApplicationService->deleteApplication(
                $emailApplication,
                $user
            );

            if ($deleted) {
                Log::info("EmailApplication ID {$emailApplication->id} soft deleted successfully by User ID {$user->id}.");
                // Corrected route name - this should point to the Livewire index page for email applications
                return redirect()->route('email-applications.index')
                    ->with('success', __('Permohonan e-mel berjaya dibuang.'));
            }
            Log::warning("Soft delete operation returned false for EmailApplication ID {$emailApplication->id} by User ID {$user->id}.");
            return redirect()->back()->with('error', __('Gagal membuang permohonan e-mel. Operasi padam tidak berjaya.'));
        } catch (Throwable $e) {
            Log::error("Error soft deleting EmailApplication ID {$emailApplication->id} by User ID {$user->id}.", [
                'error' => $e->getMessage(),
                'exception_trace_snippet' => substr($e->getTraceAsString(), 0, 500),
            ]);
            return redirect()->back()->with('error', __('Gagal membuang permohonan e-mel disebabkan ralat sistem: ') . $e->getMessage());
        }
    }
}
