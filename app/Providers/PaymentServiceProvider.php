<?php

namespace App\Providers;

use App\Payment\PaymentGateway;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        foreach (PaymentGateway::getGateways() as $gatewayClass) {
            $this->app->singleton($gatewayClass, function(Application $app) use($gatewayClass) {
                $gateway = new $gatewayClass;
                $gateway->config(
                    $app['config']['services']['payment'][$gateway->getGatewayName()]
                );
                return new PaymentGateway($gateway);
            });
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Route::get('payment/{gateway}/purchase', function($gateway) {
            /**
             * @var \App\Payment\PaymentGateway
             */
            $purchase = app()->make(PaymentGateway::getGatewayClassname($gateway));
            $purchase->gateway->setRequestItem('description', 'پرداخت برای تست');
            $purchase->init(amount: 4000);
            if ($purchase->requestPurchase()) {
                return $purchase->redirect();
            }
        })->name('payment.purchase');
        Route::post('payment/{gateway}/verify', function(Request $request, string $gateway) {
            dd(
                $request->all()
            );
        })->name('payment.verify');
    }
}
