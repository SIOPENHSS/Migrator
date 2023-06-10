<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static create($data)
 * @method static insert(array $toArray)
 */
class Unit extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $connection = 'old_siopen';

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
