<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Permission\Guard;
use Illuminate\Support\Facades\Log; // Added for logging

class Role extends SpatieRole
{
    // You can add custom logic or properties to your Role model here if needed.

    /**
     * A role belongs to some users.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            $this->getUserModelClass(),
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.role_pivot_key') ?: 'role_id',
            config('permission.column_names.model_morph_key')
        );
    }

    /**
     * Helper method to get the User model class string.
     * This uses Spatie's Guard::getModelForGuard() for robustness.
     *
     * @return string
     * @throws \Exception if the User model class cannot be determined.
     */
    protected function getUserModelClass(): string
    {
        $defaultGuardNameForStaticClass = Guard::getDefaultName(static::class); // Default guard for Role class itself
        $currentRoleGuardNameAttribute = $this->attributes['guard_name'] ?? null;

        // Determine the guard name to use for this specific role instance
        // Prefer the guard_name attribute if it's explicitly set on the role instance,
        // otherwise, fall back to the application's default guard.
        $guardNameToUse = $currentRoleGuardNameAttribute ?: $defaultGuardNameForStaticClass;

        if (empty($guardNameToUse)) {
            $errorMessage = 'Guard name could not be determined for Role ID: ' . ($this->id ?? 'N/A') . '. The guard_name attribute is empty/null, and no default guard is configured for the application.';
            Log::error($errorMessage, [
                'role_id' => $this->id ?? 'N/A',
                'role_attributes' => $this->attributes,
                'default_guard_for_role_class' => $defaultGuardNameForStaticClass,
                'resolved_guard_to_use' => $guardNameToUse,
            ]);
            throw new \Exception($errorMessage);
        }

        $userModelClass = Guard::getModelForGuard((string) $guardNameToUse);

        if (is_null($userModelClass)) {
            $errorMessage = "Could not determine the User model class for guard '{$guardNameToUse}' (Role ID: " . ($this->id ?? 'N/A') . "). Guard::getModelForGuard returned null. Ensure this guard and its provider are correctly configured in config/auth.php with a valid user model.";
            Log::error($errorMessage, [
                'role_id' => $this->id ?? 'N/A',
                'role_guard_name_attribute' => $currentRoleGuardNameAttribute,
                'resolved_guard_name_used' => $guardNameToUse,
                'auth_config_defaults_guard' => config('auth.defaults.guard'),
                'auth_config_guard_details' => config('auth.guards.' . $guardNameToUse),
                'provider_for_guard' => config('auth.guards.' . $guardNameToUse . '.provider'),
                'model_for_provider' => config('auth.providers.' . (config('auth.guards.' . $guardNameToUse . '.provider')) . '.model'),
            ]);
            throw new \Exception($errorMessage);
        }

        return $userModelClass;
    }
}
