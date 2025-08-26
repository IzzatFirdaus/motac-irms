<?php

// api.php

declare(strict_types=1);

// REMOVED: use App\Http\Controllers\Api\EmailProvisioningController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Standard Sanctum route to get authenticated user details
Route::middleware('auth:sanctum')
    ->get('/user', function (Request $request) {
        return $request->user()->load(['roles', 'permissions']); // Optionally load roles/permissions
    })
    ->name('api.auth.user'); // More specific name

// MOTAC System Specific API Routes (Versioned)
Route::prefix('v1')
    ->middleware('auth:sanctum') // Protect these MOTAC-specific endpoints
    ->name('api.v1.') // Name prefix for v1 routes
    ->group(function (): void {
        // REMOVED: Email Provisioning API endpoint
        // Route::post('/email-accounts/provision', [EmailProvisioningController::class, 'provisionEmailAccount'])
        //     ->name('email-accounts.provision');

        // Example: API endpoint to check equipment availability (if needed for external integration)
        // Route::get('/equipment/{equipment_tag_id}/availability', [App\Http\Controllers\Api\EquipmentApiController::class, 'checkAvailability'])
        //     ->name('equipment.check-availability');

        // Add other MOTAC-specific API endpoints here if required for integrations.
    });

// Public API routes (if any) can be defined outside the auth:sanctum group
// Example:
// Route::get('/v1/public-data', [SomePublicController::class, 'getData'])->name('api.v1.public-data');
