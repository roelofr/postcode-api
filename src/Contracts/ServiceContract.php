<?php

declare(strict_types=1);

namespace Roelofr\PostcodeApi\Contracts;

use Roelofr\PostcodeApi\Models\AddressInformation;

/**
 * Calls the postcode Api
 *
 * @license MIT
 */
interface ServiceContract
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
    public function retrieve(string $postcode, string $number): AddressInformation;
}
