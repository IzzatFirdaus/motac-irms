<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Role Model.
 *
 * Extends Spatie Role for the system, adds custom logic for user relationships.
 *
 * @property int                             $id
 * @property string                          $name
 * @property string                          $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Role extends SpatieRole
{
    /**
     * Polymorphic relationship to users for this role.
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
     * Helper to resolve User model class for guard.
     */
    protected function getUserModelClass(): string
    {
        $defaultGuardNameForStaticClass = Guard::getDefaultName(static::class);
        $currentRoleGuardNameAttribute  = $this->attributes['guard_name'] ?? null;
        $guardNameToUse                 = $currentRoleGuardNameAttribute ?: $defaultGuardNameForStaticClass;
        if (empty($guardNameToUse)) {
            $errorMessage = 'Guard name could not be determined for Role ID: ' . ($this->id ?? 'N/A') . '.';
            Log::error($errorMessage, [
                'role_id'                      => $this->id ?? 'N/A',
                'role_attributes'              => $this->attributes,
                'default_guard_for_role_class' => $defaultGuardNameForStaticClass,
                'resolved_guard_to_use'        => $guardNameToUse,
            ]);
            throw new \Exception($errorMessage);
        }
        $userModelClass = Guard::getModelForGuard((string) $guardNameToUse);
        if (is_null($userModelClass)) {
            $errorMessage = sprintf("Could not determine the User model class for guard '%s' (Role ID: ", $guardNameToUse) . ($this->id ?? 'N/A') . ').';
            Log::error($errorMessage, [
                'role_id'                    => $this->id ?? 'N/A',
                'role_guard_name_attribute'  => $currentRoleGuardNameAttribute,
                'resolved_guard_name_used'   => $guardNameToUse,
                'auth_config_defaults_guard' => config('auth.defaults.guard'),
                'auth_config_guard_details'  => config('auth.guards.' . $guardNameToUse),
                'provider_for_guard'         => config('auth.guards.' . $guardNameToUse . '.provider'),
                'model_for_provider'         => config('auth.providers.' . (config('auth.guards.' . $guardNameToUse . '.provider')) . '.model'),
            ]);
            throw new \Exception($errorMessage);
        }

        return $userModelClass;
    }
}
