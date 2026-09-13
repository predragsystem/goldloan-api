<?php

namespace App\Providers;

use App\Contracts\SmsGateway;
use App\Services\LogSmsGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Swap LogSmsGateway for a real provider (e.g. Msg91SmsGateway) here
        // once one is chosen — nothing else in the app needs to change.
        $this->app->bind(SmsGateway::class, LogSmsGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
