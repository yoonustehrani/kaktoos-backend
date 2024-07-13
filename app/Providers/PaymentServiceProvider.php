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
        Route::post('payment/{gateway}/verify', function(Request $request, string $gateway) {
            dd(
                $request->all()
            );
        })->name('payment.verify');
    }
}
