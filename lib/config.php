<?php

/**
 * Postcode API config
 */

return [
    /**
     * By default, we use the sandbox version. This prevents you from racking
     * up costs and helps with testing the application.
     */
    'use-sandbox' => env('POSTCODE_API_SANDBOX', true),

    /**
     * Specify your API key. It will be sent as-is using the `X-API-Key header
     */
    'api-key' => env('POSTCODE_API_KEY', null),

    /**
     * Should we use a frozen cache to prevent excess API calls?  Highly
     * recommended to be on, since duplicate calls may rack up significant
     * costs.
     */
    'use-cache' => env('POSTCODE_API_USE_CACHE', true),
];
