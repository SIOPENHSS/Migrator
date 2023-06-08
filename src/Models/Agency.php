<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed $staffs
 * @property mixed $parent
 * @property mixed $staffspivot
 */
class Agency extends Model
{
    use SoftDeletes;

    const TYPE_AGENCY = 'agency';

    const TYPE_DISTRICT = 'district';

    const TYPE_VILLAGE = 'village';

    const TYPE_HOSPITAL = 'hospital';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name', 'address', 'contact', 'aliases', 'slug', 'ekinerja_id', 'village_id', 'type', 'lkpp_code', 'parent_id',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'short_name',
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
     * @var string[]
     */
    protected $hidden = [
        'laravel_through_key', 'ekinerja_id',
    ];

    /**
     * @return BelongsTo
     */
    public function parent() : BelongsTo
    {
        return $this->belongsTo(Agency::class, 'parent_id')->withTrashed();
    }

    /**
     * @return HasManyThrough
     */
    public function staffs() : HasManyThrough
    {
        return $this->hasManyThrough(User::class, AgencyUser::class, 'agency_id', 'id', 'id', 'user_id');
    }

    /**
     * @return HasMany
     */
    public function staffspivot() : HasMany
    {
        return $this->hasMany(AgencyUser::class);
    }
}
