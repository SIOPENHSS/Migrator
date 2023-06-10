<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxCategory extends Model
{/**
     * @var string
     */
    protected $connection = 'siopen';
    /**
     * @var string[]
     */
    protected $fillable = [
        'value_tax_rate', 'income_tax_rate', 'local_tax_rate', 'category_id', 'has_value_tax', 'has_local_tax', 'has_income_tax', 'tax_option_id'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'has_value_tax' => 'bool',
        'has_local_tax' => 'bool',
        'has_income_tax' => 'bool',
        'created_at'    => 'datetime:d-m-Y H:i:s',
        'updated_at'    => 'datetime:d-m-Y H:i:s',
        'deleted_at'    => 'datetime:d-m-Y H:i:s',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'category_id', 'created_at', 'deleted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function tax() : BelongsTo
    {
        return $this->belongsTo(TaxOption::class, 'tax_option_id');
    }

    /**
     * @return BelongsTo
     */
    public function option() : BelongsTo
    {
        return $this->belongsTo(TaxOption::class, 'tax_option_id');
    }
}
