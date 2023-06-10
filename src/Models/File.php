<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use  SoftDeletes;

    /**
     * @var string
     */
    protected $connection = 'old_siopen';

    /**
     * @var string[]
     */
    protected $fillable = [
        'type', 'disk', 'parent_id', 'parent_model', 'file_name', 'real_name', 'deleted_at',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'url',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'parent_id', 'parent_model', 'parent', 'created_at', 'updated_at', 'deleted_at',
    ];

    /**
     * @return string
     */
    public function getUrlAttribute() : string
    {
        $url = Storage::disk($this->disk)->url($this->file_name);

        if ($this->parent instanceof Product) {
            return str_replace('[merchant]', md5($this->parent->merchant->id), $url);
        }

        if (in_array($this->type, ['LOGO', 'NPWP', 'NIB', 'KTP'])) {
            return str_replace('[merchant]', md5($this->parent->id), $url);
        }

        return $url;
    }

    /**
     * @return BelongsTo
     */
    public function parent() : BelongsTo
    {
        if (str_contains($this->parent_model, 'Product')) {
            $selected = Product::class;
        }

        return $this->belongsTo($selected, 'parent_id');
    }

    /**
     * @param  Builder $query
     * @return Builder
     */
    public function scopeProduct(Builder $query) : Builder
    {
        return $query->where('parent_model', Product::class)->where('disk', 'product');
    }
}
