<?php

declare(strict_types=1);

namespace Roelofr\PostcodeApi\Exceptions;

use InvalidArgumentException;

/**
 * Errors caused by invalid postal codes being fed (before API call)
 *
 * @license MIT
 */
class MalformedDataException extends InvalidArgumentException
{
    public const ERR_INVALID_POSTCODE = 1;
    public const ERR_INVALID_NUMBER = 2;
}
