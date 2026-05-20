<?php

namespace App\Console\Commands\Elasticsearch;

use App\Support\Elasticsearch\ProductsIndex;
use Illuminate\Console\Command;

class CreateProductsIndex extends Command
{
    protected $signature = 'es:create-products-index
                            {--force : 若索引已存在则先删除再创建}
                            {--sync : 创建完成后执行 es:sync-products}';

    protected $description = '创建商品 Elasticsearch 索引（含 nested skus/properties 与 IK 分词）';

    public function handle(): int
    {
        $es = app('es');
        $index = ProductsIndex::NAME;

        if ($es->indices()->exists(['index' => $index])) {
            if (! $this->option('force')) {
                $this->error("索引 [{$index}] 已存在。使用 --force 删除并重建。");

                return self::FAILURE;
            }

            $this->warn("正在删除索引 [{$index}]...");
            $es->indices()->delete(['index' => $index]);
        }

        $this->info("正在创建索引 [{$index}]...");
        $es->indices()->create([
            'index' => $index,
            'body' => ProductsIndex::definition(),
        ]);

        $this->info('索引创建成功。');

        if ($this->option('sync')) {
            $this->call('es:sync-products');
        } else {
            $this->line('请执行: php artisan es:sync-products');
        }

        return self::SUCCESS;
    }
}
