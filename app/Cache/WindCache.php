<?php

namespace App\Cache;

use App\Interfaces\WindInterface;
use Cache;

class WindCache implements WindInterface
{
    private $next;

    public function __construct(WindInterface $next)
    {
        $this->next = $next;
    }

    public function getWindByZip($zipCode)
    {
        $cacheKey = md5($zipCode);
        $minutes = 15;
        return Cache::remember($cacheKey, $minutes, function () use ($zipCode) {
            return $this->next->getWindByZip($zipCode);
        });
    }

    public function bustCache($zipCode)
    {
        $cacheKey = md5($zipCode);
        Cache::forget($cacheKey);
    }

    public function flushCache()
    {
        Cache::flush();
    }
}
