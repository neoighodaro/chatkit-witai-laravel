<?php

namespace App\Providers;

use Chatkit\Chatkit;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ChatKit::class, function () {
            return new Chatkit([
                'key' => config('services.chatkit.secret'),
                'instance_locator' => config('services.chatkit.locator'),
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
