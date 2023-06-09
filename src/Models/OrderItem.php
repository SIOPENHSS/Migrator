<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    /**
     * @var string
     */
    protected $connection = 'siopen';

    /**
     * @var string[]
     */
    protected $fillable = [
        'quantity', 'note', 'sub_total', 'product_id', 'price_id', 'order_id', 'tax_category_id',
    ];

    /**
     * @return BelongsTo
     */
    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * @return HasOne
     */
    public function bargain() : HasOne
    {
        return $this->hasOne(OrderPriceBargain::class, 'order_item_id')->whereNull('deleted_at');
    }

    /**
     * @return BelongsTo
     */
    public function price() : BelongsTo
    {
        return $this->belongsTo(ProductPrice::class, 'price_id');
    }

    /**
     * @return BelongsTo
     */
    public function taxCategory() : BelongsTo
    {
        return $this->belongsTo(TaxCategory::class, 'tax_category_id');
    }

    /**
     * @return BelongsTo
     */
    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id')->withTrashed();
    }
}
