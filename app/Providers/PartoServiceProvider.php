<?php

namespace App\Providers;

use App\Parto\PartoClient;
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
            return new PartoClient($app['config']['services']['parto']);
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
