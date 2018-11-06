<?php

namespace Tests\Unit;

use App\Cache\WindCache;
use App\Services\WindService;
use Closure;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WindCacheTest extends TestCase
{
    public function test_retrieving_wind_remembers_result_in_cache()
    {
        Cache::shouldReceive('remember')
             ->once()
             ->with(md5(89101), 15, Closure::class)
             ->andReturn([]);

        $this->get('/wind/89101');
    }
}
