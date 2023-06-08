<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    public const SERVICE_COMMODITY = 'KOMODITAS JASA';

    /**
     * @var string
     */
    public const PRODUCT_COMMODITY = 'KOMODITAS BARANG';

    /**
     * @var string
     */
    public const CATERING = 'Katering';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name', 'parent_id', 'status', 'featured',
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
        'created_at', 'updated_at', 'deleted_at',
    ];

    /**
     * @return HasOne
     */
//    public function tax() : HasOne
//    {
//        return $this->hasOne(TaxCategory::class, 'category_id')->where('status', true);
//    }

    /**
     * @return HasMany
     */
    public function child() : HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * @return BelongsTo
     */
    public function parent() : BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * @return HasMany
     */
//    public function products() : HasMany
//    {
//        return $this->hasMany(Product::class, 'category_id');
//    }
}
