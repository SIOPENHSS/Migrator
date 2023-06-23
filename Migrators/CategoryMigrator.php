<?php

namespace App\Laravel\Migrators;

use App\Domain\Category\Models\Category;
use App\Domain\Tax\Models\Tax;
use PHPUnit\Framework\Assert;
use SIOPEN\Migrator\Models\Category as SIOPENCategory;
use SIOPEN\Migrator\Models\TaxCategory;

class CategoryMigrator extends Migrator
{
    /**
     * {@inheritDoc}
     */
    public function handle() : void
    {
        $migrator = $this->factory([
            'name'   => 'name',
            'slug'   => 'slug',
            'status' => 'status',
            'lkpp'   => 'vertical_id',
            'type'   => function(SIOPENCategory $category) {
                return match ($category->getOriginal('parent_id')) {
                    3       => Category::TYPE_GOODS,
                    1       => Category::TYPE_SERVICE,
                    default => Category::TYPE_SERVICE
                };
            },
        ]);

        $migrator
            ->uniques([
                'slug',
            ])
            ->created(function(Category $category, SIOPENCategory $siopenCategory) {
                $this->handleCategoryTaxes($category, $siopenCategory);
            });

        $migrator->migrate(Category::class, [
            SIOPENCategory::class => function($query) {
                return $query->whereNotNull('parent_id');
            },
        ]);

        Assert::assertSame(SIOPENCategory::whereNotNull('parent_id')->withTrashed()->count(), Category::count());
    }

    public function handleCategoryTaxes(Category $category, SIOPENCategory $siopenCategory) : void
    {
        $siopenCategory->taxes->each(function(TaxCategory $taxCategory) use ($category) {
            $migrator = $this->factory([
                'old_id' => 'id',
                'tax_id' => function(TaxCategory $taxCategory) {
                    return Tax::whereOldId($taxCategory->tax_option_id)->withTrashed()->first()->getKey();
                },
                'has_pad'    => 'has_local_tax',
                'has_ppn'    => 'has_value_tax',
                'has_pph'    => 'has_income_tax',
                'deleted_at' => function(TaxCategory $taxCategory) {
                    return 0 === $taxCategory->getOriginal('status') ? ($taxCategory->getOriginal('deleted_at') ?? $taxCategory->getOriginal('updated_at')) : null;
                },
            ]);

            $migrator->create($taxCategory, $category->tax());
        });
    }
}
