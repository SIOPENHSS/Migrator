<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPriceBargain extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $connection = 'old_siopen';

    /**
     * @var string[]
     */
    protected $fillable = [
        'price', 'order_item_id', 'offered_by',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'price' => 'float',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'order_item_id', 'offered_by', 'created_at', 'updated_at',
    ];

    /**
     * @return BelongsTo
     */
    public function item() : BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    /**
     * @return BelongsTo
     */
    public function offered() : BelongsTo
    {
        return $this->belongsTo(MerchantUser::class, 'offered_by');
    }
}
