<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmailApplicationRequest;
use App\Http\Requests\SubmitEmailApplicationRequest;
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

        $this->authorizeResource(EmailApplication::class, 'email_application', [
            'except' => ['index', 'create', 'edit'],
        ]);
    }

    public function store(StoreEmailApplicationRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $validatedData = $request->validated();

        Log::info("EmailApplicationController@store: User ID {$user->id} storing new email application draft.");

        try {
            $application = $this->emailApplicationService->createDraftApplication(
                $validatedData,
                $user
            );
            Log::info("Email application draft ID: {$application->id} created successfully by User ID: {$user->id}.");

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

    public function show(EmailApplication $emailApplication): View
    {
        Log::info("EmailApplicationController@show: User ID ".Auth::id()." viewing EmailApplication ID {$emailApplication->id}.");

        // Use the default relations defined in the service for consistency
        $emailApplication->loadMissing($this->emailApplicationService->getDefaultEmailApplicationRelations());

        return view('email-applications.show', compact('emailApplication'));
    }

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
                ->route('email-applications.show', $emailApplication)
                ->with('error', __('Hanya draf permohonan yang boleh dikemaskini.'));
        }

        Log::info("EmailApplicationController@update: User ID {$user->id} attempting to update EmailApplication ID {$emailApplication->id}.");

        try {
            $updatedApplication = $this->emailApplicationService->updateDraftApplication(
                $emailApplication,
                $validatedData,
                $user
            );
            Log::info("EmailApplication ID {$updatedApplication->id} updated successfully by User ID {$user->id}.");

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

    public function submitApplication(SubmitEmailApplicationRequest $request, EmailApplication $emailApplication): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $validatedData = $request->validated();

        Log::info("EmailApplicationController@submitApplication: User ID {$user->id} attempting to submit EmailApplication ID {$emailApplication->id}.");

        try {
            $submittedApplication = $this->emailApplicationService->submitDraftApplication(
                $emailApplication,
                $validatedData,
                $user
            );
            Log::info("EmailApplication ID {$submittedApplication->id} submitted successfully by User ID {$user->id}. Status: {$submittedApplication->status}");

            return redirect()
                ->route('email-applications.show', $submittedApplication)
                ->with('success', __('Permohonan e-mel berjaya dihantar untuk kelulusan.'));
        } catch (Throwable $e) {
            Log::error("Error submitting EmailApplication ID {$emailApplication->id} by User ID {$user->id}.", [
                'error' => $e->getMessage(),
                'exception_trace_snippet' => substr($e->getTraceAsString(), 0, 500),
                'request_data' => $request->except(['_token', 'password', 'password_confirmation']),
            ]);
            $errorMessage = ($e instanceof \RuntimeException || $e instanceof \InvalidArgumentException)
                ? $e->getMessage()
                : __('Gagal menghantar permohonan e-mel disebabkan ralat sistem.');
            return redirect()->route('email-applications.show', $emailApplication)->with('error', $errorMessage);
        }
    }

    public function destroy(EmailApplication $emailApplication): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $this->authorize('delete', $emailApplication);

        if (!$emailApplication->isDraft()) {
            Log::warning("User ID {$user->id} attempt to delete non-draft EmailApplication ID {$emailApplication->id}.", [
                'application_status' => $emailApplication->status,
            ]);
            return redirect()
                ->route('email-applications.show', $emailApplication)
                ->with('error', __('Hanya draf permohonan yang boleh dibuang.'));
        }
        Log::info("EmailApplicationController@destroy: User ID {$user->id} attempting to soft delete EmailApplication ID {$emailApplication->id}.");
        try {
            $this->emailApplicationService->deleteApplication(
                $emailApplication,
                $user
            );
            Log::info("EmailApplication ID {$emailApplication->id} soft deleted successfully by User ID {$user->id}.");
            return redirect()->route('email-applications.index')
                ->with('success', __('Permohonan e-mel berjaya dibuang.'));
        } catch (Throwable $e) {
            Log::error("Error soft deleting EmailApplication ID {$emailApplication->id} by User ID {$user->id}.", [
                'error' => $e->getMessage(),
                'exception_trace_snippet' => substr($e->getTraceAsString(), 0, 500),
            ]);
            return redirect()->route('email-applications.show', $emailApplication)
                             ->with('error', __('Gagal membuang permohonan e-mel disebabkan ralat sistem: ') . $e->getMessage());
        }
    }
}
