<?php

namespace App\Providers;

use App\Wit;
use Illuminate\Contracts\Container\Container;
use Jeylabs\Wit\Laravel\WitServiceProvider as BaseServiceProvider;

class WitServiceProvider extends BaseServiceProvider
{
    protected function registerBindings(Container $app)
    {
        $app->singleton('wit', function ($app) {
            return new Wit(
                $app['config']->get('wit.access_token', null),
                $app['config']->get('wit.async_requests', false)
            );
        });

        $app->alias('wit', Wit::class);
    }
}
