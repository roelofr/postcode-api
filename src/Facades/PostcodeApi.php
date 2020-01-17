<?php

declare(strict_types=1);

namespace Roelofr\PostcodeApi\Facades;

use Roelofr\PostcodeApi\Contracts\PostcodeApiContract;
use Illuminate\Support\Facades\Facade;

/**
 * Forwards calls to the Postcode API
 *
 * @license MIT
 */
class PostcodeApi extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PostcodeApiContract::class;
    }
}
