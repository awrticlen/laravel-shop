<?php

namespace App\Jobs;

use Elasticsearch\Common\Exceptions\Missing404Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteProductFromES implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $productId)
    {
    }

    public function handle(): void
    {
        try {
            app('es')->delete([
                'index' => 'products',
                'id' => $this->productId,
            ]);
        } catch (Missing404Exception) {
            // ES 中本无该文档，忽略
        }
    }
}
