<?php

return [

  'models' => [

    /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your permissions. Of course, it
         * is often just the "Permission" model but you may use whatever you like.
         *
         * The model you want to use as a Permission model needs to implement the
         * `Spatie\Permission\Contracts\Permission` contract.
         */
    'permission' => Spatie\Permission\Models\Permission::class,

    /*
         * When using the "HasRoles" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your roles. Of course, it
         * is often just the "Role" model but you may use whatever you like.
         *
         * The model you want to use as a Role model needs to implement the
         * `Spatie\Permission\Contracts\Role` contract.
         */
    //'role' => Spatie\Permission\Models\Role::class,
    'role' => App\Models\Role::class, // Your custom Role model

    /*
         * When using the "HasRoles" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your users.
         *
         * The model you want to use as a User model needs to extend Illuminate\Foundation\Auth\User
         * and use the Spatie\Permission\Traits\HasRoles trait.
         */
    'user' => App\Models\User::class,

  ],

  'table_names' => [
    'roles' => 'roles',
    'permissions' => 'permissions',
    'model_has_permissions' => 'model_has_permissions',
    'model_has_roles' => 'model_has_roles',
    'role_has_permissions' => 'role_has_permissions',
  ],

  'column_names' => [
    'role_pivot_key' => null, // default 'role_id',
    'permission_pivot_key' => null, // default 'permission_id',
    'model_morph_key' => 'model_id',
    'team_foreign_key' => 'team_id',
  ],

  'register_permission_check_method' => true,
  'register_octane_reset_listener' => false,
  'teams' => false,
  'use_passport_client_credentials' => false,
  'display_permission_in_exception' => false,
  'display_role_in_exception' => false,
  'enable_wildcard_permission' => false,
  // 'permission.wildcard_permission' => Spatie\Permission\WildcardPermission::class,

  'cache' => [
    'expiration_time' => \DateInterval::createFromDateString('24 hours'),
    'key' => 'spatie.permission.cache',
    'store' => 'default',
  ],
];
