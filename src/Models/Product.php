<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    const WAITING = 'MENUNGGU KONFIRMASI';

    const REJECTED = 'DITOLAK';

    const VERIFIED = 'TELAH DIVERIFIKASI';

    /**
     * @var string
     */
    protected $connection = 'old_siopen';

    /**
     * @var array
     */
    protected $fillable = [
        'code', 'merchant_id', 'title', 'slug', 'stock', 'minimum_order', 'unit_id', 'description', 'display_status', 'category_id', 'tkdn',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
        'deleted_at' => 'datetime:d-m-Y H:i:s',
    ];

    /**
     * @return BelongsTo
     */
    public function tax() : BelongsTo
    {
        return $this->belongsTo(TaxCategory::class, 'category_id', 'category_id')->where('status', true);
    }

    /**
     * @return BelongsTo
     */
    public function unit() : BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function status() : HasOne
    {
        return $this->hasOne(ProductStatus::class, 'product_id')->latest('product_statuses.id');
    }

    /**
     * @return HasMany
     */
    public function statuses() : HasMany
    {
        return $this->hasMany(ProductStatus::class, 'product_id');
    }

    /**
     * @return HasOne
     */
    public function statusHelper() : HasOne
    {
        return $this->hasOne(ProductStatus::class, 'product_id');
    }

    /**
     * @return BelongsTo
     */
    public function merchant() : BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    /**
     * @return HasOne
     */
    public function price() : HasOne
    {
        return $this->hasOne(ProductPrice::class, 'product_id')
            ->where([
                'product_prices.status' => true,
            ]);
    }

    /**
     * @return HasMany
     */
    public function prices() : HasMany
    {
        return $this->hasMany(ProductPrice::class, 'product_id');
    }

    /**
     * @return BelongsTo
     */
    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * @return HasMany
     */
    public function images() : HasMany
    {
        return $this->hasMany(File::class, 'parent_id')
            ->where('parent_model', 'LIKE', '%Product%',)
            ->limit(4)
            ->latest('id')
            ->whereNull('deleted_at');
    }

    /**
     * @return HasOne
     */
    public function cover() : HasOne
    {
        return $this->hasOne(File::class, 'parent_id')
            ->where([
                'parent_model' => Product::class,
            ])
            ->whereNull('deleted_at');
    }
}
