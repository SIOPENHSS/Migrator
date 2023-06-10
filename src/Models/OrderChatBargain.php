<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderChatBargain extends Model
{/**
     * @var string
     */
    protected $connection = 'old_siopen';
    /**
     * @var string[]
     */
    protected $fillable = [
        'order_id', 'user_id', 'content',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * @return BelongsTo
     */
    public function sender() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
