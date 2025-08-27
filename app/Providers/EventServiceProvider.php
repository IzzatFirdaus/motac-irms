<?php

// EventServiceProvider.php

namespace App\Providers;

// Import required models and observer
use App\Models\Approval;
use App\Models\Department;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Grade;
use App\Models\HelpdeskAttachment;
use App\Models\HelpdeskCategory;
use App\Models\HelpdeskComment;
use App\Models\HelpdeskPriority;
use App\Models\HelpdeskTicket;
use App\Models\Import;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\Location as EquipmentLocation;
use App\Models\Notification as CustomNotification;
// Helpdesk Models
use App\Models\Position;
use App\Models\Setting;
use App\Models\SubCategory as EquipmentSubCategory;
use App\Models\User;
use App\Observers\BlameableObserver;
// Laravel Events & Listeners
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Registers events and model observers for audit/history and Helpdesk integration.
 * All EmailApplication references removed as per the v4.0 transformation plan.
 */
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
     * - BlameableObserver tracks created_by, updated_by, deleted_by, etc.
     * - Helpdesk models are included as per v4.0 requirements.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $observers = [
        // User & HR Models
        User::class       => [BlameableObserver::class],
        Department::class => [BlameableObserver::class],
        Position::class   => [BlameableObserver::class],
        Grade::class      => [BlameableObserver::class],

        // Loan Application & Transaction Models
        LoanApplication::class     => [BlameableObserver::class],
        LoanApplicationItem::class => [BlameableObserver::class],
        LoanTransaction::class     => [BlameableObserver::class],
        LoanTransactionItem::class => [BlameableObserver::class],

        // Approval Model
        Approval::class => [BlameableObserver::class],

        // Inventory & Supporting Models
        Equipment::class            => [BlameableObserver::class],
        EquipmentCategory::class    => [BlameableObserver::class],
        EquipmentSubCategory::class => [BlameableObserver::class],
        EquipmentLocation::class    => [BlameableObserver::class],

        // System Utility Models
        Setting::class => [BlameableObserver::class],
        Import::class  => [BlameableObserver::class],

        // Custom Notification Model for audit trails
        CustomNotification::class => [BlameableObserver::class],

        // Helpdesk Models as per v4.0 transformation plan
        HelpdeskTicket::class     => [BlameableObserver::class],
        HelpdeskCategory::class   => [BlameableObserver::class],
        HelpdeskPriority::class   => [BlameableObserver::class],
        HelpdeskComment::class    => [BlameableObserver::class],
        HelpdeskAttachment::class => [BlameableObserver::class],
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
