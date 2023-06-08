<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static create($data)
 * @method static insert(array $toArray)
 */
class Unit extends Model
{
    use SoftDeletes;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name', 'symbol', 'parent_id',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'parent_id',
    ];

    /**
     * @return HasMany
     */
    public function child() : HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_id');
    }
}
