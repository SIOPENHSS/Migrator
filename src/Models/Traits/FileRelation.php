<?php

namespace SIOPEN\Migrator\Models\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use SIOPEN\Migrator\Models\File;

trait FileRelation
{
    /**
     * @return HasOne
     */
    public function file() : HasOne
    {
        return $this->hasOne(File::class, 'parent_id')->where('parent_model', 'LIKE', '%' . str(get_class($this))->afterLast('\\') . '%');
    }

    /**
     * @return HasMany
     */
    public function files() : HasMany
    {
        return $this->hasMany(File::class, 'parent_id')->where('parent_model', 'LIKE', '%' . str(get_class($this))->afterLast('\\') . '%');
    }
}
