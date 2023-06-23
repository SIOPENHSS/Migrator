<?php

namespace App\Laravel\Migrators;

use App\Domain\Role\Enums\RoleEnum;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\DB;
use SIOPEN\Migrator\Models\User as SIOPENUser;
use SIOPEN\Migrator\Models\UserDetail as SIOPENUserDetail;

class UserMigrator extends Migrator
{
    public function handle() : void
    {
        $migrator = $this->factory([
            'name'     => 'name',
            'email'    => 'email',
            'username' => 'username',
            'password' => 'password',
            'status'   => function(SIOPENUser $user) {
                return $user->getOriginal('status') ? User::STATUS_ACTIVE : User::STATUS_INACTIVE;
            },
            'lpse_id'    => 'lpsed_id',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
            'deleted_at' => 'deleted_at',
        ]);

        $migrator
            ->console($this->command)
            ->uniques([
                'email',
            ])
            ->created(function(User $user, SIOPENUser $origin) {
                $this->handleUserDetail($user, $origin);

                DB::connection('siopen')->table('user_roles')->where('user_id', $origin->getKey())->get()->each(function($row) use ($user) {
                    $name = DB::connection('siopen')->table('roles')->where('id', $row->role_id)->first()->name;

                    $role = match ($name) {
                        'super-admin', 'root' => RoleEnum::SUPER_ADMIN->name,
                        'agency-admin'       => RoleEnum::AGENCY_ADMIN->name,
                        'agency-head'        => RoleEnum::AGENCY_HEAD->name,
                        'agency-finance'     => RoleEnum::AGENCY_FINANCE->name,
                        'agency-procurement' => RoleEnum::AGENCY_PROCUREMENT->name,

                        'merchant-owner' => RoleEnum::MERCHANT_OWNER->name,
                        'merchant-staff', 'merchant-admin' => RoleEnum::MERCHANT_STAFF->name,
                        'auditor'     => RoleEnum::PROCUREMENT_AUDITOR->name,
                        'verificator' => RoleEnum::PROCUREMENT_APPROVER->name,
                    };

                    $user->assign($role);
                });
            })
            ->migrate(User::class, SIOPENUser::class);

        User::whereEmail('supianidz@gmail.com')->first()->assign(RoleEnum::SUPER_ADMIN->name);

        $this->assertSame(SIOPENUser::class, User::class);
    }

    private function handleUserDetail(User $user, SIOPENUser $origin) : void
    {
        $migrator = $this->factory([
            'address'         => 'address',
            'village_id'      => 'village_id',
            'birth_place'     => 'birth_place',
            'date_of_birth'   => 'date_of_birth',
            'employee_number' => 'employee_number',
            'identity_number' => 'identity_number',
            'phone'           => function(SIOPENUserDetail $detail) : string|null {
                return $detail->contact['phone'] ?? null;
            },
            'whatsapp' => function(SIOPENUserDetail $detail) : string|null {
                return $detail->contact['phone'] ?? null;
            },
        ]);

        $migrator->uniques([
            'identity_number',
        ]);

        $migrator->create($origin->detail, $user->detail());
    }
}
