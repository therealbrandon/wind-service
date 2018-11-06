<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Cache\WindCache;
use App\Interfaces\WindInterface;

class WindServiceWrapper implements WindInterface
{
    private $windCache;

    public function __construct(WindCache $windCache)
    {
        $this->windCache = $windCache;
    }

    public function getWindByZip($zipCode)
    {
        return $this->windCache->getWindByZip($zipCode);
    }
}
