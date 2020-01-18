<?php

namespace Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Roelofr\PostcodeApi\Contracts\PostcodeApiContract;
use Roelofr\PostcodeApi\ServiceProvider;
use Roelofr\PostcodeApi\Services\PostcodeApiService;

abstract class TestCase extends BaseTestCase
{
    /**
     * Returns the Postcode API service
     * @return Roelofr\PostcodeApi\Contracts\PostcodeApiContract
     */
    protected function getService(MockHandler $handler = null): PostcodeApiContract
    {
        // Return default if no handler is set
        if ($handler) {
            // Configure handler
            $httpClient = new GuzzleClient([
                'hander' => HandlerStack::create($handler)
            ]);

            // Bind it
            $this->app->instance(GuzzleClient::class, $httpClient);
        }

        // Create service (optionally with handler)
        return $this->app->make(PostcodeApiService::class);
    }

    /**
     * Get package providers.
     * @param Application $app
     * @return string[]
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class
        ];
    }

    /**
     * Define environment setup.
     * @param Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app['config']->set('postcode-api', include dirname(__DIR__) . '/lib/config.php');
    }
}
