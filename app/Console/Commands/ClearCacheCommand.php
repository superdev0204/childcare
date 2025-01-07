<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:cache-clear';
    protected $description = 'Cache clear';

    const MAX_LINK_COUNT = 50000;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Cache::flush();

        $this->info('Cache has been cleaned');
    }
}