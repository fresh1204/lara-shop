<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use Yansongda\Pay\Pay;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 往服务容器中注入一个名为 alipay 的单例对象
        $this->app->singleton('alipay',function(){
            $config = config('pay.alipay');
            //$config['notify_url'] = route('payment.alipay.notify');  //代表服务器端回调地址
            $config['notify_url'] = 'http://requestbin.fullcontact.com/17u8c971';
            $config['return_url'] = route('payment.alipay.return');  //代表前端回调地址

            // 判断当前项目运行环境是否为线上环境
            if(app()->environment() !== 'production'){
                $config['mode']         = 'dev';
                $config['log']['level'] = Logger::DEBUG;
            }else{
                $config['log']['level'] = Logger::WARNING;
            }

            // 调用 Yansongda\Pay 来创建一个支付宝支付对象
            return Pay::alipay($config);
        });

        $this->app->singleton('wechat_pay',function(){
            $config = config('pay.wechat');
            if(app()->environment() !== 'production'){
                $config['log']['level'] = Logger::DEBUG;
            }else{
                $config['log']['level'] = Logger::WARNING;
            }

            // 调用 Yansongda\Pay 来创建一个微信支付对象
            return Pay::wechat($config);

        });
    }
}
