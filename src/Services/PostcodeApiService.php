<?php

declare(strict_types=1);

namespace Roelofr\PostcodeApi\Services;

use Roelofr\PostcodeApi\Contracts\PostcodeApiContract;
use Roelofr\PostcodeApi\Exceptions\ApiException;
use Roelofr\PostcodeApi\Exceptions\AuthenticationFailureException;
use Roelofr\PostcodeApi\Exceptions\MalformedDataException;
use Roelofr\PostcodeApi\Exceptions\NotFoundException;
use Roelofr\PostcodeApi\Models\AddressInformation;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request;
use Illuminate\Contracts\Cache\Store as CacheStoreContract;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Cache\TaggedCache;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Support\Str;
use InvalidArgumentException;
use JsonException;

/**
 * Provides a quick and easy method to retrieve addresses
 *
 * @license MIT
 */
class PostcodeApiService implements PostcodeApiContract
{
    private const URL_TESTING = 'https://sandbox.postcodeapi.nu/';
    private const URL_PRODUCTION = 'https://api.postcodeapi.nu/';
    private const LOOKUP_PATH = '/v3/lookup/%s/%s';
    private const CACHE_KEY = 'postcode.%s.%s';

    private const REGEX_POSTCODE = '/^\s*([1-9][0-9]{3})\s*([a-z]{2})\s*$/i';
    private const REGEX_NUMBER = '/^\s*([1-9][0-9]{0,4})/';

    /**
     * HTTP client with authentication
     */
    private GuzzleClient $client;

    /**
     * Cache to use, if enabled
     */
    private ?CacheStoreContract $cache;

    /**
     * Creates a new Postcode API client based on the provided config
     * @param ConfigContract $config Config to read from
     * @param CacheRepository $cache Cache to use
     */
    public function __construct(ConfigContract $config, CacheRepository $cache)
    {
        // Get config
        $apiKey = $config->get('postcode-api.api-key');
        $useSandbox = (bool) $config->get('postcode-api.use-sandbox', true);
        $useCache = (bool) $config->get('postcode-api.use-cache', true);

        // Determine URL, use testing API if no key is set (don't spam
        // production with test requests)
        $baseUrl = ($useSandbox || empty($apiKey)) ? self::URL_TESTING : self::URL_PRODUCTION;

        // Build headers
        $userAgent = sprintf(
            "Roelof's Postcode API client (+https://github.com/roelofr/postcode-api); cURL/%s; PHP/%s",
            \curl_version()['version'],
            \phpversion()
        );

        // Create client
        $this->client = new GuzzleClient([
            'http_errors' => false,
            'base_uri' => $baseUrl,
            'timeout' => 5,
            'headers' => [
                'X-API-Key' => $apiKey ?? 'test',
                'User-Agent' => $userAgent
            ]
        ]);

        // If enabled, enable the cache
        if ($useCache) {
            $this->cache = $cache->getStore();

            // Tag the cache if possible
            if ($this->cache instanceof TaggedCache) {
                $this->cache = $this->cache->tags(['postcode-api']);
            }
        }
    }

    /**
     * Formats a postcode for API consumption
     *
     * @param string $postcode User-supplied postcode
     * @return string Clean postcode
     * @throws MalformedDataException if the postcode cannot be processed
     */
    protected function formatPostcode(string $postcode): string
    {
        if (!preg_match(self::REGEX_POSTCODE, $postcode, $matches)) {
            throw new MalformedDataException('Postcode is invalid', MalformedDataException::ERR_INVALID_POSTCODE);
        }

        return Str::upper($matches[1] . $matches[2]);
    }

    /**
     * Formats a number for API consumption
     *
     * @param string $number User-supplied number
     * @return string Clean number
     * @throws MalformedDataException if the number cannot be processed
     */
    protected function formatNumber(string $number): string
    {
        if (!preg_match(self::REGEX_NUMBER, $number, $matches)) {
            throw new MalformedDataException('Number is invalid', MalformedDataException::ERR_INVALID_NUMBER);
        }

        return $matches[1];
    }

    /**
     * Retireves information about the given postcode and number. Failures are
     * exceptions.
     *
     * @param string $postcode user-supplied postcode
     * @param string $number user-supplied number
     * @return Roelofr\PostcodeApi\Models\AddressInformation Seeded address info
     * @throws Roelofr\PostcodeApi\Exceptions\ApiException If the API gave an error
     */
    public function retrieve(string $postcode, string $number): AddressInformation
    {
        // Format data
        $postcode = $this->formatPostcode($postcode);
        $number = $this->formatNumber($number);

        // Format cache key
        $cacheKey = sprintf(self::CACHE_KEY, $postcode, $number);

        // Check cache, if present
        if ($this->cache && $cached = $this->cache->get($cacheKey)) {
            return $cached;
        }

        // Perform API call
        $request = new Request(
            'GET',
            sprintf(self::LOOKUP_PATH, $postcode, $number),
            $this->client->getConfig('headers')
        );
        $response = $this->client->send($request);

        if ($response->getStatusCode() === 200) {
            try {
                // Decode JSON and convert to AddressInformation
                $json = json_decode($response->getBody()->getContents(), true, 2, \JSON_THROW_ON_ERROR);
                $data = AddressInformation::fromArray($json);
            } catch (JsonException $exception) {
                throw new ApiException('Failed to convert response to data', $request, $response, $exception);
            } catch (InvalidArgumentException $exception) {
                throw new ApiException('Failed to create AddressInformation model', $request, $response, $exception);
            }

            // Cache if required
            if ($this->cache) {
                $this->cache->forever($cacheKey, $data);
            }
        }

        $responseStatus = $response->getStatusCode();

        // Default message and type
        $message = "Request failed: {$response->getReasonPhrase()}";
        $type = ApiException::class;

        // Handle non-error conditions
        if ($responseStatus < 400) {
            $message = "Server gave unexpected response code: {$response->getReasonPhrase()}";
        }

        // Handle API key rejection
        if ($responseStatus === 401) {
            $message = 'The provided API key does not seem to work';
            $type = AuthenticationFailureException::class;
        }

        // Handle not found
        if ($responseStatus === 404) {
            $message = 'The postcode/number combination was not found';
            $type = NotFoundException::class;
        }

        // Sanity checks
        assert(is_a($type, ApiException::class, true), "To-throw exception is of a weird type");
        assert(!empty($message), "To-throw exception has no message");

        // Throw exception
        throw new $type($message, $request, $response);
    }
}
