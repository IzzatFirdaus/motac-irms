<?php

namespace App\Providers;

// Models to be observed by BlameableObserver
use App\Models\Approval;
use App\Models\Department;
use App\Models\EmailApplication;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Grade;
use App\Models\Import;                     // If settings are database-driven and auditable
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\Location as EquipmentLocation;         // Alias for clarity
use App\Models\Notification as CustomNotification;    // Alias for clarity if it's your custom App\Models\Notification
use App\Models\Position;
use App\Models\Setting;                     // If import processes are auditable
use App\Models\SubCategory as EquipmentSubCategory; // Alias for clarity
use App\Models\User;
use App\Observers\BlameableObserver; // Ensure this observer exists and functions as expected

// Laravel Events & Listeners
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
// use Illuminate\Support\Facades\Event; // Not explicitly used in this file's current content

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class, // Standard Laravel listener for new user registration
        ],
        // Example for custom events, if any are defined for the system:
        // \App\Events\ApplicationStatusChanged::class => [
        //     \App\Listeners\NotifyApplicantOfStatusChange::class,
        //     \App\Listeners\LogApplicationHistory::class,
        // ],
    ];

    /**
     * The model observers for your application.
     * This array registers the BlameableObserver for all models that require
     * created_by, updated_by, and/or deleted_by audit trails as per System Design[cite: 47, 312, 352].
     * Ensure BlameableObserver correctly sets user IDs.
     *
     * @var array<class-string, array<int, class-string<\object>>>
     */
    protected $observers = [
        // User & Organizational Data Models
        User::class => [BlameableObserver::class],
        Department::class => [BlameableObserver::class],
        Position::class => [BlameableObserver::class],
        Grade::class => [BlameableObserver::class],

        // Application Process Models
        EmailApplication::class => [BlameableObserver::class],
        LoanApplication::class => [BlameableObserver::class],
        LoanApplicationItem::class => [BlameableObserver::class],

        // Transaction Models
        LoanTransaction::class => [BlameableObserver::class],
        LoanTransactionItem::class => [BlameableObserver::class],

        // Approval Model
        Approval::class => [BlameableObserver::class],

        // Inventory & Supporting Models
        Equipment::class => [BlameableObserver::class],
        EquipmentCategory::class => [BlameableObserver::class],
        EquipmentSubCategory::class => [BlameableObserver::class], // Uses alias: SubCategory as EquipmentSubCategory
        EquipmentLocation::class => [BlameableObserver::class],   // Uses alias: Location as EquipmentLocation

        // System Utility Models (if they have blameable fields as per DB design)
        Setting::class => [BlameableObserver::class], // If 'settings' table is used and audited
        Import::class => [BlameableObserver::class],  // If 'imports' table is used and audited

        // Custom Notification Model (if it has blameable fields)
        // System Design Ref: Custom model for DB notifications with audit trails
        CustomNotification::class => [BlameableObserver::class], // Uses alias: Notification as CustomNotification
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        // Laravel automatically registers observers defined in the $observers property.
        // Calling parent::boot() is good practice if the parent ServiceProvider's boot method has logic.
        parent::boot();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     * Setting to false requires explicit registration in the $listen array for event listeners.
     *
     * @return bool
     */
    public function shouldDiscoverEvents(): bool
    {
        return false; // Explicit registration is generally preferred for clarity
    }
}
