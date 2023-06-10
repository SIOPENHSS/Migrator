<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    /**
     * @var string
     */
    protected $connection = 'siopen';

    /**
     * @var string[]
     */
    protected $fillable = [
        'activity', 'sub_activity', 'account_code', 'evidence_number', 'recorded_date', 'year', 'agency_id', 'necessities',
    ];

    protected $casts = [
        'recorded_date' => 'date:d M Y',
    ];
}
