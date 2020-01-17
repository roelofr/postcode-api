<?php

declare(strict_types=1);

namespace Roelofr\PostcodeApi\Services;

use Roelofr\PostcodeApi\Contracts\PostcodeApiContract;
use Roelofr\PostcodeApi\Exceptions\ApiException;
use Roelofr\PostcodeApi\Models\AddressInformation;

/**
 * Provides a quick and easy method to retrieve addresses
 *
 * @license MIT
 */
class PostcodeApiService implements PostcodeApiContract
{
    /**
     * Retireves information about the given postcode and number.
     * Failures are exceptions.
     *
     * @param string $postcode user-supplied postcode
     * @param string $number user-supplied number
     * @return Roelofr\PostcodeApi\Models\AddressInformation Seeded address info
     * @throws Roelofr\PostcodeApi\Exceptions\ApiException If the API gave an error
     */
    public function retrieve(string $postcode, string $number): AddressInformation
    {
        // TODO
        new \LogicException('Not yet implemented');
    }
}
