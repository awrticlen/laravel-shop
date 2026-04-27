<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Exceptions\CouponCodeUnavailableException;
use App\Exceptions\InvalidRequestException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'payment/alipay/notify',
            'installments/alipay/notify',
        ]);
    })->withExceptions(function (Exceptions $exceptions): void {
        // 用户触发的业务异常：不写入日志，避免刷屏
        $exceptions->dontReport([
            InvalidRequestException::class,
            CouponCodeUnavailableException::class,
        ]);
    })->create();
