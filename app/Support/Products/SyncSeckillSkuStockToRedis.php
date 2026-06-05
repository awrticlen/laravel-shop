<?php

namespace App\Support\Products;

use App\Models\Product;
use App\Models\ProductSku;
use Illuminate\Support\Facades\Redis;

class SyncSeckillSkuStockToRedis
{
    public static function run(Product $product): void
    {
        $product->load(['seckill', 'skus']);

        if (! $product->seckill) {
            return;
        }

        $diff = $product->seckill->end_at->getTimestamp() - time();

        $product->skus->each(function (ProductSku $sku) use ($diff, $product) {
            if ($product->on_sale && $diff > 0) {
                Redis::setex('seckill_sku_'.$sku->id, $diff, $sku->stock);
            } else {
                Redis::del('seckill_sku_'.$sku->id);
            }
        });
    }
}
