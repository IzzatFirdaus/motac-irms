<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Jetstream's own service provider handles its core registration.
    }

    /**
     * Bootstrap any application services.
     * This is where you can customize Jetstream features.
     * System Design Reference: [cite: 58, 342] (if Jetstream is used)
     */
    public function boot(): void
    {
        $this->configurePermissions();

        // Registering custom action for deleting users
        Jetstream::deleteUsersUsing(DeleteUser::class);

        // Other Jetstream customizations can go here
        // Jetstream::useRoles();
        // Jetstream::role('admin', 'Administrator', [...permissions...])->description(...);
    }

    /**
     * Configure the permissions that are available within the application.
     * These permissions are used by Jetstream for features like API tokens and team management.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']); // Default permissions for new API tokens

        // Define all available API token permissions
        Jetstream::permissions([
            'create',
            'read',
            'update',
            'delete',
            // Add any other custom permissions your API might use
            // 'server:provision',
        ]);
    }
}
