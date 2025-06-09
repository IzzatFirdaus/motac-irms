<?php

namespace App\Providers;

// Models to be observed by BlameableObserver
use App\Models\Approval;
use App\Models\Department;
use App\Models\EmailApplication;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Grade;
use App\Models\Import;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\Location as EquipmentLocation;
use App\Models\Notification as CustomNotification;
use App\Models\Position; // Ensure this is imported
use App\Models\Setting;
use App\Models\SubCategory as EquipmentSubCategory;
use App\Models\User;
use App\Observers\BlameableObserver; // Ensure this observer exists and functions as expected
// Laravel Events & Listeners
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * The model observers for your application.
     * This array registers the BlameableObserver for all models that require
     * created_by, updated_by, and/or deleted_by audit trails as per System Design.
     * Ensure BlameableObserver correctly sets user IDs.
     *
     * @var array<class-string, array<int, class-string<\object>>>
     */
    protected $observers = [
        // User & Organizational Data Models
        User::class => [BlameableObserver::class],
        Department::class => [BlameableObserver::class],
        Position::class => [BlameableObserver::class], // Ensure this line is present and uncommented
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
        EquipmentSubCategory::class => [BlameableObserver::class],
        EquipmentLocation::class => [BlameableObserver::class],

        // System Utility Models (if they have blameable fields as per DB design)
        Setting::class => [BlameableObserver::class],
        Import::class => [BlameableObserver::class],

        // Custom Notification Model (if it has blameable fields)
        CustomNotification::class => [BlameableObserver::class], // System Design Ref: Custom model for DB notifications with audit trails
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
