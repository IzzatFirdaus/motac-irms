<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmailApplicationRequest;    // Assumed to exist
use App\Http\Requests\UpdateEmailApplicationRequest;    // Assumed to exist
use App\Models\EmailApplication;                       //
use App\Models\User;                                   //
use App\Services\EmailApplicationService;              //
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable; // Catching generic Throwable for broad error handling

class EmailApplicationController extends Controller
{
    protected EmailApplicationService $emailApplicationService;

    public function __construct(EmailApplicationService $emailApplicationService)
    {
        $this->emailApplicationService = $emailApplicationService;
        $this->middleware('auth');

        // Automatically authorize resource methods based on EmailApplicationPolicy.
        // 'index' and 'create' are handled by Livewire.
        // 'submitApplication' will have its authorization checked manually using the 'submit' ability.
        $this->authorizeResource(EmailApplication::class, 'emailApplication', [
            'only' => ['show', 'store', 'edit', 'update', 'destroy'],
        ]);
    }

    /**
     * Store a newly created email application draft.
     * Validation and authorization (create ability) handled by StoreEmailApplicationRequest.
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
            ); //
            Log::info("Email application draft ID: {$application->id} created successfully by User ID: {$user->id}.");

            return redirect()
                ->route('resource-management.my-applications.email-applications.show', $application) // Route from web.php
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
     * Authorization to 'view' is handled by authorizeResource.
     *
     * @param  \App\Models\EmailApplication  $emailApplication
     * @return \Illuminate\View\View
     */
    public function show(EmailApplication $emailApplication): View
    {
        // $this->authorize('view', $emailApplication); // Covered by authorizeResource

        Log::info("EmailApplicationController@show: User ID ".Auth::id()." viewing EmailApplication ID {$emailApplication->id}.");

        $emailApplication->loadMissing([
            'user.department', 'user.grade', 'user.position', //
            'supportingOfficerUser.grade', //
            'approvals.officer:id,name',     //
            'creator:id,name',               //
            'updater:id,name',               //
        ]);

        return view('email-applications.show', compact('emailApplication')); // View path
    }

    /**
     * Show the form for editing the specified email application.
     * Authorization to 'update' (implicitly for edit form) is handled by authorizeResource.
     *
     * @param  \App\Models\EmailApplication  $emailApplication
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(EmailApplication $emailApplication): View|RedirectResponse
    {
        // $this->authorize('update', $emailApplication); // Covered by authorizeResource

        if (!$emailApplication->isDraft()) { //
            Log::warning("User ID ".Auth::id()." attempt to edit non-draft EmailApplication ID {$emailApplication->id}.", [
                'application_status' => $emailApplication->status,
            ]);
            return redirect()
                ->route('resource-management.my-applications.email-applications.show', $emailApplication) //
                ->with('error', __('Hanya draf permohonan yang boleh disunting.'));
        }

        Log::info("EmailApplicationController@edit: User ID ".Auth::id()." viewing edit form for EmailApplication ID {$emailApplication->id}.");
        return view('email-applications.edit', compact('emailApplication')); // View path
    }

    /**
     * Update the specified email application in storage.
     * Validation and authorization (update ability) handled by UpdateEmailApplicationRequest.
     *
     * @param  \App\Http\Requests\UpdateEmailApplicationRequest  $request
     * @param  \App\Models\EmailApplication  $emailApplication
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateEmailApplicationRequest $request, EmailApplication $emailApplication): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $validatedData = $request->validated();

        // Business rule: only drafts can be updated. Policy/FormRequest should ideally also enforce this.
        if (!$emailApplication->isDraft()) { //
            Log::warning("User ID {$user->id} attempt to update non-draft EmailApplication ID {$emailApplication->id}.", [
                'application_status' => $emailApplication->status,
            ]);
            return redirect()
                ->route('resource-management.my-applications.email-applications.show', $emailApplication) //
                ->with('error', __('Hanya draf permohonan yang boleh dikemaskini.'));
        }

        Log::info("EmailApplicationController@update: User ID {$user->id} attempting to update EmailApplication ID {$emailApplication->id}.");

        try {
            $updatedApplication = $this->emailApplicationService->updateApplication(
                $emailApplication,
                $validatedData,
                $user
            ); //
            Log::info("EmailApplication ID {$updatedApplication->id} updated successfully by User ID {$user->id}.");

            return redirect()
                ->route('resource-management.my-applications.email-applications.show', $updatedApplication) //
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
     * Method name matches the route definition in web.php.
     *
     * @param  \App\Models\EmailApplication  $emailApplication
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitApplication(EmailApplication $emailApplication): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // Use 'submit' ability from EmailApplicationPolicy
        $this->authorize('submit', $emailApplication);

        Log::info("EmailApplicationController@submitApplication: User ID {$user->id} attempting to submit EmailApplication ID {$emailApplication->id}.");

        try {
            $submittedApplication = $this->emailApplicationService->submitApplication(
                $emailApplication,
                $user
            ); //
            Log::info("EmailApplication ID {$submittedApplication->id} submitted successfully by User ID {$user->id}. Status: {$submittedApplication->status}");

            return redirect()
                ->route('resource-management.my-applications.email-applications.show', $submittedApplication) //
                ->with('success', __('Permohonan e-mel berjaya dihantar untuk kelulusan.'));
        } catch (Throwable $e) {
            Log::error("Error submitting EmailApplication ID {$emailApplication->id} by User ID {$user->id}.", [
                'error' => $e->getMessage(),
                'exception_trace_snippet' => substr($e->getTraceAsString(), 0, 500),
            ]);
            $errorMessage = ($e instanceof \RuntimeException || $e instanceof \InvalidArgumentException)
                ? $e->getMessage()
                : __('Gagal menghantar permohonan e-mel disebabkan ralat sistem.');
            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Remove the specified email application (soft delete).
     * Authorization to 'delete' is handled by authorizeResource.
     *
     * @param  \App\Models\EmailApplication  $emailApplication
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(EmailApplication $emailApplication): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        // $this->authorize('delete', $emailApplication); // Covered by authorizeResource

        if (!$emailApplication->isDraft()) { //
            Log::warning("User ID {$user->id} attempt to delete non-draft EmailApplication ID {$emailApplication->id}.", [
                'application_status' => $emailApplication->status,
            ]);
            return redirect()
                ->route('resource-management.my-applications.email-applications.show', $emailApplication) //
                ->with('error', __('Hanya draf permohonan yang boleh dibuang.'));
        }

        Log::info("EmailApplicationController@destroy: User ID {$user->id} attempting to soft delete EmailApplication ID {$emailApplication->id}.");

        try {
            $deleted = $this->emailApplicationService->deleteApplication(
                $emailApplication,
                $user
            ); //

            if ($deleted) {
                Log::info("EmailApplication ID {$emailApplication->id} soft deleted successfully by User ID {$user->id}.");
                return redirect()->route('resource-management.my-applications.email.index') // Redirect to Livewire index page
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
