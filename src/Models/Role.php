<?php

namespace SIOPEN\Migrator\Models;

use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionClassConstant;

class Role extends \Laratrust\Models\Role
{
    /**
     * @var string
     */
    public const ROOT = 'root';

    public const ADMIN_VERIFICATOR = 'verificator';

    /**
     * @var string
     */
    public const SUPER_ADMIN = 'super-admin';

    /**
     * @var string
     */
    public const AUDITOR = 'auditor';

    /**
     * @var string
     */
    public const AGENCY_ADMIN = 'agency-admin';

    /**
     * @var string
     */
    public const AGENCY_HEAD = 'agency-head';

    /**
     * @var string
     */
    public const AGENCY_FINANCE = 'agency-finance';

    /**
     * @var string
     */
    public const AGENCY_PROCUREMENT = 'agency-procurement';

    /**
     * @var string
     */
    public const MERCHANT_OWNER = 'merchant-owner';

    /**
     * @var string
     */
    public const MERCHANT_ADMIN = 'merchant-admin';

    /**
     * @var string
     */
    public const MERCHANT_STAFF = 'merchant-staff';

    /**
     * @var array
     */
    public $guarded = [];

    /**
     * @var string[]
     */
    protected $appends = [
        'display_name',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
        'deleted_at' => 'datetime:d-m-Y H:i:s',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'pivot',
    ];

    /**
     * @return string
     */
    public function getDisplayNameAttribute() : string
    {
        return match ($this->name) {
            static::SUPER_ADMIN        => 'SUPER ADMIN',
            static::AGENCY_HEAD        => 'KADIS',
            static::AGENCY_ADMIN       => 'ADMIN',
            static::AGENCY_FINANCE     => 'BENDAHARA',
            static::AGENCY_PROCUREMENT => 'PEJABAT PK',
            static::MERCHANT_OWNER     => 'PEMILIK TOKO',
            static::MERCHANT_ADMIN     => 'ADMIN TOKO',
            static::MERCHANT_STAFF     => 'STAFF TOKO',
            static::ROOT               => 'ROOT',
            static::AUDITOR            => 'AUDITOR',
            static::ADMIN_VERIFICATOR  => 'VERIFIKATOR',
        };
    }

    /**
     * @return string[]
     */
    public static function merchant() : array
    {
        return [
            static::MERCHANT_OWNER,
            static::MERCHANT_ADMIN,
            static::MERCHANT_STAFF,
        ];
    }

    /**
     * @return string[]
     */
    public static function agency() : array
    {
        return [
            static::AGENCY_HEAD,
            static::AGENCY_ADMIN,
            static::AGENCY_FINANCE,
            static::AGENCY_PROCUREMENT,
        ];
    }

    /**
     * @return Collection
     */
    public static function getRolesFromConst() : Collection
    {
        $reflection = new ReflectionClass(Role::class);

        return collect($reflection->getConstants(ReflectionClassConstant::IS_PUBLIC))
            ->filter(function ($value, $key) {
                return ! in_array($key, ['CREATED_AT', 'UPDATED_AT', 'DELETED_AT']);
            });
    }

    /**
     * @return string
     */
    public static function getRandomRole() : string
    {
        return static::getRolesFromConst()->values()->random(1)->first();
    }
}
