<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProcessEmailProvisioningRequest; // New Form Request
use App\Models\EmailApplication;                       //
use App\Models\User;                                   //
use App\Services\EmailApplicationService;              //
use Illuminate\Http\RedirectResponse;
// use Illuminate\Http\Request; // No longer needed for processApplication
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

        // Apply specific policy middleware.
        // Note: web.php already applies 'role:Admin|IT Admin' to this controller's routes
        // and 'can:process,emailApplication' to the processApplication route.
        // Ensure the 'process' ability alias in the route matches an ability in EmailApplicationPolicy (e.g., 'processByIT').
        // For consistency, if policy method is 'processByIT', route middleware should be 'can:processByIT,emailApplication'.
        // This controller-level middleware might be redundant if routes are fully specific.
        $this->middleware('can:viewAny,App\Models\EmailApplication')->only('indexForAdmin');
        $this->middleware('can:view,emailApplication')->only('showForAdmin');
        // Authorization for 'processApplication' is also handled by ProcessEmailProvisioningRequest::authorize
        // and route-level middleware 'can:process,emailApplication' (which should align with policy ability).
    }

    /**
     * Display a listing of email applications for administrators.
     * (Corresponds to route: admin.resource-management.email-applications.index)
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function indexForAdmin(): View|RedirectResponse
    {
        Log::info('EmailAccountController@indexForAdmin: Fetching email applications for admin view.', ['admin_user_id' => Auth::id()]);
        try {
            // The service should ideally handle fetching logic based on admin context/filters
            // For now, fetching applications that might need attention or are recently processed.
            $emailApplications = EmailApplication::with(['user:id,name']) //
                ->whereIn('status', [
                    EmailApplication::STATUS_PENDING_ADMIN, //
                    EmailApplication::STATUS_APPROVED,      //
                    EmailApplication::STATUS_PROCESSING,    //
                    EmailApplication::STATUS_PROVISION_FAILED, //
                    EmailApplication::STATUS_COMPLETED,     //
                ])
                ->orderBy('updated_at', 'desc')
                ->paginate(config('pagination.default_size', 15));

            return view('admin.email-applications.index', compact('emailApplications')); // Ensure this view exists
        } catch (Throwable $e) {
            Log::error('Error fetching email applications for admin: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('dashboard')->with('error', __('Gagal memuatkan senarai permohonan e-mel.'));
        }
    }

    /**
     * Display the specified email application for an administrator.
     * (Corresponds to route: admin.resource-management.email-applications.show)
     *
     * @param  \App\Models\EmailApplication  $emailApplication
     * @return \Illuminate\View\View
     */
    public function showForAdmin(EmailApplication $emailApplication): View
    {
        Log::info("EmailAccountController@showForAdmin: Displaying EmailApplication ID {$emailApplication->id} for admin.", ['admin_user_id' => Auth::id()]);

        $emailApplication->loadMissing([
            'user.department', 'user.grade', 'user.position', //
            'supportingOfficerUser.grade', //
            'approvals.officer', //
            // 'creator', 'updater' // Blameable fields if needed
        ]);

        return view('admin.email-applications.show', compact('emailApplication')); // Ensure this view exists
    }

    /**
     * Handle the IT Admin action to process an approved email application for provisioning.
     * Method name changed to 'processApplication' to match route definition.
     *
     * @param \App\Http\Requests\ProcessEmailProvisioningRequest $request
     * @param \App\Models\EmailApplication $emailApplication Route Model Binding
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processApplication(
        ProcessEmailProvisioningRequest $request, // Use Form Request
        EmailApplication $emailApplication
    ): RedirectResponse {
        /** @var User $actingAdmin */
        $actingAdmin = $request->user(); // User from FormRequest, already authenticated & authorized by FormRequest
        $validatedData = $request->validated();

        // Policy check is now primarily handled by ProcessEmailProvisioningRequest::authorize()
        // and the route-level middleware `can:process,emailApplication` (ensure 'process' matches a policy ability like 'processByIT').

        // Additional status check for clarity, though policy might also cover this.
        if (!in_array($emailApplication->status, [EmailApplication::STATUS_APPROVED, EmailApplication::STATUS_PENDING_ADMIN])) { //
            Log::warning("Attempted to process EmailApplication ID {$emailApplication->id} not in an actionable status for provisioning.", [
                'acting_admin_id' => $actingAdmin->id,
                'current_status' => $emailApplication->status,
            ]);
            return redirect()->back()->with('error', __('Permohonan ini tidak dalam status yang membenarkan pemprosesan penyediaan.'));
        }

        Log::info("IT Admin (User ID: {$actingAdmin->id}) initiating provisioning for EmailApplication ID {$emailApplication->id}.", $validatedData);

        try {
            $updatedApplication = $this->emailApplicationService->processProvisioning( //
                $emailApplication,
                $validatedData, // Contains 'final_assigned_email', 'user_id_assigned'
                $actingAdmin
            );

            if ($updatedApplication->status === EmailApplication::STATUS_COMPLETED) { //
                return redirect()->route('admin.resource-management.email-applications.show', $updatedApplication) // Use admin route
                    ->with('success', __('Permohonan e-mel berjaya diproses dan akaun telah disediakan.'));
            } elseif ($updatedApplication->status === EmailApplication::STATUS_PROVISION_FAILED) { //
                $errorMessage = $updatedApplication->rejection_reason ?? __('Proses penyediaan akaun gagal. Sila semak log.');
                return redirect()->route('admin.resource-management.email-applications.show', $updatedApplication)
                    ->with('error', __('Gagal memproses permohonan e-mel: ') . $errorMessage);
            } else {
                Log::warning("Email provisioning for App ID {$emailApplication->id} ended with unexpected status: {$updatedApplication->status}.");
                return redirect()->back()->with('warning', __('Proses permohonan selesai dengan status yang tidak dijangka: ') . $updatedApplication->statusTranslated);
            }
        } catch (Throwable $e) {
            Log::error("Exception in EmailAccountController@processApplication for App ID {$emailApplication->id}: " . $e->getMessage(), [
                'acting_admin_id' => $actingAdmin->id,
                'exception_trace_snippet' => substr($e->getTraceAsString(), 0, 500),
            ]);
            return back()->withInput()->with('error', __('Gagal memproses permohonan e-mel disebabkan ralat sistem: ') . $e->getMessage());
        }
    }
}
