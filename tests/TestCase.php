<?php

namespace Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Illuminate\Foundation\Application;
use JsonException;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Roelofr\PostcodeApi\Contracts\ServiceContract;
use Roelofr\PostcodeApi\ServiceProvider;
use Roelofr\PostcodeApi\Services\PostcodeApiService;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Returns the Postcode API service
     *
     * @return Roelofr\PostcodeApi\Contracts\PostcodeApiContract
     */
    protected function getService(MockHandler $handler = null): ServiceContract
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
     *
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
     *
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

    /**
     * Returns a JSON file from the lib/ folder
     *
     * @param string $name
     * @return array
     * @throws RuntimeException
     */
    protected function getLibraryFile(string $name): array
    {
        // Library path
        $filePath = dirname(__DIR__) . "/lib/tests/{$name}.json";

        // Error if not found
        if (!\file_exists($filePath)) {
            throw new RuntimeException("Cannot find lib/{$name}.json");
        }

        // Get conents
        $contents = \file_get_contents($filePath);

        try {
            // Return JSON contents
            return json_decode($contents, true, 512, \JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            // Error if damaged
            throw new RuntimeException("Failed to read JSON from lib/{$name}.json", 0, $e);
        }
    }
}
