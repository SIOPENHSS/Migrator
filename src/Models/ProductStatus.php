<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStatus extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    public const WAITING = 'MENUNGGU KONFIRMASI';

    /**
     * @var string
     */
    public const REJECTED = 'DITOLAK';

    /**
     * @var string
     */
    public const VERIFIED = 'TELAH DIVERIFIKASI';

    /**
     * @var string
     */
    protected $connection = 'siopen';

    /**
     * @var array
     */
    protected $fillable = [
        'product_id', 'status', 'reason', 'verified_at', 'verifier_id',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'product_id', 'created_at', 'updated_at', 'deleted_at',
    ];

    /**
     * @var string[]
     */
    protected $appends = [];

    /**
     * @var string[]
     */
    protected $casts = [
        'verified_at' => 'datetime:d-m-Y H:i:s',
        'created_at'  => 'datetime:d-m-Y H:i:s',
        'updated_at'  => 'datetime:d-m-Y H:i:s',
        'deleted_at'  => 'datetime:d-m-Y H:i:s',
    ];

    /**
     * @return BelongsTo
     */
    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
