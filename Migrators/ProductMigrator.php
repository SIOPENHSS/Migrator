<?php

namespace App\Laravel\Migrators;

use App\Domain\Product\Models\Product;
use App\Laravel\Migrators\Utils\Get;
use SIOPEN\Migrator\Models\File;
use SIOPEN\Migrator\Models\Product as SIOPENProduct;
use SIOPEN\Migrator\Models\ProductPrice as SIOPENProductPrice;

class ProductMigrator extends Migrator
{
    /**
     * {@inheritDoc}
     */
    public function handle() : void
    {
        $migrator = $this->factory([
            'code'        => 'code',
            'name'        => 'title',
            'slug'        => 'slug',
            'description' => 'description',
            'minimum'     => 'minimum_order',
            'stock'       => 'stock',
            'show'        => 'display_status',
            'unit_id'     => function(SIOPENProduct $product) {
                return Get::unit($product->unit_id)->getKey();
            },
            'merchant_id' => function(SIOPENProduct $product) {
                return Get::merchant($product->merchant_id)->getKey();
            },
            'category_id' => function(SIOPENProduct $product) {
                return Get::category($product->category_id)->getKey();
            },
            'approval' => function(SIOPENProduct $product) {
                if (! $product->status) {
                    return Product::STATUS_APPROVAL_PENDING;
                }

                return match ($product->status->status) {
                    'TELAH DIVERIFIKASI'  => Product::STATUS_APPROVAL_APPROVED,
                    'MENUNGGU KONFIRMASI' => Product::STATUS_APPROVAL_PENDING,
                    'DITOLAK'             => Product::STATUS_APPROVAL_REJECTED,
                };
            },
            'created_by' => function(SIOPENProduct $product) {
                return Get::merchant($product->merchant_id)->owner->getKey();
            },
        ]);

        $migrator
            ->created(function(Product $product, SIOPENProduct $siopenProduct) {
                $this->handleProductPrice($product, $siopenProduct)->handleProductImages($product, $siopenProduct);
            })
            ->uniques([
                'code',
            ])
            ->migrate(Product::class, SIOPENProduct::class);

        $this->assertSame(SIOPENProduct::class, Product::class);
    }

    public function handleProductPrice(Product $product, SIOPENProduct $siopenProduct) : self
    {
        $siopenProduct->prices->each(function(SIOPENProductPrice $siopenProductPrice) use ($product) {
            $migrator = $this->factory([
                'price'  => 'price',
                'status' => 'status',
                'old_id' => function(SIOPENProductPrice $siopenProductPrice) {
                    return $siopenProductPrice->getKey();
                },
                'created_by' => function() use ($product) {
                    return $product->refresh()->merchant->owner->getKey();
                },
            ]);

            $migrator->create($siopenProductPrice, $product->price());
        });

        return $this;
    }

    public function handleProductImages(Product $product, SIOPENProduct $siopenProduct) : void
    {
        $siopenProduct->images->each(function(File $siopenProductImage) use ($product) {
            $product->images()->create([
                'image' => $siopenProductImage->file_name,
            ]);
        });
    }
}
