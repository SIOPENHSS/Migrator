<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TaxOption extends Model
{
    /**
     * @var string
     */
    protected $connection = 'siopen';

    /**
     * @var string[]
     */
    protected $fillable = [
        'income_tax_rate', 'value_tax_rate', 'local_tax_rate', 'minimum_local_tax', 'minimum_value_tax', 'minimum_value_income_tax', 'tax_option_id', 'name', 'divider',
    ];

    /**
     * @param  Builder $query
     * @return Builder
     */
    public function scopeIsActive(Builder $query) : Builder
    {
        return $query->where('status', true);
    }
}
