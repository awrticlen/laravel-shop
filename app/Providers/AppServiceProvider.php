<?php

namespace App\Providers;

use App\Events\OrderPaid;
use App\Events\OrderReviewed;
use App\Listeners\SendOrderPaidMail;
use App\Listeners\UpdateProductRating;
use App\Listeners\UpdateProductSoldCount;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Yansongda\Pay\Pay;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register() {
        $config = config('pay');
        //判断当前项目运行环境是否为线上环境
        if (app()->environment() !== 'production') {
            $config['alipay']['default']['mode'] = $config['wechat']['default']['mode'] = Pay::MODE_SANDBOX;
            $config['logger']['level'] = 'debug';
        } else {
            $config['logger']['level'] = 'info';
        }

        // 往服务容器中注入一个名为 alipay 的单例对象
        $this->app->singleton('alipay', function() use ($config) {
            //调用Yansongda\Pay来创建一个支付宝支付对象
            $config = config('pay');
            $config['alipay']['default']['notify_url'] = ngrok_url('payment.alipay.notify');
            $config['alipay']['default']['return_url'] = ngrok_url('payment.alipay.return');

            return Pay::alipay($config);
        });

        $this->app->singleton('wechat_pay', function() use ($config) {
            // 调用 Yansongda\Pay 来创建一个微信支付对象
            return Pay::wechat($config);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 支付宝/微信等服务器回调不会携带 CSRF Token，需放入白名单
        VerifyCsrfToken::except([
            'payment/alipay/notify',
        ]);

        \Illuminate\Pagination\Paginator::useBootstrap();

        Event::listen(OrderPaid::class, UpdateProductSoldCount::class);
        Event::listen(OrderPaid::class, SendOrderPaidMail::class);
        Event::listen(OrderReviewed::class, UpdateProductRating::class);
        // 当 Laravel 渲染 products.index 和 products.show 模板时，就会使用 CategoryTreeComposer 这个来注入类目树变量
        // 同时 Laravel 还支持通配符，例如 products.* 即代表当渲染 products 目录下的模板时都执行这个 ViewComposer
        \View::composer(['products.index', 'products.show'], \App\Http\ViewComposers\CategoryTreeComposer::class);
    }
}
