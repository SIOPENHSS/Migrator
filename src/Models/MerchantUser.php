<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantUser extends Model
{
    use SoftDeletes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id', 'merchant_id', 'status', 'last_online',
    ];
}
