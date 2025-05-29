<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProcessEmailProvisioningRequest;
use App\Models\EmailApplication;
use App\Models\User;
use App\Services\EmailApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class EmailAccountController extends Controller
{
    protected EmailApplicationService $emailApplicationService;

    public function __construct(EmailApplicationService $emailApplicationService)
    {
        $this->middleware('auth');
        $this->emailApplicationService = $emailApplicationService;

        // Middleware for authorization, aligned with policies and web.php route definitions
        // Note: web.php applies 'role:Admin|IT Admin'.
        // Controller-level 'can' middleware should match policy methods.
        $this->middleware('can:viewAnyAdmin,' . EmailApplication::class)->only('indexForAdmin'); // Assumes 'viewAnyAdmin' ability in policy
        $this->middleware('can:viewAdmin,' . EmailApplication::class)->only('showForAdmin');   // Assumes 'viewAdmin' ability in policy
        // For processApplication, authorization is primarily handled by:
        // 1. ProcessEmailProvisioningRequest FormRequest's authorize() method.
        // 2. Route-level middleware: `can:processByIT,email_application` (ensure 'processByIT' ability in EmailApplicationPolicy).
    }

    /**
     * Display a listing of email applications for administrators.
     * Corresponds to route: resource-management.email-applications-admin.index
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function indexForAdmin(): View|RedirectResponse
    {
        Log::info('EmailAccountController@indexForAdmin: Fetching email applications for admin view.', ['admin_user_id' => Auth::id()]);
        try {
            $emailApplications = EmailApplication::with(['user:id,name'])
                ->whereIn('status', [
                    EmailApplication::STATUS_PENDING_ADMIN,
                    EmailApplication::STATUS_APPROVED,      // Applications approved by support, awaiting IT admin processing
                    EmailApplication::STATUS_PROCESSING,
                    EmailApplication::STATUS_PROVISION_FAILED,
                    EmailApplication::STATUS_COMPLETED,
                ])
                ->orderBy('updated_at', 'desc')
                ->paginate(config('pagination.default_size', 15));

            // Ensure this view path is correct, e.g., 'resource-management.admin.email-applications.index'
            return view('admin.email-applications.index', compact('emailApplications'));
        } catch (Throwable $e) {
            Log::error('Error fetching email applications for admin: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('dashboard')->with('error', __('Gagal memuatkan senarai permohonan e-mel.'));
        }
    }

    /**
     * Display the specified email application for an administrator.
     * Corresponds to route: resource-management.email-applications-admin.show
     *
     * @param  \App\Models\EmailApplication  $emailApplication
     * @return \Illuminate\View\View
     */
    public function showForAdmin(EmailApplication $emailApplication): View
    {
        Log::info("EmailAccountController@showForAdmin: Displaying EmailApplication ID {$emailApplication->id} for admin.", ['admin_user_id' => Auth::id()]);

        $emailApplication->loadMissing([
            'user.department', 'user.grade', 'user.position',
            'supportingOfficerUser.grade', // Assuming 'supportingOfficerUser' is the correct relation name
            'approvals.officer:id,name',
            // 'creator:id,name', // If you have blameable behavior for creator
            // 'updater:id,name', // If you have blameable behavior for updater
        ]);
        // Ensure this view path is correct
        return view('admin.email-applications.show', compact('emailApplication'));
    }

    /**
     * Handle the IT Admin action to process an approved email application for provisioning.
     * Corresponds to route: resource-management.email-applications-admin.process
     *
     * @param \App\Http\Requests\ProcessEmailProvisioningRequest $request
     * @param \App\Models\EmailApplication $emailApplication Route Model Binding
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processApplication(
        ProcessEmailProvisioningRequest $request,
        EmailApplication $emailApplication
    ): RedirectResponse {
        /** @var User $actingAdmin */
        $actingAdmin = $request->user();
        $validatedData = $request->validated();

        // Additional status check, although policy and FormRequest should be primary gatekeepers.
        // STATUS_PENDING_ADMIN is the primary status IT admins should act upon for provisioning.
        // STATUS_APPROVED might be if it's directly assigned to IT after first-level approval.
        if (!in_array($emailApplication->status, [EmailApplication::STATUS_PENDING_ADMIN, EmailApplication::STATUS_APPROVED])) {
            Log::warning("Attempted to process EmailApplication ID {$emailApplication->id} not in an actionable status for provisioning.", [
                'acting_admin_id' => $actingAdmin->id,
                'current_status' => $emailApplication->status,
            ]);
            return redirect()->back()->with('error', __('Permohonan ini tidak dalam status yang membenarkan pemprosesan penyediaan.'));
        }

        Log::info("IT Admin (User ID: {$actingAdmin->id}) initiating provisioning for EmailApplication ID {$emailApplication->id}.", $validatedData);

        try {
            $updatedApplication = $this->emailApplicationService->processProvisioning(
                $emailApplication,
                $validatedData,
                $actingAdmin
            );

            // Corrected route names
            if ($updatedApplication->status === EmailApplication::STATUS_COMPLETED) {
                return redirect()->route('resource-management.email-applications-admin.show', $updatedApplication)
                    ->with('success', __('Permohonan e-mel berjaya diproses dan akaun telah disediakan.'));
            } elseif ($updatedApplication->status === EmailApplication::STATUS_PROVISION_FAILED) {
                $errorMessage = $updatedApplication->rejection_reason ?? __('Proses penyediaan akaun gagal. Sila semak log.');
                return redirect()->route('resource-management.email-applications-admin.show', $updatedApplication)
                    ->with('error', __('Gagal memproses permohonan e-mel: ') . $errorMessage);
            } else {
                // This case might indicate an unexpected status after processing.
                Log::warning("Email provisioning for App ID {$emailApplication->id} by Admin ID {$actingAdmin->id} ended with an unexpected status: {$updatedApplication->status}.");
                return redirect()->route('resource-management.email-applications-admin.show', $updatedApplication)
                    ->with('warning', __('Proses permohonan selesai dengan status yang tidak dijangka: ') . $updatedApplication->statusTranslated);
            }
        } catch (Throwable $e) {
            Log::error("Exception in EmailAccountController@processApplication for App ID {$emailApplication->id}: " . $e->getMessage(), [
                'acting_admin_id' => $actingAdmin->id,
                'exception_trace_snippet' => substr($e->getTraceAsString(), 0, 500),
                 'request_data' => $request->except(['_token', 'password', 'password_confirmation']), // Log sanitized request data
            ]);
            return redirect()->back()->withInput()->with('error', __('Gagal memproses permohonan e-mel disebabkan ralat sistem: ') . $e->getMessage());
        }
    }
}
