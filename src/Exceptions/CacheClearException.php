<?php

declare(strict_types=1);

namespace Roelofr\PostcodeApi\Exceptions;

use GuzzleHttp\Exception\ClientException;
use RuntimeException;

/**
 * Indicates an issue with the cache. Usually called from the cache clearing system.
 *
 * @license MIT
 */
class CacheClearException extends RuntimeException
{
    public const ERR_BUCKSHOT = 1;
    public const ERR_DISABLED = 2;
}
