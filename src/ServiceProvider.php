<?php

declare(strict_types=1);

namespace Roelofr\PostcodeApi;

use Roelofr\PostcodeApi\Contracts\PostcodeApiContract;
use Roelofr\PostcodeApi\Services\PostcodeApiService;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Provides a singleton Postcode API and registers config
 *
 * @license MIT
 */
class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PostcodeApiContract::class, PostcodeApiService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            dirname(__DIR__) . '/lib/config.php' => config_path('postcode-api.php'),
        ], 'config');
    }
}
