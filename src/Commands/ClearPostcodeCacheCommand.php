<?php

namespace Roelofr\PostcodeApi\Commands;

use App\Jobs\SendTo2Solar;
use App\Models\Lead;
use Illuminate\Console\Command;
use Roelofr\PostcodeApi\Exceptions\CacheClearException;
use Roelofr\PostcodeApi\Services\PostcodeApiService;

class ClearPostcodeCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postcode:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes cached postcodes';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(PostcodeApiService $service)
    {
        try {
            // Try to clear the cache
            $service->clearCache();

            // Pass if OK
            return true;
        } catch (CacheClearException $exception) {
            // Report error
            $this->line("Failed to clear cache: <info>{$exception->getMessage()}</>");

            // Fail
            return false;
        }
    }
}
