<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\UserAddressesController; // 新增这一行

Route::get('/', [PagesController::class, 'root'])->name('root');
Auth::routes(['verify' => true]);

// auth 中间件代表需要登录，verified中间件代表需要经过邮箱验证
Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('user_addresses', [UserAddressesController::class, 'index'])
        ->name('user_addresses.index');
});