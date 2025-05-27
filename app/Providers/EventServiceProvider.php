<?php

namespace App\Providers;

use App\Models\Approval;
use App\Models\Department;
use App\Models\EmailApplication;
use App\Models\Equipment;
use App\Models\Grade;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionItem;
use App\Models\Position;
use App\Models\User;
use App\Observers\BlameableObserver; // Ensure this Observer exists
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

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
     *
     * @var array
     */
    protected $observers = [
        User::class => [BlameableObserver::class],
        Department::class => [BlameableObserver::class],
        Position::class => [BlameableObserver::class],
        Grade::class => [BlameableObserver::class],
        EmailApplication::class => [BlameableObserver::class],
        Equipment::class => [BlameableObserver::class],
        LoanApplication::class => [BlameableObserver::class],
        LoanApplicationItem::class => [BlameableObserver::class],
        LoanTransaction::class => [BlameableObserver::class],
        LoanTransactionItem::class => [BlameableObserver::class],
        Approval::class => [BlameableObserver::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot(); // It's good practice to call parent::boot() if it exists
                        // Or directly register observers if not using $observers property:
        /*
        User::observe(BlameableObserver::class);
        Department::observe(BlameableObserver::class);
        Position::observe(BlameableObserver::class);
        Grade::observe(BlameableObserver::class);
        EmailApplication::observe(BlameableObserver::class);
        Equipment::observe(BlameableObserver::class);
        LoanApplication::observe(BlameableObserver::class);
        LoanApplicationItem::observe(BlameableObserver::class);
        LoanTransaction::observe(BlameableObserver::class);
        LoanTransactionItem::observe(BlameableObserver::class);
        Approval::observe(BlameableObserver::class);
        */
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
