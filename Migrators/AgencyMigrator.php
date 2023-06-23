<?php

namespace App\Laravel\Migrators;

use App\Domain\Agency\Models\Agency;
use App\Domain\Agency\Models\AgencyUser;
use App\Domain\Agency\Models\AgencyUserRole;
use App\Domain\Role\Enums\RoleEnum;
use App\Domain\User\Models\User;
use SIOPEN\Migrator\Models\Agency as SIOPENAgency;
use SIOPEN\Migrator\Models\AgencyUser as SIOPENAgencyUser;
use SIOPEN\Migrator\Models\AgencyUserRole as SIOPENAgencyUserRole;

class AgencyMigrator extends Migrator
{
    public function handle() : void
    {
        SIOPENAgency::on('siopen')->withTrashed()->get()->each(function(SIOPENAgency $agency) {
            $agency->update([
                'slug' => str($agency->getOriginal('name') . '_' . $agency->getOriginal('id'))->slug(),
            ]);
        });

        $migrator = $this->factory([
            'name'    => 'name',
            'alias'   => 'aliases',
            'lkpp_id' => 'lkpp_code',
            'slug'    => 'slug',
            'logo'    => fake()->imageUrl(),
            'address' => 'address',
            'email'   => function(SIOPENAgency $agency) {
                return $agency->contact['email'] ?? null;
            },
            'phone' => function(SIOPENAgency $agency) {
                return $agency->contact['phone'] ?? null;
            },
            'special' => function() {
                return false;
            },
            'parent_id' => function(SIOPENAgency $agency) {
                return $agency->getOriginal('parent_id') ? Agency::where('slug', $agency->parent->getOriginal('slug'))->first()->getKey() : null;
            },
        ]);

        $migrator->uniques([
            'slug',
        ]);

        $migrator->created(function(Agency $agency, SIOPENAgency $origin) {
            $this->handleAgencyUser($agency, $origin);
        });

        $migrator->migrate(Agency::class, SIOPENAgency::class);

        $this
            ->assertSame(SIOPENAgency::class, Agency::class)
            ->assertSame(SIOPENAgencyUser::class, AgencyUser::class)
            ->assertSame(SIOPENAgencyUserRole::class, AgencyUserRole::class);
    }

    public function handleAgencyUserRole(SIOPENAgencyUser $siopenAgencyUser, AgencyUser $agencyUser) : void
    {
        $siopenAgencyUser->roles->each(function(SIOPENAgencyUserRole $siopenAgencyUserRole) use ($agencyUser) {
            $this
                ->factory([
                    'role' => function(SIOPENAgencyUserRole $siopenAgencyUserRole) {
                        return match ($siopenAgencyUserRole->role_name) {
                            'agency-head'        => RoleEnum::AGENCY_HEAD->name,
                            'agency-procurement' => RoleEnum::AGENCY_PROCUREMENT->name,
                            'agency-finance'     => RoleEnum::AGENCY_FINANCE->name,
                        };
                    },
                ])
                ->uniques([
                    'agency_user_id', 'role',
                ])
                ->create($siopenAgencyUserRole, $agencyUser->roles());
        });
    }

    private function handleAgencyUser(Agency $agency, SIOPENAgency $origin) : void
    {
        $agency->refresh()->users->each(function(AgencyUser $agencyUser) {
            $agencyUser->forceDelete();
        });

        $origin->staffspivot->each(function(SIOPENAgencyUser $agencyUser) use ($agency) {
            $migrator = $this->factory([
                'status'  => 'status',
                'user_id' => function(SIOPENAgencyUser $agencyUser) {
                    return User::whereEmail($agencyUser->user->getOriginal('email'))->withTrashed()->first()->getKey();
                },
            ]);

            $migrator->created(function(AgencyUser $agencyUser, SIOPENAgencyUser $siopenAgencyUser) {
                $this->handleAgencyUserRole($siopenAgencyUser, $agencyUser);
            });

            $migrator->create($agencyUser, $agency->users());
        });
    }
}
