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

        Log::info(sprintf('EmailApplicationController@store: User ID %d storing new email application draft.', $user->id));

        try {
            $application = $this->emailApplicationService->createDraftApplication(
                $validatedData,
                $user
            );
            Log::info(sprintf('Email application draft ID: %d created successfully by User ID: %d.', $application->id, $user->id));

            return redirect()
                ->route('email-applications.show', $application)
                ->with('success', __('Draf permohonan e-mel berjaya dicipta. Anda boleh menyunting atau menghantarnya untuk kelulusan.'));
        } catch (Throwable $throwable) {
            Log::error(sprintf('Error storing email application draft for User ID: %d.', $user->id), [
                'error' => $throwable->getMessage(),
                'exception_trace_snippet' => substr($throwable->getTraceAsString(), 0, 500),
                'request_data' => $request->except(['_token', 'password', 'password_confirmation']),
            ]);

            return redirect()->back()->withInput()->with('error', __('Gagal mencipta draf permohonan e-mel: ').$throwable->getMessage());
        }
    }

    public function show(EmailApplication $emailApplication): View
    {
        Log::info('EmailApplicationController@show: User ID '.Auth::id().sprintf(' viewing EmailApplication ID %d.', $emailApplication->id));

        // Use the default relations defined in the service for consistency
        $emailApplication->loadMissing($this->emailApplicationService->getDefaultEmailApplicationRelations());

        return view('email-applications.show', ['emailApplication' => $emailApplication]);
    }

    public function update(UpdateEmailApplicationRequest $request, EmailApplication $emailApplication): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $validatedData = $request->validated();

        if (! $emailApplication->isDraft()) {
            Log::warning(sprintf('User ID %d attempt to update non-draft EmailApplication ID %d.', $user->id, $emailApplication->id), [
                'application_status' => $emailApplication->status,
            ]);

            return redirect()
                ->route('email-applications.show', $emailApplication)
                ->with('error', __('Hanya draf permohonan yang boleh dikemaskini.'));
        }

        Log::info(sprintf('EmailApplicationController@update: User ID %d attempting to update EmailApplication ID %d.', $user->id, $emailApplication->id));

        try {
            $updatedApplication = $this->emailApplicationService->updateDraftApplication(
                $emailApplication,
                $validatedData,
                $user
            );
            Log::info(sprintf('EmailApplication ID %d updated successfully by User ID %d.', $updatedApplication->id, $user->id));

            return redirect()
                ->route('email-applications.show', $updatedApplication)
                ->with('success', __('Permohonan e-mel berjaya dikemaskini.'));
        } catch (Throwable $throwable) {
            Log::error(sprintf('Error updating EmailApplication ID %d by User ID %d.', $emailApplication->id, $user->id), [
                'error' => $throwable->getMessage(),
                'exception_trace_snippet' => substr($throwable->getTraceAsString(), 0, 500),
                'request_data' => $request->except(['_token', 'password', 'password_confirmation']),
            ]);

            return redirect()->back()->withInput()->with('error', __('Gagal mengemaskini permohonan e-mel: ').$throwable->getMessage());
        }
    }

    public function submitApplication(SubmitEmailApplicationRequest $request, EmailApplication $emailApplication): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $validatedData = $request->validated();

        Log::info(sprintf('EmailApplicationController@submitApplication: User ID %d attempting to submit EmailApplication ID %d.', $user->id, $emailApplication->id));

        try {
            $submittedApplication = $this->emailApplicationService->submitDraftApplication(
                $emailApplication,
                $validatedData,
                $user
            );
            Log::info(sprintf('EmailApplication ID %d submitted successfully by User ID %d. Status: %s', $submittedApplication->id, $user->id, $submittedApplication->status));

            return redirect()
                ->route('email-applications.show', $submittedApplication)
                ->with('success', __('Permohonan e-mel berjaya dihantar untuk kelulusan.'));
        } catch (Throwable $throwable) {
            Log::error(sprintf('Error submitting EmailApplication ID %d by User ID %d.', $emailApplication->id, $user->id), [
                'error' => $throwable->getMessage(),
                'exception_trace_snippet' => substr($throwable->getTraceAsString(), 0, 500),
                'request_data' => $request->except(['_token', 'password', 'password_confirmation']),
            ]);
            $errorMessage = ($throwable instanceof \RuntimeException || $throwable instanceof \InvalidArgumentException)
                ? $throwable->getMessage()
                : __('Gagal menghantar permohonan e-mel disebabkan ralat sistem.');

            return redirect()->route('email-applications.show', $emailApplication)->with('error', $errorMessage);
        }
    }

    public function destroy(EmailApplication $emailApplication): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $this->authorize('delete', $emailApplication);

        if (! $emailApplication->isDraft()) {
            Log::warning(sprintf('User ID %d attempt to delete non-draft EmailApplication ID %d.', $user->id, $emailApplication->id), [
                'application_status' => $emailApplication->status,
            ]);

            return redirect()
                ->route('email-applications.show', $emailApplication)
                ->with('error', __('Hanya draf permohonan yang boleh dibuang.'));
        }

        Log::info(sprintf('EmailApplicationController@destroy: User ID %d attempting to soft delete EmailApplication ID %d.', $user->id, $emailApplication->id));
        try {
            $this->emailApplicationService->deleteApplication(
                $emailApplication,
                $user
            );
            Log::info(sprintf('EmailApplication ID %d soft deleted successfully by User ID %d.', $emailApplication->id, $user->id));

            return redirect()->route('email-applications.index')
                ->with('success', __('Permohonan e-mel berjaya dibuang.'));
        } catch (Throwable $throwable) {
            Log::error(sprintf('Error soft deleting EmailApplication ID %d by User ID %d.', $emailApplication->id, $user->id), [
                'error' => $throwable->getMessage(),
                'exception_trace_snippet' => substr($throwable->getTraceAsString(), 0, 500),
            ]);

            return redirect()->route('email-applications.show', $emailApplication)
                ->with('error', __('Gagal membuang permohonan e-mel disebabkan ralat sistem: ').$throwable->getMessage());
        }
    }
}
