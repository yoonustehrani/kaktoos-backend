<?php

namespace App\Providers;

use App\SMS\SMSService;
use Illuminate\Support\ServiceProvider;

class MelliPayamakServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('sms', function() {
            return SMSService::default();
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
