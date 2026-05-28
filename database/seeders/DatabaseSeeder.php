<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ShieldSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(UserAddressesSeeder::class);
        // 放在商品 Seeder 之前（DDRProductsSeeder 依赖「内存」类目）
        $this->call(CategoriesSeeder::class);
        $this->call(DDRProductsSeeder::class);
        $this->call(ProductsSeeder::class);
        $this->call(CouponCodesSeeder::class);
        $this->call(OrdersSeeder::class);
    }
}