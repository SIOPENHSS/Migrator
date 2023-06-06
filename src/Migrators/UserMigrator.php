<?php

namespace SIOPEN\Migrator\Migrators;

use SIOPEN\Migrator\Factory;
use SIOPEN\Migrator\Models\User;

class UserMigrator extends Factory
{
    /**
     * @var string
     */
    protected string $origin = User::class;
}
