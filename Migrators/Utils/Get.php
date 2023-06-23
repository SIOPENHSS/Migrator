<?php

/** @noinspection PhpIncompatibleReturnTypeInspection */

namespace App\Laravel\Migrators\Utils;

use App\Domain\Category\Models\CategoryTax;
use App\Domain\Product\Models\Product;
use App\Domain\Product\Models\ProductPrice;
use Illuminate\Support\Facades\Cache;
use SIOPEN\Migrator\Models\Agency;
use SIOPEN\Migrator\Models\Category;
use SIOPEN\Migrator\Models\Merchant;
use SIOPEN\Migrator\Models\Unit;
use SIOPEN\Migrator\Models\User;

class Get
{
    public static function price(int $id) : ProductPrice
    {
        return ProductPrice::where('old_id', $id)->withTrashed()->first();
    }

    public static function tax(string $code) : CategoryTax
    {
        return Product::whereCode($code)->first()->category->tax;
    }

    public static function user(int $id) : \App\Domain\User\Models\User
    {
        $email = Cache::sear('user:' . $id, function() use ($id) {
            return User::withTrashed()->find($id)->email;
        });

        return \App\Domain\User\Models\User::whereEmail($email)->withTrashed()->firstOrFail();
    }

    public static function agency(int $id) : \App\Domain\Agency\Models\Agency
    {
        $slug = Cache::sear('agency:' . $id, function() use ($id) {
            return Agency::withTrashed()->find($id)->slug;
        });

        return \App\Domain\Agency\Models\Agency::whereSlug($slug)->withTrashed()->firstOrFail();
    }

    public static function merchant(int $id) : \App\Domain\Merchant\Models\Merchant
    {
        $slug = Cache::sear('merchant:' . $id, function() use ($id) {
            return Merchant::withTrashed()->find($id)->slug;
        });

        return \App\Domain\Merchant\Models\Merchant::whereSlug($slug)->withTrashed()->firstOrFail();
    }

    public static function unit(int $id) : \App\Domain\Unit\Models\Unit
    {
        $symbol = Cache::sear('unit:' . $id, function() use ($id) {
            return Unit::find($id)->symbol;
        });

        return \App\Domain\Unit\Models\Unit::whereSymbol($symbol)->withTrashed()->firstOrFail();
    }

    public static function category(int $id) : \App\Domain\Category\Models\Category
    {
        $slug = Cache::sear('category:' . $id, function() use ($id) {
            return Category::withTrashed()->find($id)->slug;
        });

        return \App\Domain\Category\Models\Category::whereSlug($slug)->withTrashed()->firstOrFail();
    }
}
