<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class TaxableMerchant extends Model
{
    use SoftDeletes;

    public const REJECTED = 'REJECTED';

    public const UNREPORTED = 'UNREPORTED';

    public const ACCEPTED = 'ACCEPTED';

    public const UNVERIFIED = 'UNVERIFIED';

    /**
     * @var string
     */
    protected $connection = 'siopen';

    protected $fillable = [
        'merchant_id', 'taxable', 'reported_by', 'document', 'status', 'year', 'document_number',
    ];

    protected $casts = [
        'taxable'    => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * @return string
     */
    public function getDocumentUrlAttribute() : string
    {
        if ($this->taxable) {
            return Storage::disk('has_pkp')->url($this->document ?? '');
        }

        return Storage::disk('non_pkp')->url($this->document ?? '');
    }

    /**
     * @return BelongsTo
     */
    public function reporter() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
