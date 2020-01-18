<?php

declare(strict_types=1);

namespace Tests\Traits;

use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

trait HasApiResponses
{
    /**
     * Builds a response
     * @param int $status
     * @param JsonSerializable|array|string $data
     * @return GuzzleHttp\Psr7\Response
     */
    protected function buildJsonResponse(int $status, $data): Response
    {
        // Change array to JSON
        if (!is_string($data)) {
            $data = \json_encode($data);
        }

        // Headers
        $headers = [
            // Assign date
            'Date' => Carbon::now()->toRfc822String(),

            // Not a valid format
            'Content-Type' => 'application/problem+json',

            // This is wrong, there IS content!
            'Content-Length' => \strlen($data),
            'Access-Control-Allow-Origin' => '*',

            // Add Amazon headers (aka: noise)
            'X-Amzn-RequestId' => (string) Str::uuid(),
            'X-Amz-Apigw-Id' => \base64_encode(\random_bytes(8)),
        ];

        // Return response
        return new Response($status, $headers, $data);
    }
}
