<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use  SoftDeletes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name', 'email', 'password', 'status', 'lkpp_token', 'username', 'demo', 'lpse_id', 'lkpp_role', 'login_from_tokodaring',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'status'     => 'bool',
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
        'deleted_at' => 'datetime:d-m-Y H:i:s',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'password', 'laravel_through_key',
    ];

    /**
     * @return HasOne
     */
    public function detail() : HasOne
    {
        return $this->hasOne(UserDetail::class, 'user_id');
    }
}
