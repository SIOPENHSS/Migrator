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

            'filesystems.disks.product.driver'     => 'local',
            'filesystems.disks.product.root'       => storage_path('app/public/merchant/[merchant]/product'),
            'filesystems.disks.product.url'        => config('app.url') . '/storage/merchant/[merchant]/product',
            'filesystems.disks.product.visibility' => 'public',

            'filesystems.disks.dummy.driver'     => 'local',
            'filesystems.disks.dummy.root'       => storage_path('app/public'),
            'filesystems.disks.dummy.url'        => config('app.url') . '/market/img/dummy/',
            'filesystems.disks.dummy.visibility' => 'public',
        ]);
    }
}
