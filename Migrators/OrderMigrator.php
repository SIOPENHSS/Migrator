<?php

namespace App\Laravel\Migrators;

use App\Domain\Category\Models\CategoryTax;
use App\Domain\Merchant\Models\MerchantTaxable;
use App\Domain\Order\Enums\OrderStatus;
use App\Domain\Order\Models\Order;
use App\Domain\Order\Models\OrderBargain;
use App\Domain\Order\Models\OrderItem;
use App\Laravel\Migrators\Utils\Get;
use Illuminate\Support\Facades\Cache;
use SIOPEN\Migrator\Models\AgencyUser;
use SIOPEN\Migrator\Models\Order as SIOPENOrder;
use SIOPEN\Migrator\Models\OrderChatBargain;
use SIOPEN\Migrator\Models\OrderDelivery;
use SIOPEN\Migrator\Models\OrderItem as SIOPENOrderItem;

class OrderMigrator extends Migrator
{
    /**
     * {@inheritDoc}
     */
    public function handle() : void
    {
        $migrator = $this->factory([
            'invoice'     => 'invoice_number',
            'amount'      => 0,
            'toko_daring' => 'from_toko_daring',
            'token'       => 'transaction_token',

            'taxable_id' => function(SIOPENOrder $siopenOrder) {
                if (! $siopenOrder->pkp) {
                    return null;
                }

                return MerchantTaxable::whereOldId($siopenOrder->pkp)->firstOrFail()->getKey();
            },
            'status' => function(SIOPENOrder $siopenOrder) {
                return match ($siopenOrder->getOriginal('status')) {
                    'MENUNGGU KONFIRMASI'   => OrderStatus::WAITING->name,
                    'SEDANG DIPROSES'       => OrderStatus::PROCESSING->name,
                    'SEDANG DIANTARKAN'     => OrderStatus::SHIPPED->name,
                    'BARANG SUDAH DITERIMA' => OrderStatus::RECEIVED->name,
                    'SUDAH DIBAYAR'         => OrderStatus::SETTLED->name,
                    'SELESAI'               => OrderStatus::COMPLETED->name,
                    'DIBATALKAN'            => OrderStatus::CANCELED->name,
                    default                 => $siopenOrder->getOriginal('status'),
                };
            },
            'ordered_by' => function(SIOPENOrder $siopenOrder) {
                return Get::user(AgencyUser::find($siopenOrder->costumer_id)->user->getKey())->getKey();
            },
            'merchant_id' => function(SIOPENOrder $siopenOrder) {
                return Get::merchant($siopenOrder->merchant_id)->getKey();
            },
            'agency_id' => function(SIOPENOrder $siopenOrder) {
                return Get::agency($siopenOrder->agency->getKey())->getKey();
            },
        ]);

        $migrator
            ->uniques([
                'invoice',
            ])
            ->created(function(Order $order, SIOPENOrder $siopenOrder) {
                $this
                    ->handleOrderItems($order, $siopenOrder)
                    ->handleOrderDelivery($order, $siopenOrder)
                    ->handleOrderChatBargains($order, $siopenOrder)
                    ->handleOrderImplementations($order, $siopenOrder);
            });

        $migrator->migrate(Order::class, SIOPENOrder::class);

        $this
            ->assertSame(SIOPENOrder::class, Order::class)
            ->assertSame(SIOPENOrderItem::class, OrderItem::class)
            ->assertSame(OrderChatBargain::class, OrderBargain::class);
    }

    public function handleOrderItems(Order $order, SIOPENOrder $siopenOrder) : self
    {
        $siopenOrder->items->each(function(SIOPENOrderItem $siopenOrderItem) use ($order) {
            $migrator = $this->factory([
                'toko_daring' => function(SIOPENOrderItem $item) {
                    return $item->transaction_token ?? false;
                },
                'product_code' => function(SIOPENOrderItem $item) {
                    return $item->product->code;
                },
                'quantity'         => 'quantity',
                'product_price_id' => function(SIOPENOrderItem $item) {
                    return Get::price($item->price_id)->getKey();
                },
                'category_tax_id' => function(SIOPENOrderItem $item) {
                    if ($item->tax_category_id) {
                        return CategoryTax::whereOldId($item->tax_category_id)->withTrashed()->firstOrFail()->getKey();
                    }

                    return Get::tax($item->product->code)->getKey();
                },
                'category_id' => function(SIOPENOrderItem $item) {
                    return Get::category($item->product->category_id)->getKey();
                },
                'discount' => function(SIOPENOrderItem $item) {
                    if ($item->bargain) {
                        return $item->bargain->price;
                    }

                    return 0;
                },
            ]);

            $migrator
                ->uniques([
                    'product_code', 'invoice',
                ])
                ->create($siopenOrderItem, $order->items());
        });

        $order->update([
            'amount' => Cache::sear('order_amount' . $order->invoice, function() use ($order) {
                return $order->refresh()->items->sum('total');
            }),
        ]);

        return $this;
    }

    public function handleOrderChatBargains(Order $order, SIOPENOrder $siopenOrder) : self
    {
        $order->bargains->each(function(OrderItem $orderItem) {
            $orderItem->delete();
        });

        $siopenOrder->chats->each(function(OrderChatBargain $chatBargain) use ($order) {
            $migrator = $this->factory([
                'user_id' => function(OrderChatBargain $chatBargain) {
                    return Get::user($chatBargain->user_id)->getKey();
                },
                'message' => 'content',
            ]);

            $migrator->create($chatBargain, $order->bargains());
        });

        return $this;
    }

    public function handleOrderDelivery(Order $order, SIOPENOrder $siopenOrder) : self
    {
        $migrator = $this->factory([
            'fee'     => 'fee',
            'type'    => 'type',
            'address' => 'address',
            'name'    => function(OrderDelivery $siopenOrder) use ($order) {
                return $order->buyer->name;
            },
            'phone' => function() use ($order) {
                return $order->buyer->phone;
            },
        ]);

        $migrator->uniques([
            'invoice',
        ]);

        $migrator->create($siopenOrder->delivery, $order->delivery());

        return $this;
    }

    public function handleOrderImplementations(Order $order, SIOPENOrder $siopenOrder) : self
    {
        $migrator = $this->factory([
            'activity'     => 'activity',
            'necessity'    => 'necessities',
            'sub_activity' => 'sub_activity',
        ]);

        $migrator->uniques([
            'invoice',
        ]);

        $migrator->create($siopenOrder->activity, $order->implementation());

        return $this;
    }
}
