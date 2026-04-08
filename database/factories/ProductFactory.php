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
        // 从数据库中随机取一个类目
        $category = \App\Models\Category::query()->where('is_directory', false)->inRandomOrder()->first();
        return [
            'title'        => $this->faker->word,
            'description'  => $this->faker->sentence,
            'image'        => $image,
            'on_sale'      => true,
            'rating'       => $this->faker->numberBetween(0, 5),
            'sold_count'   => 0,
            'review_count' => 0,
            'price'        => 0,
            // 将取出的类目 ID 赋给 category_id 字段
            // 如果数据库中没有类目则 $category 为 null，同样 category_id 也设成 null
            'category_id'  => $category ? $category->id : null,
        ];
    }
}