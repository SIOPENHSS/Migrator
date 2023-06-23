<?php

namespace App\Laravel\Migrators;

use App\Domain\Merchant\Enums\MerchantStatus;
use App\Domain\Merchant\Models\Merchant;
use App\Domain\Merchant\Models\MerchantDocument;
use App\Domain\Role\Enums\RoleEnum;
use App\Domain\User\Models\User;
use Illuminate\Support\Facades\Cache;
use SIOPEN\Migrator\Models\Merchant as SIOPENMerchant;
use SIOPEN\Migrator\Models\MerchantUser as SIOPENMerchantUser;
use SIOPEN\Migrator\Models\User as SIOPENUser;

class MerchantMigrator extends Migrator
{
    /**
     * {@inheritDoc}
     */
    public function handle() : void
    {
        SIOPENMerchant::withTrashed()->get()->each(function(SIOPENMerchant $merchant) {
            $merchant->update([
                'slug' => str($merchant->name . '_' . $merchant->id)->slug(),
            ]);
        });

        $migrator = $this->factory([
            'name' => 'name',
            'type' => function(SIOPENMerchant $merchant) {
                return match ($merchant->type) {
                    SIOPENMerchant::TYPE_COMPANY  => Merchant::TYPE_BUSINESS,
                    SIOPENMerchant::TYPE_PERSONAL => Merchant::TYPE_PERSONAL,
                };
            },
            'email' => function(SIOPENMerchant $merchant) {
                return $merchant->contact['email'] ?? null;
            },
            'phone' => function(SIOPENMerchant $merchant) {
                return $merchant->contact['phone'] ?? null;
            },
            'owner_id' => function(SIOPENMerchant $merchant) {
                $email = Cache::sear('OwnerID:' . $merchant->getKey(), function() use ($merchant) {
                    $siopenMerchantUser = SIOPENMerchantUser::on('siopen')->where('merchant_id', $merchant->getKey())->withTrashed()->first();

                    if (! $siopenMerchantUser) {
                        return null;
                    }

                    return SIOPENUser::on('siopen')->withTrashed()->find($siopenMerchantUser->user_id)->email;
                });

                if (! $email) {
                    return null;
                }

                $owner = User::withTrashed()->where('email', $email)->first() ?? null;

                $owner?->assign(RoleEnum::MERCHANT_OWNER->name);

                return $owner->getKey();
            },
            'slug'       => 'slug',
            'address'    => 'address',
            'village_id' => 'village_id',
            'status'     => function(SIOPENMerchant $merchant) {
                return match ($merchant->status->status) {
                    'DITOLAK'             => MerchantStatus::DECLINED->name,
                    'TELAH DIVERIFIKASI'  => MerchantStatus::APPROVED->name,
                    'MENUNGGU KONFIRMASI' => MerchantStatus::PROCESS->name,
                };
            },
            'approved_at' => function(SIOPENMerchant $merchant) {
                return match ($merchant->status->status) {
                    'TELAH DIVERIFIKASI' => $merchant->status->updated_at,
                    default              => null,
                };
            },
            'logo' => function(SIOPENMerchant $merchant) {
                return $merchant->logo->file_name ?? '-';
            },
        ]);

        $migrator
            ->uniques([
                'slug',
            ])
            ->created(function(Merchant $merchant, SIOPENMerchant $siopenMerchant) {
                $this->handleMerchantDocuments($merchant, $siopenMerchant);
            })
            ->migrate(Merchant::class, SIOPENMerchant::class);

        $this->assertSame(SIOPENMerchant::class, Merchant::class);
    }

    public function handleMerchantDocuments(Merchant $merchant, SIOPENMerchant $siopenMerchant) : void
    {
        if ($siopenMerchant->ktp) {
            $merchant->documents()->firstOrCreate([
                'type'     => MerchantDocument::TYPE_IDENTITY_CARD,
                'number'   => $siopenMerchant->identity_number,
                'document' => $siopenMerchant->ktp->file_name,
            ]);
        }

        if ($siopenMerchant->npwp) {
            $merchant->documents()->firstOrCreate([
                'type'     => MerchantDocument::TYPE_TAXPAYER_CARD,
                'number'   => $siopenMerchant->taxpayer_number,
                'document' => $siopenMerchant->npwp->file_name,
            ]);
        }

        if ($siopenMerchant->business_number && $siopenMerchant->nib) {
            $merchant->documents()->firstOrCreate([
                'type'     => MerchantDocument::TYPE_BUSINESS_CARD,
                'number'   => $siopenMerchant->business_number,
                'document' => $siopenMerchant->nib->file_name,
            ]);
        }
    }
}
