<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WindServiceWrapper;

class WindController extends Controller
{
    private $windServiceWrapper;

    public function __construct(WindServiceWrapper $windServiceWrapper)
    {
        $this->windServiceWrapper = $windServiceWrapper;
    }

    public function getWindByZip($zipCode)
    {
        return $this->windServiceWrapper->getWindByZip($zipCode);
    }
}
