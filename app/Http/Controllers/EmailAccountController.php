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

/**
 * Controller for IT Administrator actions related to Email Applications and Account Provisioning.
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Sections 3.1, 9.2
 */
class EmailAccountController extends Controller
{
    protected EmailApplicationService $emailApplicationService;

    public function __construct(EmailApplicationService $emailApplicationService)
    {
        $this->middleware('auth');
        $this->emailApplicationService = $emailApplicationService;

        $this->middleware('can:viewAnyAdmin,'.EmailApplication::class)->only('indexForAdmin');
        $this->middleware('can:viewAdmin,'.EmailApplication::class)->only('showForAdmin');
    }

    /**
     * Display a listing of email applications for administrators to manage/process.
     * Corresponds to route: resource-management.email-applications-admin.index
     */
    public function indexForAdmin(): View|RedirectResponse
    {
        Log::info('EmailAccountController@indexForAdmin: Fetching email applications for admin view.', ['admin_user_id' => Auth::id()]);
        try {
            $statusesForAdminView = [
                EmailApplication::STATUS_PENDING_ADMIN,
                EmailApplication::STATUS_APPROVED,
                EmailApplication::STATUS_PROCESSING,
                EmailApplication::STATUS_PROVISION_FAILED,
                EmailApplication::STATUS_COMPLETED,
            ];

            $emailApplications = EmailApplication::with(['user:id,name'])
                ->whereIn('status', $statusesForAdminView)
                ->orderBy('updated_at', 'desc')
                ->paginate(config('pagination.default_size', 15));

            return view('admin.email-applications.index', compact('emailApplications'));
        } catch (Throwable $e) {
            Log::error('Error fetching email applications for admin: '.$e->getMessage(), [
                'admin_user_id' => Auth::id(),
                'exception_class' => get_class($e),
                'exception_trace_snippet' => substr($e->getTraceAsString(), 0, 500),
            ]);

            return redirect()->route('dashboard')->with('error', __('Gagal memuatkan senarai permohonan e-mel. Sila cuba lagi kemudian.'));
        }
    }

    /**
     * Display the specified email application for an administrator.
     * Corresponds to route: resource-management.email-applications-admin.show
     */
    public function showForAdmin(EmailApplication $emailApplication): View
    {
        Log::info("EmailAccountController@showForAdmin: Displaying EmailApplication ID {$emailApplication->id} for admin.", ['admin_user_id' => Auth::id()]);

        // Use the default relations from the service for consistency
        $emailApplication->loadMissing($this->emailApplicationService->getDefaultEmailApplicationRelations());

        return view('admin.email-applications.show', compact('emailApplication'));
    }

    /**
     * Handle the IT Admin action to process an approved email application for provisioning.
     * Corresponds to route: resource-management.email-applications-admin.process
     */
    public function processApplication(
        ProcessEmailProvisioningRequest $request,
        EmailApplication $emailApplication
    ): RedirectResponse {
        /** @var User $actingAdmin */
        $actingAdmin = $request->user();
        $validatedData = $request->validated();

        $actionableStatuses = [
            EmailApplication::STATUS_PENDING_ADMIN,
            EmailApplication::STATUS_APPROVED,
            EmailApplication::STATUS_PROVISION_FAILED, // Allow reprocessing
        ];

        if (! in_array($emailApplication->status, $actionableStatuses)) {
            Log::warning("Attempted to process EmailApplication ID {$emailApplication->id} not in an actionable status for provisioning.", [
                'acting_admin_id' => $actingAdmin->id,
                'current_status' => $emailApplication->status,
                'allowed_statuses' => $actionableStatuses,
            ]);

            return redirect()->back()->with('error', __('Permohonan ini tidak dalam status yang membenarkan pemprosesan penyediaan pada masa ini.'));
        }

        Log::info("IT Admin (User ID: {$actingAdmin->id}) initiating provisioning for EmailApplication ID {$emailApplication->id}.", ['validated_data' => $validatedData]);

        try {
            $updatedApplication = $this->emailApplicationService->processProvisioning(
                $emailApplication,
                $validatedData,
                $actingAdmin
            );

            if ($updatedApplication->status === EmailApplication::STATUS_COMPLETED) {
                return redirect()->route('resource-management.email-applications-admin.show', $updatedApplication)
                    ->with('success', __('Permohonan e-mel berjaya diproses dan akaun telah disediakan.'));
            } elseif ($updatedApplication->status === EmailApplication::STATUS_PROVISION_FAILED) {
                $errorMessage = $updatedApplication->rejection_reason ?? __('Proses penyediaan akaun gagal. Sila semak log sistem atau nota permohonan.');

                return redirect()->route('resource-management.email-applications-admin.show', $updatedApplication)
                    ->with('error', __('Gagal memproses permohonan e-mel: ').$errorMessage);
            } else {
                Log::warning("Email provisioning for App ID {$emailApplication->id} by Admin ID {$actingAdmin->id} ended with an unexpected status: {$updatedApplication->status}.");
                $statusMessage = $updatedApplication->status_translated ?? $updatedApplication->status;

                return redirect()->route('resource-management.email-applications-admin.show', $updatedApplication)
                    ->with('warning', __('Proses permohonan selesai dengan status yang tidak dijangka: ').e($statusMessage));
            }
        } catch (Throwable $e) {
            Log::error("Exception in EmailAccountController@processApplication for App ID {$emailApplication->id}: ".$e->getMessage(), [
                'acting_admin_id' => $actingAdmin->id,
                'exception_class' => get_class($e),
                'exception_trace_snippet' => substr($e->getTraceAsString(), 0, 1000),
                'request_data' => $request->except(['_token', 'password', 'password_confirmation']),
            ]);

            return redirect()->back()->withInput()->with('error', __('Gagal memproses permohonan e-mel disebabkan ralat sistem. Sila hubungi sokongan teknikal.'));
        }
    }
}
