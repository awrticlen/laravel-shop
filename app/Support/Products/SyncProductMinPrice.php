<?php

namespace App\Support\Products;

use App\Models\Product;

class SyncProductMinPrice
{
    public static function run(Product $product): void
    {
        $product->refresh();
        $minPrice = $product->skus->isEmpty() ? 0 : (float) $product->skus->min('price');
        $product->updateQuietly(['price' => $minPrice]);
    }
}

