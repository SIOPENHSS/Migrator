<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use JetBrains\PhpStorm\Pure;

class MerchantStatus extends Model
{
    /**
     * @var string
     */
    public const WAITING = 'MENUNGGU KONFIRMASI';

    /**
     * @var string
     */
    public const PROCESSING = 'SEDANG DIPROSES';

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
     * @var string[]
     */
    protected $fillable = [
        'merchant_id', 'status', 'reason', 'verifier_id', 'verified_at',
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
     * @param  string $status
     * @return bool
     */
    public function has(string $status) : bool
    {
        return $this->status === $status;
    }

    /**
     * @return bool
     */
    #[Pure]
    public function hasUnderReview() : bool
    {
        return $this->has(MerchantStatus::PROCESSING);
    }

    /**
     * @return bool
     */
    #[Pure]
    public function hasVerified() : bool
    {
        return $this->has(MerchantStatus::VERIFIED);
    }

    /**
     * @return BelongsTo
     */
    public function merchant() : BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    /**
     * @param  string      $status
     * @param  string|null $reason
     * @return MerchantStatus
     */
    public function set(string $status, string $reason = null) : MerchantStatus
    {
        return match ($status) {
            MerchantStatus::VERIFIED   => $this->accepted(),
            MerchantStatus::PROCESSING => $this->processed(),
            MerchantStatus::REJECTED   => $this->rejected($reason),
        };
    }

    /**
     * @return MerchantStatus
     */
    public function processed() : MerchantStatus
    {
        return (new MerchantStatusRepository($this))->setStatusUnderReview($this->merchant);
    }

    /**
     * @return MerchantStatus
     */
    public function accepted() : MerchantStatus
    {
        return (new MerchantStatusRepository($this))->setStatusAccepted($this->merchant);
    }

    /**
     * @param  string $reason
     * @return MerchantStatus
     */
    public function rejected(string $reason) : MerchantStatus
    {
        return (new MerchantStatusRepository($this))->setStatusRejected($this->merchant, $reason);
    }
}
