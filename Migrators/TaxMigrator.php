<?php

namespace App\Laravel\Migrators;

use App\Domain\Tax\Models\Tax;
use SIOPEN\Migrator\Models\TaxOption;

class TaxMigrator extends Migrator
{
    /**
     * {@inheritDoc}
     */
    public function handle() : void
    {
        Tax::withTrashed()->get()->each(function(Tax $tax) {
            $tax->forceDelete();
        });

        $migrator = $this->factory([
            'name'        => 'name',
            'pad_rate'    => 'local_tax_rate',
            'ppn_rate'    => 'value_tax_rate',
            'pph_rate'    => 'income_tax_rate',
            'pad_minimum' => 'minimum_local_tax',
            'ppn_minimum' => 'minimum_value_tax',
            'pph_minimum' => 'minimum_value_income_tax',
            'divider'     => 'divider',
            'old_id'      => 'id',
            'deleted_at'  => function(TaxOption $option) {
                return 0 === $option->getOriginal('status') ? $option->getOriginal('updated_at') : null;
            },
        ]);

        $migrator->migrate(Tax::class, TaxOption::class);

        $this->assertSame(TaxOption::class, Tax::class);
    }
}
