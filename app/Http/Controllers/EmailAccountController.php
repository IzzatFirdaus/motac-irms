<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EmailApplication;
use App\Models\User;
use App\Services\EmailApplicationService; // EmailProvisioningService is used via EmailApplicationService
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class EmailAccountController extends Controller
{
    use AuthorizesRequests;

    // EmailProvisioningService is a dependency of EmailApplicationService, not directly used here
    protected EmailApplicationService $emailApplicationService;

    public function __construct(EmailApplicationService $emailApplicationService)
    {
        $this->middleware('auth');
        // Ensure the policy 'process' exists in EmailApplicationPolicy
        $this->middleware('can:process,emailApplication')->only('processEmailApplication'); // Renamed method

        $this->emailApplicationService = $emailApplicationService;
    }

    /**
     * Handle the IT Admin action to process an approved email application for provisioning.
     */
    public function processEmailApplication(// Renamed method for clarity
        Request $request,
        EmailApplication $emailApplication // Route Model Binding
    ): RedirectResponse {
        /** @var User $actingUser */
        $actingUser = Auth::user(); // Ensure user is authenticated via middleware

        // Policy 'can:process,emailApplication' already checked by middleware
        // Initial status check is good practice
        if ($emailApplication->status !== EmailApplication::STATUS_APPROVED) { //
            Log::warning("Attempted to process email application ID {$emailApplication->id} not in APPROVED status.", [
                'acting_user_id' => $actingUser->id,
                'current_status' => $emailApplication->status,
            ]);

            return redirect()->back()->with('error', 'Permohonan ini tidak dalam status "Diluluskan" dan tidak boleh diproses untuk penyediaan.');
        }

        // Validation for admin inputs needed for provisioning
        $validatedData = $request->validate([
            'final_assigned_email' => 'required|email|max:255',
            'user_id_assigned' => 'nullable|string|max:255',
            // 'admin_notes' => 'nullable|string', // If admins add notes during this step
        ]);

        Log::info("IT Admin (User ID: {$actingUser->id}) initiating provisioning for EmailApplication ID {$emailApplication->id}.", $validatedData);

        try {
            // Call the appropriate method in EmailApplicationService which in turn uses EmailProvisioningService
            // The method signature for processProvisioning in EmailApplicationService is:
            // processProvisioning(EmailApplication $application, array $provisioningDetails, User $actingUser)
            $updatedApplication = $this->emailApplicationService->processProvisioning( //
                $emailApplication,
                $validatedData, // This array contains 'final_assigned_email', 'user_id_assigned'
                $actingUser
            );

            if ($updatedApplication->status === EmailApplication::STATUS_COMPLETED) { //
                return redirect()->route('email-applications.show', $updatedApplication) // Adjust route name if necessary
                    ->with('success', 'Permohonan e-mel berjaya diproses dan akaun telah disediakan.');
            } elseif ($updatedApplication->status === EmailApplication::STATUS_PROVISION_FAILED) { //
                $errorMessage = $updatedApplication->rejection_reason ?? 'Proses penyediaan akaun gagal. Sila semak log.';

                return redirect()->route('email-applications.show', $updatedApplication)
                    ->with('error', 'Gagal memproses permohonan e-mel: '.$errorMessage);
            } else {
                // Should not happen if service correctly sets status
                return redirect()->back()->with('warning', 'Proses permohonan selesai dengan status yang tidak dijangka: '.$updatedApplication->status);
            }
        } catch (Throwable $e) {
            Log::error("Exception in EmailAccountController@processEmailApplication for App ID {$emailApplication->id}: ".$e->getMessage(), [
                'acting_user_id' => $actingUser->id,
                'exception_trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->with('error', 'Gagal memproses permohonan e-mel: '.$e->getMessage());
        }
    }
}
