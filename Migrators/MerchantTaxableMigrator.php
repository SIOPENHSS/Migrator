<?php

namespace App\Laravel\Migrators;

use App\Domain\Merchant\Models\MerchantTaxable;
use App\Laravel\Migrators\Utils\Get;
use SIOPEN\Migrator\Models\TaxableMerchant;

class MerchantTaxableMigrator extends Migrator
{
    /**
     * {@inheritDoc}
     */
    public function handle() : void
    {
        $migrator = $this->factory([
            'old_id'      => 'id',
            'merchant_id' => function(TaxableMerchant $taxableMerchant) {
                return Get::merchant($taxableMerchant->merchant_id)->getKey();
            },
            'taxable'     => 'taxable',
            'document'    => 'document',
            'status'      => 'status',
            'number'      => 'document_number',
            'year'        => 'year',
            'reported_by' => function(TaxableMerchant $taxableMerchant) {
                return Get::user($taxableMerchant->reported_by)->getKey();
            },
        ]);

        //        $migrator->uniques([
        //            'merchant_id', 'year',
        //        ]);

        $migrator->migrate(MerchantTaxable::class, TaxableMerchant::class);

        $this->assertSame(TaxableMerchant::class, MerchantTaxable::class);
    }
}
