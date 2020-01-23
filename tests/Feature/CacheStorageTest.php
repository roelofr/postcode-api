<?php

namespace Tests\Feature;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Roelofr\PostcodeApi\Contracts\CachedServiceContract;
use Roelofr\PostcodeApi\Exceptions\ApiException;
use Roelofr\PostcodeApi\Exceptions\CacheClearException;
use Tests\TestCase;
use Tests\Traits\HasApiResponses;

/**
 * Tests cache with a Redis cache
 *
 * @requires ext-redis
 */
class CacheStorageTest extends TestCase
{
    use HasApiResponses;

    private const TEST_ADDRESS = [
        'postcode' => '6545CA',
        'number' => 29,
        'street' => 'Waldeck Pyrmontsingel',
        'city' => 'Nijmegen',
        'municipality' => 'Nijmegen',
        'province' => 'Gelderland'
    ];

    private const TEST_ADDRESS_2 = [
        'postcode' => '6545CA',
        'number' => 29,
        'street' => 'Langestraat',
        'city' => 'Almere',
        'municipality' => 'Almere',
        'province' => 'Flevoland'
    ];

    private const TEST_POSTCODE = self::TEST_ADDRESS['postcode'];
    private const TEST_NUMBER = self::TEST_ADDRESS['number'];

    /**
     * Ensure cache is enabled
     *
     * @setUp
     */
    public function alwaysActivateCache(): void
    {
        // Make sure an app is present
        if (!$this->app) {
            $this->createApplication();
        }

        // Configure cache to be enabled
        $this->app->get('config')->set([
            'postcode-api.use-cache' => true,
        ]);
    }

    /**
     * Tests making a call to the API and expecting the second
     * try to work too.
     * @return void
     */
    public function testCache(): void
    {
        // Enable file-based cache
        $this->app->get('config')->set([
            'cache.default' => 'file'
        ]);

        // Flush cache
        $this->app->get(CacheRepository::class)->flush();

        // Get API
        $instance = $this->getService(new MockHandler([
            $this->buildJsonResponse(200, self::TEST_ADDRESS)
        ]));

        // Make request
        $response = $instance->retrieve(self::TEST_POSTCODE, self::TEST_NUMBER);

        // Quick sanity check
        $this->assertSame(self::TEST_ADDRESS, $response->toArray());

        // Run second request
        try {
            $response2 = $instance->retrieve(self::TEST_POSTCODE, self::TEST_NUMBER);
            $this->assertEquals($response, $response2);
        } catch (ApiException $e) {
            $this->fail('Service called API, which is scheduled to fail');
        }
    }
}
