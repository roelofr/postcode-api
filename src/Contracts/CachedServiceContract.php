<?php

declare(strict_types=1);

namespace Roelofr\PostcodeApi\Contracts;

use Roelofr\PostcodeApi\Models\AddressInformation;

/**
 * Handles support for caches that can be cleared.
 *
 * @license MIT
 */
interface CachedServiceContract
{
    /**
     * Clears cached postcodes, causing all locally stored results to be re-retrieved.
     *
     * @throws Roelofr\PostcodeApi\Exceptions\CacheClearException If the cache can't be cleared
     */
    public function clearCache(): void;
}
