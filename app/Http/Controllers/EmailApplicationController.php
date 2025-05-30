<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmailApplicationRequest;
use App\Http\Requests\UpdateEmailApplicationRequest;
use App\Models\EmailApplication;
use App\Models\User; // Ensure User model is imported
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

        // Authorize resource methods.
        // System Design (Rev 3, Source 493)
        $this->authorizeResource(EmailApplication::class, 'email_application', [
            'except' => ['index', 'create', 'edit'], // Assuming Livewire handles these views/forms
        ]);
    }

    /**
     * Store a newly created email application draft.
     * System Design (Rev 3, Source 302, 493)
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

            return redirect()
                ->route('email-applications.show', $application) // Route name confirmed from web.php
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
     * System Design (Rev 3, Source 302, 493)
     * @param  \App\Models\EmailApplication  $emailApplication
     * @return \Illuminate\View\View
     */
    public function show(EmailApplication $emailApplication): View
    {
        Log::info("EmailApplicationController@show: User ID ".Auth::id()." viewing EmailApplication ID {$emailApplication->id}.");

        // Define the user fields needed for the 'user-info-card' and other parts of the view.
        // This ensures 'title' and other attributes are explicitly loaded.
        // Refer to User model (System Design Rev 3, Source 69-70) for available fields.
        $userFieldsToSelect = [
            'id', 'name', 'title', 'full_name', 'identification_number',
            'motac_email', 'email', 'mobile_number', 'profile_photo_path',
            'position_id', 'grade_id', 'department_id', 'user_id_assigned',
            'service_status', // Added as it might be relevant for applicant info display
            // Add any other user fields you directly access or pass to components from $emailApplication->user
        ];

        // Fields for officer models (like supportingOfficer, approval->officer, creator, updater)
        // Often a more limited set is needed, e.g., id and name. Adjust as necessary.
        $officerFieldsToSelect = ['id', 'name', 'title', 'full_name', 'grade_id', 'email'];


        $emailApplication->loadMissing([
            // Explicitly select fields for the main user (applicant)
            'user:' . implode(',', $userFieldsToSelect),
            'user.department:id,name', // Select specific fields for related department
            'user.grade:id,name',      // Select specific fields for related grade
            'user.position:id,name',   // Select specific fields for related position

            // Load supporting officer and their grade.
            // Use $officerFieldsToSelect or $userFieldsToSelect based on display needs.
            'supportingOfficer:' . implode(',', $officerFieldsToSelect),
            'supportingOfficer.grade:id,name',

            'approvals.officer:' . implode(',', $officerFieldsToSelect), // For officers in the approval history
            'creator:' . implode(',', $officerFieldsToSelect),      // Blameable: creator
            'updater:' . implode(',', $officerFieldsToSelect),      // Blameable: updater
        ]);

        return view('email-applications.show', compact('emailApplication'));
    }

    /**
     * Update the specified email application in storage.
     * System Design (Rev 3, Source 302, 493)
     * @param  \App\Http\Requests\UpdateEmailApplicationRequest  $request
     * @param  \App\Models\EmailApplication  $emailApplication
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateEmailApplicationRequest $request, EmailApplication $emailApplication): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $validatedData = $request->validated();

        if (!$emailApplication->isDraft()) {
            Log::warning("User ID {$user->id} attempt to update non-draft EmailApplication ID {$emailApplication->id}.", [
                'application_status' => $emailApplication->status,
            ]);
            return redirect()
                ->route('email-applications.show', $emailApplication) // Route name confirmed
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

            return redirect()
                ->route('email-applications.show', $updatedApplication) // Route name confirmed
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
     * System Design (Rev 3, Source 302, 493)
     * @param  \App\Models\EmailApplication  $emailApplication
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitApplication(EmailApplication $emailApplication): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $this->authorize('submit', $emailApplication);

        Log::info("EmailApplicationController@submitApplication: User ID {$user->id} attempting to submit EmailApplication ID {$emailApplication->id}.");

        try {
            $submittedApplication = $this->emailApplicationService->submitApplication(
                $emailApplication,
                $user
            );
            Log::info("EmailApplication ID {$submittedApplication->id} submitted successfully by User ID {$user->id}. Status: {$submittedApplication->status}");

            return redirect()
                ->route('email-applications.show', $submittedApplication) // Route name confirmed
                ->with('success', __('Permohonan e-mel berjaya dihantar untuk kelulusan.'));
        } catch (Throwable $e) {
            Log::error("Error submitting EmailApplication ID {$emailApplication->id} by User ID {$user->id}.", [
                'error' => $e->getMessage(),
                'exception_trace_snippet' => substr($e->getTraceAsString(), 0, 500),
            ]);
            $errorMessage = ($e instanceof \RuntimeException || $e instanceof \InvalidArgumentException)
                ? $e->getMessage()
                : __('Gagal menghantar permohonan e-mel disebabkan ralat sistem.');
            return redirect()->route('email-applications.show', $emailApplication)->with('error', $errorMessage); // Route name confirmed
        }
    }

    /**
     * Remove the specified email application (soft delete).
     * System Design (Rev 3, Source 302, 493)
     * @param  \App\Models\EmailApplication  $emailApplication
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(EmailApplication $emailApplication): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $this->authorize('delete', $emailApplication); // Authorization check

        if (!$emailApplication->isDraft()) {
            Log::warning("User ID {$user->id} attempt to delete non-draft EmailApplication ID {$emailApplication->id}.", [
                'application_status' => $emailApplication->status,
            ]);
            return redirect()
                ->route('email-applications.show', $emailApplication) // Route name confirmed
                ->with('error', __('Hanya draf permohonan yang boleh dibuang.'));
        }

        Log::info("EmailApplicationController@destroy: User ID {$user->id} attempting to soft delete EmailApplication ID {$emailApplication->id}.");

        try {
            $this->emailApplicationService->deleteApplication( // Assuming service method handles actual deletion
                $emailApplication,
                $user
            );

            Log::info("EmailApplication ID {$emailApplication->id} soft deleted successfully by User ID {$user->id}.");
            // System design (Source 210) indicates index is handled by Livewire (EmailApplicationsIndexLW)
            return redirect()->route('email-applications.index') // Route name confirmed
                ->with('success', __('Permohonan e-mel berjaya dibuang.'));
        } catch (Throwable $e) {
            Log::error("Error soft deleting EmailApplication ID {$emailApplication->id} by User ID {$user->id}.", [
                'error' => $e->getMessage(),
                'exception_trace_snippet' => substr($e->getTraceAsString(), 0, 500),
            ]);
            // It might be better to redirect to the index or show page with error
            return redirect()->route('email-applications.show', $emailApplication)
                             ->with('error', __('Gagal membuang permohonan e-mel disebabkan ralat sistem: ') . $e->getMessage());
        }
    }
}
