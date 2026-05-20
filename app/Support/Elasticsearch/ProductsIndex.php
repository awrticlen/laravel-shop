<?php

namespace App\Support\Elasticsearch;

class ProductsIndex
{
    public const NAME = 'products';

    /**
     * 与 Product::toESArray()、ProductsController 搜索字段一致。
     */
    public static function definition(): array
    {
        $textField = [
            'type' => 'text',
            'analyzer' => 'ik_max_word',
            'search_analyzer' => 'ik_smart',
        ];

        return [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'analyzer' => [
                        'ik_smart' => [
                            'tokenizer' => 'ik_smart',
                        ],
                        'ik_max_word' => [
                            'tokenizer' => 'ik_max_word',
                        ],
                    ],
                ],
            ],
            'mappings' => [
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'type' => ['type' => 'keyword'],
                    'title' => $textField,
                    'long_title' => $textField,
                    'description' => $textField,
                    'category_id' => ['type' => 'integer'],
                    'category' => $textField,
                    'category_path' => ['type' => 'keyword'],
                    'on_sale' => ['type' => 'boolean'],
                    'rating' => ['type' => 'float'],
                    'sold_count' => ['type' => 'integer'],
                    'review_count' => ['type' => 'integer'],
                    'price' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                    'skus' => [
                        'type' => 'nested',
                        'properties' => [
                            'title' => ['type' => 'text', 'copy_to' => 'skus_title'],
                            'description' => ['type' => 'text', 'copy_to' => 'skus_description'],
                            'price' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                        ],
                    ],
                    'properties' => [
                        'type' => 'nested',
                        'properties' => [
                            // 属性名/值用于 terms 聚合与筛选，须为 keyword
                            'name' => ['type' => 'keyword'],
                            'value' => [
                                'type' => 'keyword',
                                'copy_to' => 'properties_value',
                            ],
                        ],
                    ],
                    'skus_title' => $textField,
                    'skus_description' => $textField,
                    'properties_value' => $textField,
                ],
            ],
        ];
    }
}
