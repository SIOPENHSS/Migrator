<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use SIOPEN\Migrator\Models\Traits\FileRelation;

class Merchant extends Model
{
    use SoftDeletes, FileRelation;

    protected $connection = 'siopen';

    /**
     * @var string
     */
    public const TYPE_COMPANY = 'BADAN USAHA';

    /**
     * @var string
     */
    public const TYPE_PERSONAL = 'PERORANGAN';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name', 'type', 'contact', 'identity_number', 'taxpayer_number', 'business_number', 'address', 'village_id', 'slug', 'show_status',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'contact'    => 'json',
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
        'deleted_at' => 'datetime:d-m-Y H:i:s',
    ];

    /**
     * @return HasOne
     */
    public function logo() : HasOne
    {
        return $this->file()->where('type', 'LOGO');
    }

    /**
     * @return HasOne
     */
    public function npwp() : HasOne
    {
        return $this->file()->where('type', 'NPWP');
    }

    /**
     * @return HasOne
     */
    public function ktp() : HasOne
    {
        return $this->file()->where('type', 'KTP');
    }

    /**
     * @return HasMany
     */
    public function documents() : HasMany
    {
        return $this->files()->where(function ($query) {
            $query
                ->orWhere('type', 'KTP')
                ->orWhere('type', 'NPWP')
                ->orWhere('type', 'SIßUP')
                ->orWhere('type', 'TDP');
        });
    }

    /**
     * @return HasOne
     */
    public function account() : HasOne
    {
        return $this->hasOne(MerchantAccount::class, 'merchant_id');
    }

    /**
     * @return HasMany
     */
    public function accounts() : HasMany
    {
        return $this->hasMany(MerchantAccount::class, 'merchant_id');
    }

    /**
     * @return HasManyThrough
     */
    public function staff() : HasManyThrough
    {
        return $this->hasManyThrough(User::class, MerchantUser::class, 'merchant_id', 'id', 'id', 'user_id');
    }

    /**
     * @return HasOneThrough
     */
    public function owner() : HasOneThrough
    {
        return $this
            ->hasOneThrough(User::class, MerchantUser::class, 'merchant_id', 'id', 'id', 'user_id')
            ->whereHas('roles', function ($query) {
                return $query->where('name', Role::MERCHANT_OWNER);
            });
    }

    /**
     * @return HasOne
     */
    public function status() : HasOne
    {
        return $this->hasOne(MerchantStatus::class, 'merchant_id');
    }

    /**
     * @return HasMany
     */
    public function products() : HasMany
    {
        return $this->hasMany(Product::class, 'merchant_id');
    }

    /**
     * @return BelongsTo
     */
    public function village() : BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id');
    }

    /**
     * @return HasMany
     */
    public function orders() : HasMany
    {
        return $this->hasMany(Order::class, 'merchant_id');
    }

    /**
     * @return HasOne
     */
    public function annualNotificationLetter() : HasOne
    {
        return $this->hasOne(AnnualNotificationLetterList::class, 'merchant_id');
    }

    /**
     * @return HasOne
     */
    public function taxable() : HasOne
    {
        return $this->hasOne(TaxableMerchant::class, 'merchant_id', 'id')->where('year', Carbon::now()->year - 1);
    }
}
