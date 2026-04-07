<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $attributes = [
        'price' => 0,
    ];

    protected $fillable = [
        'title', 'description', 'image', 'on_sale', 
        'rating', 'sold_count', 'review_count', 'price'
    ];
    protected $casts = [
    'on_sale' => 'boolean', // on_sale 是一个布尔类型的字段
    ];
    // 与商品SKU关联
    public function skus()
    {
    return $this->hasMany(ProductSku::class);
    }
    public function getImageUrlAttribute()
        {
            $image = $this->attributes['image'] ?? '';
            if ($image === '') {
                return '';
            }
            // 如果 image 字段本身就已经是完整的 url 就直接返回
            if (Str::startsWith($image, ['http://', 'https://'])) {
                return $image;
            }
            // 本地文件不存在时返回占位图，用商品 id 作 seed 保证每张图不同
            $fullPath = storage_path('app/public/' . $image);
            if (! File::exists($fullPath) || ! File::isFile($fullPath)) {
                $seed = $this->getKey() ?: crc32($image);
                return 'https://picsum.photos/seed/' . $seed . '/400/400';
            }
            // 无 symlink 时通过 serve-storage 路由提供文件（Vagrant 等环境）
            return route('storage.serve', ['path' => $image]);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
