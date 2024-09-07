<?php

namespace App\Providers;

// use App\Parto\Client\PartoClient;

use App\Parto\Parto;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

class PartoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('parto', function(Application $app) {
            return new Parto($app['config']['services']['parto']);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
