<?php

namespace App\Support\Products;

use App\Jobs\SyncOneProductToES;
use App\Models\Product;

class SyncProductToElasticsearch
{
    public static function run(Product $product): void
    {
        dispatch(new SyncOneProductToES($product));
    }
}
