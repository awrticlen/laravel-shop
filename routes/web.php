<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\UserAddressesController; // 新增这一行

// 无 symlink 时通过路由提供 storage 文件（避免 /storage 被 nginx 直接 403，改用 serve-storage）
Route::get('serve-storage/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . $path);
    if (! File::exists($fullPath) || ! File::isFile($fullPath)) {
        abort(404);
    }

    return response()->file($fullPath);
})->where('path', '.*')->name('storage.serve');

Route::get('/', [PagesController::class, 'root'])->name('root');
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
});