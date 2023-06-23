<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnualNotificationLetterList extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'id', 'merchant_id', 'notification_id', 'verified_by', 'user_id', 'status', 'document',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function getLocalStatusAttribute() : string
    {
        return AnnualNotificationLetter::getLocalStatus($this->status);
    }

    /**
     * @return BelongsTo
     */
    public function letter() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AnnualNotificationLetter::class, 'notification_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by')
            ->select([
                'id', 'name',
            ])
            ->withTrashed();
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id')
            ->select([
                'id', 'name',
            ])
            ->withTrashed();
    }
}
