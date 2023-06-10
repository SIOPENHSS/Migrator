<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    /**
     * @var string
     */
    protected $connection = 'old_siopen';

    /**
     * @var string[]
     */
    protected $casts = [
        'contact' => 'json',
    ];
}
