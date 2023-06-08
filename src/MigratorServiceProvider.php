<?php

namespace SIOPEN\Migrator;

use Illuminate\Support\ServiceProvider;
use SIOPEN\Migrator\Models\Role;
use SIOPEN\Migrator\Models\User;

class MigratorServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register() : void
    {
        config([
            'laratrust.user_models'      => User::class,
            'laratrust.models.role'      => Role::class,
            'laratrust.models.roles'     => Role::class,
            'laratrust.tables.role_user' => 'user_roles',
        ]);
    }
}
