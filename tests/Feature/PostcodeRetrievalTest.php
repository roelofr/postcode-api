<?php

namespace Tests\Feature;

use Exception;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use JsonSerializable;
use Roelofr\PostcodeApi\Contracts\PostcodeApiContract;
use Roelofr\PostcodeApi\Exceptions\ApiException;
use Roelofr\PostcodeApi\Exceptions\AuthenticationFailureException;
use Roelofr\PostcodeApi\Exceptions\NotFoundException;
use Roelofr\PostcodeApi\Models\AddressInformation;
use Tests\TestCase;
use Tests\Traits\HasApiResponses;
use Throwable;

class PostcodeRetrievalTest extends TestCase
{
    use HasApiResponses;

    /**
     * Tests postcodes that are valid
     * @param string $postcode
     * @param string $number
     * @param AddressInformation $address
     * @return void
     * @dataProvider getValidAddresses
     */
    public function testValidData(
        string $postcode,
        string $number,
        Response $response,
        AddressInformation $address
    ): void {
        // Get API
        $instance = $this->getService(new MockHandler([
            $response
        ]));

        // Get address from API
        $returnedAddress = $instance->retrieve($postcode, $number);

        // Check data
        $this->assertEquals($address, $returnedAddress);
    }

    /**
     * Tests invalid postodes
     * @param string $postcode
     * @param string $number
     * @param Exception $exception
     * @return void
     * @dataProvider getInvalidAddresses
     * @covers \Roelofr\PostcodeApi\Exceptions\NotFoundException
     */
    public function testInvalidData(
        string $postcode,
        string $number,
        Response $response,
        Exception $exception
    ): void {

        // Get API
        $instance = $this->getService(new MockHandler([
            $response
        ]));

        try {
            // Get address from API
            $instance->retrieve($postcode, $number);

            // Fail
            $this->fail(sprintf(
                'No exception was thrown. Expected %s: %s',
                get_class($exception),
                $exception->getMessage()
            ));
        } catch (RequestException $thrown) {
            // Check class
            $this->assertInstanceOf(get_class($exception), $thrown);

            // Check properties
            $this->assertEquals($exception->getCode(), $thrown->getCode());
            $this->assertEquals($exception->getMessage(), $thrown->getMessage());
        }
    }

    /**
     * Sets the API key to something invalid and tests that
     * @return void
     * @covers \Roelofr\PostcodeApi\Exceptions\AuthenticationFailureException
     */
    public function testInvalidApiKey()
    {
        // Set key to something invalid
        $this->app['config']->set('postcode-api.api-key', 'test-' . Str::uuid());

        // Build set
        $testSet = $this->createInvalidEntry(
            '1234AB',
            '1',
            $this->buildJsonResponse(401, ['title' => 'Invalid API key']),
            new AuthenticationFailureException(
                'The provided API key does not seem to work',
                new Request('GET', 'https://example.com')
            )
        );

        // Run test
        $this->testInvalidData(...$testSet);
    }

    /**
     * Returns a valid entry from the API
     * @param string $postcode
     * @param string $number
     * @param AddressInformation $address
     * @return array
     */
    protected function createValidEntry(string $postcode, string $number, AddressInformation $address): array
    {
        return [
            $postcode,
            $number,
            $this->buildJsonResponse(200, $address),
            $address
        ];
    }

    /**
     * Returns an invalid entry from the API, with exception
     * @param string $postcode
     * @param int $number
     * @param AddressInformation $address
     * @return array
     */
    protected function createInvalidEntry(
        string $postcode,
        int $number,
        Response $response,
        RequestException $exception
    ): array {
        // Handle exception
        $exceptionClass = get_class($exception);
        $exceptionMessage = $exception->getMessage();
        $exceptionRequest = $exception->getRequest();

        // Build params
        return [
            $postcode,
            $number,
            $response,
            new $exceptionClass($exceptionMessage, $exceptionRequest, $response)
        ];
    }

    /**
     * Valid entries
     * @return array
     */
    public function getValidAddresses(): array
    {

        return [
            $this->createValidEntry('6545CA', '29', new AddressInformation(
                '6545CA',
                29,
                'Waldeck Pyrmontsingel',
                'Nijmegen',
                'Nijmegen',
                'Gelderland'
            )),
            $this->createValidEntry('1021JT', '19', new AddressInformation(
                '1021JT',
                19,
                'Hamerstraat',
                'Amsterdam',
                'Amsterdam',
                'Noord-Holland'
            )),
            $this->createValidEntry('5038EA', '17', new AddressInformation(
                '5038EA',
                17,
                'Stationsstraat',
                'Tilburg',
                'Tilburg',
                'Noord-Brabant'
            )),
        ];
    }

    /**
     * Invalid entries
     * @return array
     */
    public function getInvalidAddresses(): array
    {
        // Dummy request
        $request = new Request('GET', 'https://example.com');

        return [
            $this->createInvalidEntry(
                '6545CA',
                299,
                $this->buildJsonResponse(404, ['title' => 'Resource not found']),
                new NotFoundException('The postcode/number combination was not found', $request)
            ),
        ];
    }
}
