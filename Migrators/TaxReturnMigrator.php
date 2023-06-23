<?php

namespace App\Laravel\Migrators;

use App\Domain\Tax\Models\TaxReturn;
use App\Laravel\Migrators\Utils\Get;
use SIOPEN\Migrator\Models\AnnualNotificationLetter;
use SIOPEN\Migrator\Models\AnnualNotificationLetterList;

class TaxReturnMigrator extends Migrator
{
    public function handle() : void
    {
        $migrator = $this->factory([
            'year'     => 'year',
            'due_date' => 'due_date',
        ]);

        $migrator->uniques([
            'year',
        ]);

        $migrator->created(function (TaxReturn $taxReturn, AnnualNotificationLetter $siopenTaxReturn) {
            $siopenTaxReturn->items->each(function (AnnualNotificationLetterList $list) use ($taxReturn) {
                $migrator = $this->factory([
                    'document'    => 'document',
                    'status'      => 'status',
                    'merchant_id' => function (AnnualNotificationLetterList $row) {
                        return Get::merchant($row->merchant_id)->getKey();
                    },
                    'reported_by' => function (AnnualNotificationLetterList $row) {
                        return Get::user($row->user_id)->getKey();
                    },
                ]);

                $migrator->create($list, $taxReturn->taxes());
            });
        });

        $migrator->migrate(TaxReturn::class, AnnualNotificationLetter::class);
    }
}
