<?php

namespace App\Observers;

use App\Models\Product;
use App\Support\Products\RemoveProductFromElasticsearch;

class ProductObserver
{
    public function deleted(Product $product): void
    {
        RemoveProductFromElasticsearch::run($product);
    }
}
