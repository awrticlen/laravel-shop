<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Exceptions\InvalidRequestException;
use App\Models\OrderItem;
use App\Models\Category;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $order = (string) $request->input('order', '');

        // 创建一个查询构造器
        $builder = Product::query()->where('on_sale', true);
        $like = null;
        // search 参数用来模糊搜索商品（已 trim，避免首尾空格导致搜不到）
        if ($search !== '') {
            $like = '%'.$search.'%';
            // 模糊搜索商品标题、商品详情、SKU 标题、SKU描述
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }
        // 如果有传入 category_id 字段，并且在数据库中有对应的类目
        if ($request->input('category_id') && $category = Category::find($request->input('category_id'))) {
            // 如果这是一个父类目
            if ($category->is_directory) {
                // 则筛选出该父类目下所有子类目的商品
                $builder->whereHas('category', function ($query) use ($category) {
                    // 这里的逻辑参考本章第一节
                    $query->where('path', 'like', $category->path.$category->id.'-%');
                });
            } else {
                // 如果这不是一个父类目，则直接筛选此类目下的商品
                $builder->where('category_id', $category->id);
            }
        }
        // order 参数用来控制商品的排序规则
        $orderApplied = false;
        if ($order !== '' && preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
            if (in_array($m[1], ['price', 'sold_count', 'rating'], true)) {
                $builder->orderBy($m[1], $m[2]);
                $orderApplied = true;
            }
        }

        // 有搜索且用户未指定有效排序时，按与关键词的相关度排序（完全匹配标题优先）
        if ($search !== '' && ! $orderApplied && $like !== null) {
            $title = $builder->qualifyColumn('title');
            $description = $builder->qualifyColumn('description');
            $id = $builder->qualifyColumn('id');
            $builder->orderByRaw(
                'CASE
                    WHEN '.$title.' = ? THEN 0
                    WHEN '.$title.' LIKE ? THEN 1
                    WHEN '.$title.' LIKE ? THEN 2
                    WHEN '.$description.' LIKE ? THEN 3
                    ELSE 4
                END ASC, '.$id.' ASC',
                [$search, $search.'%', $like, $like]
            );
        }

        $products = $builder->paginate(16);

        return view('products.index', [
            'products' => $products,
            'filters'  => [
                'search' => $search,
                'order'  => $order,
            ],
            // 等价于 isset($category) ? $category : null
            'category' => $category ?? null,
        ]);
    }
    public function show(Product $product, Request $request)
    {
        if (!$product->on_sale) {
            throw new InvalidRequestException('商品未上架');
        }

        $favored = false;
        // 用户未登录时返回的是 null，已登录时返回的是对应的用户对象
        if($user = $request->user()) {
            // 从当前用户已收藏的商品中搜索 id 为当前商品 id 的商品
            // boolval() 函数用于把值转为布尔值
            $favored = boolval($user->favoriteProducts()->find($product->id));
        }

        $reviews = OrderItem::query()
            ->with(['order.user', 'productSku']) // 预先加载关联关系
            ->where('product_id', $product->id)
            ->whereNotNull('reviewed_at') // 筛选出已评价的
            ->orderBy('reviewed_at', 'desc') // 按评价时间倒序
            ->limit(10) // 取出 10 条
            ->get();

        // 最后别忘了注入到模板中
        return view('products.show', [
            'product' => $product,
            'favored' => $favored,
            'reviews' => $reviews
        ]);
    }
    public function favor(Product $product, Request $request)
    {
        $user = $request->user();
        if ($user->favoriteProducts()->find($product->id)) {
            return [];
        }

        $user->favoriteProducts()->attach($product);

        return [];
    }
    public function disfavor(Product $product, Request $request)
    {
        $user = $request->user();
        $user->favoriteProducts()->detach($product);

        return [];
    }
    public function favorites(Request $request)
    {
        $products = $request->user()->favoriteProducts()->paginate(16);

        return view('products.favorites', ['products' => $products]);
    }
}
