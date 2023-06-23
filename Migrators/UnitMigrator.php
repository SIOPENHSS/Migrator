<?php

namespace App\Laravel\Migrators;

use App\Domain\Unit\Models\Unit;
use App\Domain\User\Models\User;
use SIOPEN\Migrator\Models\Unit as SIOPENUnit;

class UnitMigrator extends Migrator
{
    /**
     * {@inheritDoc}
     */
    public function handle() : void
    {
        $admin = User::whereEmail('supianidz@gmail.com')->first();

        $migrator = $this->factory([
            'name'       => 'name',
            'symbol'     => 'symbol',
            'created_by' => function() use ($admin) {
                return $admin->getKey();
            },
        ]);

        $migrator->uniques([
            'symbol',
        ]);

        $migrator->migrate(Unit::class, SIOPENUnit::class);

        $this->assertSame(SIOPENUnit::class, Unit::class);
    }
}
