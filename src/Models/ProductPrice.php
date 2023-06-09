<?php

namespace SIOPEN\Migrator\Models;

use SIOPEN\Migrator\Models\Traits\DateTimeForIndonesian;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use JetBrains\PhpStorm\Pure;

/**
 * @method static ProductPrice create(array $array)
 * @method static whereProductId(mixed $id)
 * @mixin IdeHelperProductPrice
 */
class ProductPrice extends Model
{/**
     * @var string
     */
    protected $connection = 'siopen';
    /**
     * @var string[]
     */
    protected $fillable = [
        'price', 'product_id', 'status',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'formatted', 'un_formatted',
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
        'deleted_at',
    ];

    /**
     * @return string
     */
    public function getFormattedAttribute() : string
    {
        return currency($this->price);
    }

    /**
     * @return string
     */
    #[Pure]
    public function getUnFormattedAttribute() : string
    {
        return currency_no_suffix($this->price);
    }

    /**
     * @return int
     */
    #[Pure]
    public function getPriceWithTaxAttribute() : int
    {
        return $this->price + $this->getTotalTaxAttribute();
    }

    /**
     * @return int
     */
    #[Pure]
    public function getTotalTaxAttribute() : int
    {
        return $this->getLocalTaxAttribute() + $this->getValueTaxAttribute() + $this->getIncomeTaxAttribute();
    }

    /**
     * @return float
     */
    public function getLocalTaxAttribute() : float
    {
        $categoryTax = $this->product->category->tax;

        $localTax = 0;
        if ($categoryTax->has_local_tax) {
            $localTax = $this->price * $categoryTax->tax->local_tax_rate / 100;
        }

        return $localTax;
    }

    /**
     * @return float
     */
    public function getValueTaxAttribute() : float
    {
        $categoryTax = $this->product->category->tax;

        $valueTax = 0;
        if ($categoryTax->has_value_tax) {
            $valueTax = $this->price * $categoryTax->tax->value_tax_rate / 100;
        }

        return $valueTax;
    }

    /**
     * @return float
     */
    public function getIncomeTaxAttribute() : float
    {
        $categoryTax = $this->product->category->tax;

        $valueTax = 0;
        if ($categoryTax->has_income_tax) {
            $valueTax = $this->price * $categoryTax->tax->income_tax_rate / 100;
        }

        return $valueTax;
    }

    /**
     * @return BelongsTo
     */
    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id')->select([
            'id', 'title', 'category_id', 'merchant_id',
        ]);
    }
}
