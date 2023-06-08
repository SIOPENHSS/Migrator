<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;

class AgencyUserRole extends Model
{
    /**
     * @var string
     */
    protected $connection = 'siopen';

    /**
     * @var string[]
     */
    protected $fillable = [
        'agency_user_id', 'role_name', 'status',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'status' => 'bool',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];
}
