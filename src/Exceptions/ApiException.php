<?php

declare(strict_types=1);

namespace Roelofr\PostcodeApi\Exceptions;

use GuzzleHttp\Exception\ClientException;

/**
 * Any error from the API, extends Guzzle's exceptions for ease-of-use
 *
 * @license MIT
 */
class ApiException extends ClientException
{
    // nothing
}
