<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use  SoftDeletes;

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
    public const ON_DELIVERY = 'SEDANG DIANTARKAN';

    /**
     * @var string
     */
    public const RECEIVED = 'BARANG SUDAH DITERIMA';

    /**
     * @var string
     */
    public const PAID = 'SUDAH DIBAYAR';

    /**
     * @var string
     */
    public const REJECTED = 'DITOLAK';

    /**
     * @var string
     */
    public const CANCELED = 'DIBATALKAN';

    /**
     * @var string
     */
    public const COMPLETED = 'SELESAI';

    /**
     * @var string
     */
    protected $connection = 'siopen';

    /**
     * @var string[]
     */
    protected $fillable = [
        'reason', 'invoice_number', 'status', 'merchant_id', 'costumer_id', 'note', 'activity_id', 'handover_at', 'head_id', 'finance_id', 'procurement_id', 'pkp', 'from_toko_daring', 'transaction_token',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'merchant_id', 'costumer_id', 'activity_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime:d M Y H:i',
        'updated_at' => 'datetime:d M Y H:i',
    ];

    /**
     * @return BelongsTo
     */
    public function taxable() : BelongsTo
    {
        return $this->belongsTo(TaxableMerchant::class, 'pkp', 'id');
    }

    /**
     * @return HasOne
     */
    public function delivery() : HasOne
    {
        return $this->hasOne(OrderDelivery::class, 'order_id');
    }

    /**
     * @return BelongsTo
     */
    public function activity() : BelongsTo
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    /**
     * @return BelongsTo
     */
    public function merchant() : BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    /**
     * @return BelongsTo
     */
    public function agencyUser() : BelongsTo
    {
        return $this->belongsTo(AgencyUser::class, 'costumer_id');
    }

    /**
     * @return HasOneThrough|Builder
     */
    public function costumer() : Builder|HasOneThrough
    {
        return $this
            ->hasOneThrough(User::class, AgencyUser::class, 'id', 'id', 'costumer_id', 'user_id')
            ->withTrashedParents()
            ->latest('id');
    }

    /**
     * @return HasOneThrough
     */
    public function agency() : HasOneThrough
    {
        return $this
            ->hasOneThrough(Agency::class, AgencyUser::class, 'id', 'id', 'costumer_id', 'agency_id')
            ->latest('agency_users.id');
    }

    /**
     * @return HasMany
     */
    public function items() : HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function head() : BelongsTo
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    public function finance() : BelongsTo
    {
        return $this->belongsTo(User::class, 'finance_id');
    }

    public function procurement() : BelongsTo
    {
        return $this->belongsTo(User::class, 'procurement_id');
    }

    /**
     * @return HasMany
     */
    public function chats() : HasMany
    {
        return $this->hasMany(OrderChatBargain::class, 'order_id');
