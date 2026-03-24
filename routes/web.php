<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\UserAddressesController; // 新增这一行
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PaymentController;
// 无 symlink 时通过路由提供 storage 文件（避免 /storage 被 nginx 直接 403，改用 serve-storage）
Route::get('serve-storage/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . $path);
    if (! File::exists($fullPath) || ! File::isFile($fullPath)) {
        abort(404);
    }

    return response()->file($fullPath);
})->where('path', '.*')->name('storage.serve');

Route::redirect('/', '/products')->name('root');
Route::get('products', [ProductsController::class, 'index'])->name('products.index');
Route::get('products/favorites', [ProductsController::class, 'favorites'])
    ->middleware(['auth', 'verified'])
    ->name('products.favorites');
Route::get('products/{product}', [ProductsController::class, 'show'])->name('products.show');
Auth::routes(['verify' => true]);

// auth 中间件代表需要登录，verified中间件代表需要经过邮箱验证
Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('user_addresses', [UserAddressesController::class, 'index'])
        ->name('user_addresses.index');
    Route::get('user_addresses/create', [UserAddressesController::class, 'create'])->name('user_addresses.create');
    Route::post('user_addresses', [UserAddressesController::class, 'store'])->name('user_addresses.store');
    Route::get('user_addresses/{user_address}', [UserAddressesController::class, 'edit'])
    ->name('user_addresses.edit');
    Route::put('user_addresses/{user_address}', [UserAddressesController::class, 'update'])->name('user_addresses.update');
    Route::delete('user_addresses/{user_address}', [UserAddressesController::class, 'destroy'])->name('user_addresses.destroy');
    Route::post('products/{product}/favorite', [ProductsController::class, 'favor'])->name('products.favor');
    Route::delete('products/{product}/favorite', [ProductsController::class, 'disfavor'])->name('products.disfavor');
    Route::post('cart', [CartController::class, 'add'])->name('cart.add');
    Route::get('cart', [CartController::class, 'index'])->name('cart.index');
    Route::delete('cart/{sku}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('orders', [OrdersController::class, 'store'])->name('orders.store');
    Route::get('orders', [OrdersController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrdersController::class, 'show'])->name('orders.show');
    Route::get('payment/{order}/alipay', [PaymentController::class, 'payByAlipay'])->name('payment.alipay');
   });
// 前端回调路由不放到 auth 组中，避免跨域名回跳时因未登录态被中间件拦截
Route::get('payment/alipay/return', [PaymentController::class, 'alipayReturn'])->name('payment.alipay.return');
   //服务器端回调的路由不能放到带有 auth 中间件的路由组中，因为支付宝的服务器请求不会带有认证信息。
   Route::post('payment/alipay/notify', [PaymentController::class, 'alipayNotify'])->name('payment.alipay.notify');
