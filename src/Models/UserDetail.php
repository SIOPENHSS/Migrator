<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    /**
     * @var string[]
     */
    protected $casts = [
        'contact' => 'json',
    ];
}
