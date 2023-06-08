<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static updateOrCreate(array $array, array $array1)
 * @mixin IdeHelperMerchantUser
 */
class MerchantUser extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id', 'merchant_id', 'status', 'last_online',
    ];
}
