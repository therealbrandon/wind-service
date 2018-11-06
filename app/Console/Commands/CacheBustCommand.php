<?php

namespace App\Console\Commands;

use App\Cache\WindCache;
use Exception;
use Illuminate\Console\Command;

class CacheBustCommand extends Command
{
    private $windCache;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cachebust {--zipcode= : The zipcode with the cache to bust}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Flush the cache for the Wind Service API call\nWith the --zipcode option set to a value, bust the cache for a specific zip code for the Wind Service API call";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(WindCache $windCache)
    {
        parent::__construct();
        $this->windCache = $windCache;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $zipCode = $this->getZipcode();

        if (!$zipCode) {
            $this->windCache->flushCache();
            $this->info("Cache flushed");
            return 0;
        }

        $this->windCache->bustCache($zipCode);
        $this->info("Cache busted for $zipCode");
        return 0;
    }

    private function getZipcode()
    {
        $zipCode = $this->option('zipcode');
        if ($zipCode && !preg_match('/\b\d{5}\b/', $zipCode)) {
            throw new Exception("Invalid zip code '$zipCode'");
        };

        return $zipCode;
    }
}
