<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Database\Eloquent\Model;

class AnnualNotificationLetter extends Model
{
    public const REJECTED = 'REJECTED';

    public const UNREPORTED = 'UNREPORTED';

    public const ACCEPTED = 'ACCEPTED';

    public const UNVERIFIED = 'UNVERIFIED';

    /**
     * @var string
     */
    protected $connection = 'siopen';

    public static function getAllStatus() : array
    {
        return [
            self::UNREPORTED,
            self::UNVERIFIED,
            self::REJECTED,
            self::ACCEPTED,
        ];
    }

    /**
     * @param $status
     * @return string
     */
    public static function getLocalStatus($status) : string
    {
        return match ($status) {
            AnnualNotificationLetter::REJECTED   => 'DITOLAK',
            AnnualNotificationLetter::ACCEPTED   => 'DITERIMA',
            AnnualNotificationLetter::UNVERIFIED => 'SUDAH MELAPORKAN',
            AnnualNotificationLetter::UNREPORTED => 'BELUM MELAPORKAN',
        };
    }
}
