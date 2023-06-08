<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperMerchantAccount
 */
class MerchantAccount extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'account_owner_name', 'account_number', 'account_name', 'merchant_id', 'status',
    ];

    /**
     * @return BelongsTo
     */
    public function merchant() : BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
