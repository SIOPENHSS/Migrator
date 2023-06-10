<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDelivery extends Model
{
    /**
     * @var string
     */
    public const PICKED_ORDER = 'PICKED';

    /**
     * @var string
     */
    public const DELIVERY_ORDER = 'DELIVERY';

    /**
     * @var string
     */
    protected $connection = 'old_siopen';

    /**
     * @var array
     */
    protected $fillable = [
        'order_id', 'address', 'fee', 'type', 'village_id', 'date_received', 'till_date',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'fee_formatted',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'fee' => 'float',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'order_id', 'village_id',
    ];
}
