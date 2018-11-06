<?php

namespace App\Providers;

use App\Cache\WindCache;
use App\Interfaces\WindInterface;
use App\Services\WindService;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class WindServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(WindInterface::class, function ($app) {
            return new WindCache(new WindService(new Client()));
        });
    }
}
