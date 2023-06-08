<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgencyUser extends Model
{
    use  SoftDeletes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id', 'agency_id', 'status',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'status' => 'bool',
    ];

    /**
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function agency() : BelongsTo
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    /**
     * @return HasMany
     */
    public function roles() : HasMany
    {
        return $this->hasMany(AgencyUserRole::class, 'agency_user_id');
    }
}
