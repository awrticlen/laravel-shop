<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        // 使用 Picsum 占位图（learnku CDN 已不稳定）
        $seed = $this->faker->numberBetween(1, 1000);
        $image = "https://picsum.photos/seed/{$seed}/400/400";

        return [
            'title'        => $this->faker->word,
            'description'  => $this->faker->sentence,
            'image'        => $image,
            'on_sale'      => true,
            'rating'       => $this->faker->numberBetween(0, 5),
            'sold_count'   => 0,
            'review_count' => 0,
            'price'        => 0,
        ];
    }
}