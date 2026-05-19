<?php

namespace App\Support\Products;

use App\Jobs\DeleteProductFromES;
use App\Models\Product;

class RemoveProductFromElasticsearch
{
    public static function run(Product|int $product): void
    {
        $productId = $product instanceof Product ? $product->getKey() : $product;

        if ($productId === null) {
            return;
        }

        dispatch(new DeleteProductFromES((int) $productId));
    }
}
